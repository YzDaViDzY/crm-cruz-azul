<?php

namespace App\Http\Controllers;

use App\Models\Arrendatario;
use App\Models\HistorialCobro;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ArrendatarioController extends Controller
{
    public function index(Request $request): View
    {
        $query = Arrendatario::with('user');
        $search = trim((string) $request->query('q'));

        if ($search !== '') {
            $query->where(function ($builder) use ($search) {
                $builder->where('nombres', 'like', "%{$search}%")
                    ->orWhere('apellidos', 'like', "%{$search}%")
                    ->orWhere('ci', 'like', "%{$search}%")
                    ->orWhere('numero_departamento', 'like', "%{$search}%")
                    ->orWhereHas('user', fn ($user) => $user->where('email', 'like', "%{$search}%"));
            });
        }

        if ($request->query('deuda') === 'si') {
            $query->where('deuda_actual', '>', 0);
        } elseif ($request->query('deuda') === 'no') {
            $query->where('deuda_actual', '<=', 0);
        }

        if (in_array($request->query('estado'), ['activo', 'inactivo'], true)) {
            $query->where('estado', $request->query('estado'));
        }

        return view('admin.arrendatarios.index', [
            'tenants' => $query->orderBy('apellidos')->paginate(15)->withQueryString(),
            'search' => $search,
        ]);
    }

    public function create(): View
    {
        return view('admin.arrendatarios.create');
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'ci' => ['required', 'string', 'max:50', 'unique:arrendatarios,ci'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'numero_departamento' => ['required', 'string', 'max:50', 'unique:arrendatarios,numero_departamento'],
            'fecha_ingreso' => ['nullable', 'date'],
            'deuda_inicial' => ['required', 'numeric', 'min:0', 'max:9999999999.99'],
        ]);

        $tenant = DB::transaction(function () use ($data) {
            $user = User::create([
                'name' => trim($data['nombres'].' '.$data['apellidos']),
                'email' => $data['email'],
                'password' => Hash::make($data['password']),
                'role' => 'arrendatario',
            ]);

            $tenant = Arrendatario::create([
                'user_id' => $user->id,
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'ci' => $data['ci'],
                'telefono' => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'numero_departamento' => $data['numero_departamento'],
                'deuda_actual' => $data['deuda_inicial'],
                'estado' => 'activo',
                'fecha_ingreso' => $data['fecha_ingreso'] ?? null,
            ]);

            if ((float) $data['deuda_inicial'] > 0) {
                HistorialCobro::create([
                    'arrendatario_id' => $tenant->id,
                    'monto' => $data['deuda_inicial'],
                    'concepto' => 'Deuda inicial',
                    'fecha' => now(),
                    'tipo' => 'cargo',
                    'descripcion' => 'Saldo inicial del arrendatario.',
                ]);
            }

            return $tenant;
        });

        return redirect()->route('admin.arrendatarios.show', $tenant)
            ->with('success', 'Arrendatario creado correctamente.');
    }

    public function show(Arrendatario $arrendatario): View
    {
        $arrendatario->load([
            'user',
            'facturas.servicio',
            'pagos.usuarioRegistrador',
            'historialCobros',
        ]);

        return view('admin.arrendatarios.show', ['tenant' => $arrendatario]);
    }

    public function edit(Arrendatario $arrendatario): View
    {
        return view('admin.arrendatarios.edit', [
            'tenant' => $arrendatario->load('user'),
        ]);
    }

    public function update(Request $request, Arrendatario $arrendatario): RedirectResponse
    {
        $data = $request->validate([
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($arrendatario->user_id)],
            'password' => ['nullable', 'string', 'min:8', 'confirmed'],
            'ci' => ['required', 'string', 'max:50', Rule::unique('arrendatarios', 'ci')->ignore($arrendatario->id)],
            'telefono' => ['nullable', 'string', 'max:30'],
            'direccion' => ['nullable', 'string', 'max:255'],
            'numero_departamento' => ['required', 'string', 'max:50', Rule::unique('arrendatarios', 'numero_departamento')->ignore($arrendatario->id)],
            'fecha_ingreso' => ['nullable', 'date'],
            'estado' => ['required', Rule::in(['activo', 'inactivo'])],
        ]);

        DB::transaction(function () use ($data, $arrendatario) {
            $arrendatario->update([
                'nombres' => $data['nombres'],
                'apellidos' => $data['apellidos'],
                'ci' => $data['ci'],
                'telefono' => $data['telefono'] ?? null,
                'direccion' => $data['direccion'] ?? null,
                'numero_departamento' => $data['numero_departamento'],
                'fecha_ingreso' => $data['fecha_ingreso'] ?? null,
                'estado' => $data['estado'],
            ]);

            $userData = [
                'name' => trim($data['nombres'].' '.$data['apellidos']),
                'email' => $data['email'],
            ];
            if (!empty($data['password'])) {
                $userData['password'] = Hash::make($data['password']);
            }
            $arrendatario->user()->update($userData);
        });

        return redirect()->route('admin.arrendatarios.show', $arrendatario)
            ->with('success', 'Información actualizada correctamente.');
    }

    public function destroy(Arrendatario $arrendatario): RedirectResponse
    {
        if (
            $arrendatario->pagos()->exists()
            || $arrendatario->facturas()->exists()
            || $arrendatario->historialCobros()->exists()
        ) {
            return back()->withErrors([
                'delete' => 'No se puede eliminar un arrendatario con pagos, facturas o historial registrados; desactívalo para conservar su información.',
            ]);
        }

        $arrendatario->user()->delete();

        return redirect()->route('admin.arrendatarios.index')
            ->with('success', 'Arrendatario eliminado.');
    }
}
