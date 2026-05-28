@extends('layouts.app')

@section('title', 'Clientes')
@section('page-icon')<i class="bi bi-people-fill text-primary"></i>@endsection
@section('subtitle', 'Gestión · ' . $totalItems . ' clientes')

@section('topbar-actions')
    <div class="search-bar d-none d-md-flex">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Buscar cliente...">
    </div>
    <button class="btn btn-primary" onclick="abrirNuevoCliente()">
        <i class="bi bi-person-plus-fill"></i>
        <span>Nuevo Cliente</span>
    </button>
@endsection

@section('content')
<div class="d-md-none mb-3">
    <div class="search-bar w-100">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInputMobile" placeholder="Buscar cliente..." style="width:100%">
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table id="mainTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Tipo Doc.</th>
                    <th>N° Documento</th>
                    <th>Nombre / Razón Social</th>
                    <th>Teléfono</th>
                    <th>Correo Electrónico</th>
                    <th>Estado</th>
                    <th style="text-align:center">Ventas</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($clientes as $c)
                <tr>
                    <td><strong>#{{ $c->id }}</strong></td>
                    <td>
                        <span class="badge-status
                            {{ $c->tipo_documento === 'DNI' ? 'badge-vigente' : ($c->tipo_documento === 'RUC' ? 'badge-reclamada' : '') }}"
                            style="{{ $c->tipo_documento === 'VARIOS' ? 'background:rgba(100,116,139,0.1);color:#475569;' : '' }}">
                            {{ $c->tipo_documento }}
                        </span>
                    </td>
                    <td>{{ $c->numero_documento ?? '—' }}</td>
                    <td><strong>{{ $c->nombre_razon_social ?? 'Cliente General' }}</strong></td>
                    <td style="font-size:12px;color:var(--text-muted)">
                        {{ $c->telefono ? implode(' ', str_split($c->telefono, 3)) : '—' }}
                    </td>
                    <td style="font-size:12px;color:var(--text-muted)">{{ $c->email ?? '—' }}</td>
                    <td>
                        <span class="badge-status {{ $c->estado === 'Activo' ? 'badge-vigente' : 'badge-anulada' }}">
                            {{ $c->estado }}
                        </span>
                    </td>
                    <td style="text-align:center">
                        <span class="badge-status badge-vigente">{{ $c->ventas_count }}</span>
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action edit" title="Editar"
                                    onclick="editarCliente({{ $c->id }}, '{{ $c->tipo_documento }}', '{{ addslashes($c->numero_documento ?? '') }}', '{{ addslashes($c->nombre_razon_social ?? '') }}', '{{ $c->telefono ?? '' }}', '{{ addslashes($c->email ?? '') }}', '{{ $c->estado }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('clientes.destroy', $c) }}" class="d-inline" id="delCli{{ $c->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-action delete" title="Eliminar"
                                        onclick="confirmDelete(document.getElementById('delCli{{ $c->id }}'), '{{ addslashes($c->nombre_razon_social ?? 'este cliente') }}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </tr>
                </tr>
                @empty
                <tr>
                    <td colspan="9" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <i class="bi bi-people" style="font-size:32px;display:block;margin-bottom:10px"></i>
                        Sin clientes registrados
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($clientes->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border);padding:12px 20px">
        {{ $clientes->links() }}
    </div>
    @endif
</div>

{{-- Modal Crear / Editar --}}
<div class="sigvi-modal-overlay" id="modalCliente" data-close-fn="cerrarModalCliente">
    <div class="sigvi-modal">
        <div class="sigvi-modal-header">
            <h4 id="modalClienteTitulo"><i class="bi bi-person-plus text-primary"></i> Nuevo Cliente</h4>
            <button class="sigvi-modal-close" onclick="cerrarModalCliente()">&times;</button>
        </div>
        <form method="POST" id="formCliente" action="{{ route('clientes.store') }}" novalidate>
            @csrf
            <input type="hidden" name="_method" id="metodoCliente" value="POST">
            <div class="sigvi-modal-body">
                <div class="row g-3">
                    <div class="col-sm-4">
                        <div class="form-group mb-0">
                            <label class="form-label">Tipo Documento <span class="req">*</span></label>
                            <select name="tipo_documento" id="cliTipoDoc" class="form-select @error('tipo_documento') is-invalid @enderror" required>
                                <option value="">-- Seleccionar --</option>
                                <option value="DNI">DNI</option>
                                <option value="RUC">RUC</option>
                                <option value="VARIOS">VARIOS</option>
                            </select>
                            @error('tipo_documento') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-sm-8">
                        <div class="form-group mb-0">
                            <label class="form-label">N° Documento <span id="reqNumDoc" class="req" style="display:none;">*</span></label>
                            <input type="text" name="numero_documento" id="cliNumDoc"
                                   class="form-control @error('numero_documento') is-invalid @enderror"
                                   maxlength="15" placeholder="Primero selecciona el tipo de documento"
                                   inputmode="numeric" disabled>
                            @error('numero_documento') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label class="form-label">Nombre / Razón Social <span id="reqNombre" class="req" style="display:none;">*</span></label>
                            <input type="text" name="nombre_razon_social" id="cliNombre"
                                   class="form-control @error('nombre_razon_social') is-invalid @enderror"
                                   maxlength="255" placeholder="Nombre del cliente"
                                   value="{{ old('nombre_razon_social') }}">
                            @error('nombre_razon_social') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="form-label">Teléfono <span id="reqTelefono" class="req" style="display:none;">*</span></label>
                            <input type="text" name="telefono" id="cliTelefono"
                                   class="form-control @error('telefono') is-invalid @enderror"
                                   maxlength="11" placeholder="9 dígitos" inputmode="numeric"
                                   value="{{ old('telefono') }}">
                            @error('telefono') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group mb-0">
                            <label class="form-label">Correo Electrónico</label>
                            <input type="email" name="email" id="cliEmail"
                                   class="form-control @error('email') is-invalid @enderror"
                                   maxlength="255" placeholder="cliente@ejemplo.com"
                                   value="{{ old('email') }}">
                            @error('email') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    {{-- Campo Estado (oculto en creación, visible en edición) --}}
                    <div class="col-12" id="estadoContainer" style="display: none;">
                        <div class="form-group mb-0">
                            <label class="form-label">Estado <span class="req">*</span></label>
                            <select name="estado" id="cliEstado" class="form-select @error('estado') is-invalid @enderror">
                                <option value="Activo">Activo</option>
                                <option value="Inactivo">Inactivo</option>
                            </select>
                            @error('estado') <span class="invalid-feedback d-block">{{ $message }}</span> @enderror
                        </div>
                    </div>
                </div>
            </div>
            <div class="sigvi-modal-footer">
                <button type="button" class="btn btn-outline" onclick="cerrarModalCliente()">Cancelar</button>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-circle"></i> <span id="btnCliLabel">Guardar Cliente</span>
                </button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
// ================================
// 1. Elementos del DOM
// ================================
const tipoSelect = document.getElementById('cliTipoDoc');
const numDoc = document.getElementById('cliNumDoc');
const cliNombre = document.getElementById('cliNombre');
const cliTelefono = document.getElementById('cliTelefono');
const cliEmail = document.getElementById('cliEmail');
const reqNum = document.getElementById('reqNumDoc');
const reqNombre = document.getElementById('reqNombre');
const reqTelefono = document.getElementById('reqTelefono');

// ================================
// 2. Funciones de control de campos
// ================================

// Limpia y ajusta el campo número de documento según el tipo seleccionado
function ajustarNumeroDocumento() {
    const tipo = tipoSelect.value;
    let rawValue = numDoc.value.replace(/\D/g, '');

    if (tipo === 'DNI') {
        if (rawValue.length > 8) rawValue = rawValue.slice(0, 8);
        numDoc.value = rawValue;
        numDoc.maxLength = 8;
        numDoc.pattern = '[0-9]{8}';
        numDoc.placeholder = '8 dígitos (obligatorio)';
    } else if (tipo === 'RUC') {
        if (rawValue.length > 11) rawValue = rawValue.slice(0, 11);
        numDoc.value = rawValue;
        numDoc.maxLength = 11;
        numDoc.pattern = '[0-9]{11}';
        numDoc.placeholder = '11 dígitos (obligatorio)';
    } else if (tipo === 'VARIOS') {
        numDoc.removeAttribute('pattern');
        numDoc.maxLength = 15;
        numDoc.placeholder = 'Opcional (ej. GEN-001)';
        if (rawValue.length > 15) rawValue = rawValue.slice(0, 15);
        numDoc.value = rawValue;
    } else {
        numDoc.disabled = true;
        numDoc.value = '';
        numDoc.maxLength = 15;
        numDoc.removeAttribute('pattern');
        numDoc.placeholder = 'Primero selecciona el tipo de documento';
        return;
    }
    numDoc.disabled = false;
}

// Actualiza campos requeridos y asteriscos según el tipo de documento
function actualizarCampos() {
    const tipo = tipoSelect.value;

    numDoc.removeAttribute('required');
    cliNombre.removeAttribute('required');
    cliTelefono.removeAttribute('required');
    reqNum.style.display = 'none';
    reqNombre.style.display = 'none';
    reqTelefono.style.display = 'none';
    numDoc.disabled = true;

    if (tipo === 'DNI') {
        numDoc.setAttribute('required', '');
        cliNombre.setAttribute('required', '');
        cliTelefono.setAttribute('required', '');
        reqNum.style.display = 'inline';
        reqNombre.style.display = 'inline';
        reqTelefono.style.display = 'inline';
        ajustarNumeroDocumento();
    } else if (tipo === 'RUC') {
        numDoc.setAttribute('required', '');
        cliNombre.setAttribute('required', '');
        cliTelefono.setAttribute('required', '');
        reqNum.style.display = 'inline';
        reqNombre.style.display = 'inline';
        reqTelefono.style.display = 'inline';
        ajustarNumeroDocumento();
    } else if (tipo === 'VARIOS') {
        reqNum.style.display = 'none';
        ajustarNumeroDocumento();
    } else {
        numDoc.disabled = true;
        numDoc.value = '';
    }
}

// Formatea el teléfono con espacios cada 3 dígitos (solo visual)
function formatPhoneNumber(value) {
    let cleaned = value.replace(/\D/g, '');
    if (cleaned.length > 9) cleaned = cleaned.slice(0, 9);
    return cleaned.replace(/(\d{3})(?=\d)/g, '$1 ');
}

// Evento para formatear el teléfono mientras se escribe
cliTelefono.addEventListener('input', function(e) {
    let rawValue = e.target.value.replace(/\s/g, '');
    let formatted = formatPhoneNumber(rawValue);
    e.target.value = formatted;
});

// Limpia errores de un campo específico al escribir
function clearFieldError(fieldId) {
    const field = document.getElementById(fieldId);
    if (field) {
        field.classList.remove('is-invalid');
        const feedback = field.parentElement.querySelector('.invalid-feedback');
        if (feedback) feedback.textContent = '';
        const nextFeedback = field.nextElementSibling;
        if (nextFeedback && nextFeedback.classList.contains('invalid-feedback')) {
            nextFeedback.textContent = '';
        }
    }
}

// Asignar limpieza de errores a todos los campos del formulario
const formFields = ['cliTipoDoc', 'cliNumDoc', 'cliNombre', 'cliTelefono', 'cliEmail', 'cliEstado'];
formFields.forEach(fieldId => {
    const field = document.getElementById(fieldId);
    if (field) {
        field.addEventListener('input', () => clearFieldError(fieldId));
        field.addEventListener('change', () => clearFieldError(fieldId));
    }
});

// Evento específico para el número de documento
numDoc.addEventListener('input', function(e) {
    if (tipoSelect.value === 'VARIOS') {
        let raw = this.value;
        if (raw.length > 15) raw = raw.slice(0, 15);
        this.value = raw;
    } else {
        this.value = this.value.replace(/[^0-9]/g, '');
        ajustarNumeroDocumento();
    }
});

// Cuando cambia el tipo de documento, actualizar todo
tipoSelect.addEventListener('change', () => {
    actualizarCampos();
    clearFieldError('cliNumDoc');
});

// ================================
// 3. Antes de enviar, limpiar espacios del teléfono
// ================================
document.getElementById('formCliente').addEventListener('submit', function() {
    let phoneField = document.getElementById('cliTelefono');
    if (phoneField) {
        phoneField.value = phoneField.value.replace(/\s/g, '');
    }
});

// ================================
// 4. Apertura de nuevo cliente (resetea completamente)
// ================================
function abrirNuevoCliente() {
    document.getElementById('formCliente').reset();
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    tipoSelect.value = '';
    numDoc.disabled = true;
    numDoc.value = '';
    cliNombre.value = '';
    cliTelefono.value = '';
    cliEmail.value = '';
    document.getElementById('estadoContainer').style.display = 'none';
    actualizarCampos();
    openModal('modalCliente');
}

// ================================
// 5. Editar cliente (carga datos existentes)
// ================================
function editarCliente(id, tipo, numDocVal, nombre, telefono, email, estado) {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');

    document.getElementById('modalClienteTitulo').innerHTML = '<i class="bi bi-pencil text-primary"></i> Editar Cliente';
    document.getElementById('btnCliLabel').textContent = 'Actualizar Cliente';
    document.getElementById('formCliente').action = `/clientes/${id}`;
    document.getElementById('metodoCliente').value = 'PUT';

    tipoSelect.value = tipo;
    numDoc.value = numDocVal;
    cliNombre.value = nombre;
    cliTelefono.value = telefono ? formatPhoneNumber(telefono) : '';
    cliEmail.value = email;
    document.getElementById('cliEstado').value = estado;
    document.getElementById('estadoContainer').style.display = 'block';

    actualizarCampos();
    openModal('modalCliente');
}

// ================================
// 6. Cerrar modal y resetear todo
// ================================
function cerrarModalCliente() {
    document.getElementById('formCliente').reset();
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.textContent = '');
    tipoSelect.value = '';
    numDoc.disabled = true;
    numDoc.value = '';
    cliNombre.value = '';
    cliTelefono.value = '';
    cliEmail.value = '';
    document.getElementById('estadoContainer').style.display = 'none';
    actualizarCampos();
    closeModal('modalCliente');
}

// ================================
// 7. Si hay errores de validación del servidor, abrir modal con valores antiguos
// ================================
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        @php
            $old = session()->getOldInput();
        @endphp
        document.getElementById('cliTipoDoc').value = '{{ old('tipo_documento') }}';
        document.getElementById('cliNumDoc').value = '{{ old('numero_documento') }}';
        document.getElementById('cliNombre').value = '{{ old('nombre_razon_social') }}';
        let rawTelefono = '{{ old('telefono') ? addslashes(old('telefono')) : '' }}';
        document.getElementById('cliTelefono').value = rawTelefono ? formatPhoneNumber(rawTelefono) : '';
        document.getElementById('cliEmail').value = '{{ old('email') }}';
        if ('{{ old('estado') }}') {
            document.getElementById('cliEstado').value = '{{ old('estado') }}';
            document.getElementById('estadoContainer').style.display = 'block';
        }
        actualizarCampos();
        if (tipoSelect.value) {
            ajustarNumeroDocumento();
        }
        openModal('modalCliente');
    });
@endif
</script>
@endpush
