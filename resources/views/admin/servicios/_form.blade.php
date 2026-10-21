<div class="form-grid">
    <label>Nombre del servicio<input name="nombre" value="{{ old('nombre', $service->nombre) }}" required maxlength="100"></label>
    <label>Monto base (Bs)<input type="number" name="monto_base" min="0" step="0.01" value="{{ old('monto_base', $service->monto_base ?? '0.00') }}" required></label>
    <label class="full-field">Descripción<textarea name="descripcion" rows="4">{{ old('descripcion', $service->descripcion) }}</textarea></label>
    <label class="checkbox-label"><input type="checkbox" name="activo" value="1" @checked(old('activo', $service->exists ? $service->activo : true))> Servicio activo</label>
</div><div class="form-actions"><a class="button button-secondary" href="{{ route('admin.servicios.index') }}">Cancelar</a><button class="button button-primary" type="submit">Guardar servicio</button></div>
