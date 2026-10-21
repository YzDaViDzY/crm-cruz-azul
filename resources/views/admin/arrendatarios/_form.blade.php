<div class="form-grid">
    <label>Nombres<input name="nombres" value="{{ old('nombres', $tenant->nombres ?? '') }}" required maxlength="100"></label>
    <label>Apellidos<input name="apellidos" value="{{ old('apellidos', $tenant->apellidos ?? '') }}" required maxlength="100"></label>
    <label>Correo electrónico<input type="email" name="email" value="{{ old('email', $tenant->user->email ?? '') }}" required></label>
    <label>CI<input name="ci" value="{{ old('ci', $tenant->ci ?? '') }}" required></label>
    <label>Teléfono<input name="telefono" value="{{ old('telefono', $tenant->telefono ?? '') }}"></label>
    <label>Departamento<input name="numero_departamento" value="{{ old('numero_departamento', $tenant->numero_departamento ?? '') }}" required></label>
    <label class="full-field">Dirección<input name="direccion" value="{{ old('direccion', $tenant->direccion ?? '') }}"></label>
    <label>Fecha de ingreso<input type="date" name="fecha_ingreso" value="{{ old('fecha_ingreso', isset($tenant) && $tenant->fecha_ingreso ? $tenant->fecha_ingreso->format('Y-m-d') : '') }}"></label>
    @if(isset($tenant))
        <label>Estado<select name="estado" required><option value="activo" @selected(old('estado', $tenant->estado) === 'activo')>Activo</option><option value="inactivo" @selected(old('estado', $tenant->estado) === 'inactivo')>Inactivo</option></select></label>
        <label>Nueva contraseña <small>(opcional)</small><input type="password" name="password" autocomplete="new-password"></label>
        <label>Confirmar nueva contraseña<input type="password" name="password_confirmation" autocomplete="new-password"></label>
    @else
        <label>Deuda inicial (Bs)<input type="number" name="deuda_inicial" min="0" step="0.01" value="{{ old('deuda_inicial', '0.00') }}" required></label>
        <label>Contraseña inicial<input type="password" name="password" minlength="8" required autocomplete="new-password"></label>
        <label>Confirmar contraseña<input type="password" name="password_confirmation" minlength="8" required autocomplete="new-password"></label>
    @endif
</div>
<div class="form-actions"><a class="button button-secondary" href="{{ route('admin.arrendatarios.index') }}">Cancelar</a><button class="button button-primary" type="submit">Guardar arrendatario</button></div>
