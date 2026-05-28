@extends('layouts.app')

@section('title', 'Proveedores')
@section('page-icon')<i class="bi bi-truck-front-fill text-primary"></i>@endsection
@section('subtitle', 'Gestión de proveedores')

@section('topbar-actions')
    <div class="search-bar d-none d-md-flex">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Buscar proveedor...">
    </div>
    <button class="btn btn-primary" onclick="openModal('modalProveedor')">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Nuevo Proveedor</span>
    </button>
@endsection

@section('content')

<div class="card">
    <div class="table-wrapper">
        <table id="mainTable">
            <thead>
                <tr>
                    <th>#</th><th>RUC</th><th>Nombre Comercial</th><th>Contacto</th><th>Teléfono</th><th style="text-align:center">Compras</th><th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($proveedores as $p)
                <tr>
                    <td><strong>#{{ $p->id }}</strong></td>
                    <td><code style="background:#f1f5f9;padding:2px 7px;border-radius:5px;font-size:12px">{{ $p->ruc }}</code></td>
                    <td><strong>{{ $p->nombre_comercial }}</strong></td>
                    <td style="color:var(--text-muted)">{{ $p->contacto_nombre ?? '—' }}</td>
                    <td style="color:var(--text-muted)">{{ $p->telefono ?? '—' }}</td>
                    <td style="text-align:center"><span class="badge-status badge-vigente">{{ $p->compras_count }}</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action edit" title="Editar"
                                    onclick="editarProveedor({{ $p->id }},'{{ $p->ruc }}','{{ addslashes($p->nombre_comercial) }}','{{ addslashes($p->contacto_nombre ?? '') }}','{{ addslashes($p->telefono ?? '') }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('proveedores.destroy', $p) }}" class="d-inline" id="delProv{{ $p->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-action delete" title="Eliminar"
                                        onclick="confirmDelete(document.getElementById('delProv{{ $p->id }}'),'{{ addslashes($p->nombre_comercial) }}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">
                    <i class="bi bi-truck" style="font-size:32px;display:block;margin-bottom:10px"></i>Sin proveedores registrados
                </td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($proveedores->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border);padding:12px 20px">{{ $proveedores->links() }}</div>
    @endif
</div>

<div class="sigvi-modal-overlay" id="modalProveedor">
    <div class="sigvi-modal">
        <div class="sigvi-modal-header">
            <h4 id="modalProvTitulo"><i class="bi bi-truck-front text-primary"></i> Nuevo Proveedor</h4>
            <button class="sigvi-modal-close" onclick="cerrarModalProv()">&times;</button>
        </div>
        <form method="POST" id="formProveedor" action="{{ route('proveedores.store') }}">
            @csrf
            <input type="hidden" name="_method" id="metodoProv" value="POST">
            <div class="sigvi-modal-body">
                <div class="row g-3">
                    <div class="col-sm-5">
                        <div class="form-group mb-0">
                            <label class="form-label">RUC (11 dígitos) <span class="req">*</span></label>
                            <input type="text" name="ruc" id="provRuc" class="form-control" maxlength="11" required placeholder="20xxxxxxxxx">
                        </div>
                    </div>
                    <div class="col-sm-7">
                        <div class="form-group mb-0">
                            <label class="form-label">Nombre Comercial <span class="req">*</span></label>
                            <input type="text" name="nombre_comercial" id="provNombre" class="form-control" required>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label">Nombre Contacto</label>
                            <input type="text" name="contacto_nombre" id="provContacto" class="form-control">
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <div class="form-group mb-0">
                            <label class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="provTelefono" class="form-control">
                        </div>
                    </div>
                </div>
            </div>
            <div class="sigvi-modal-footer">
                <button type="button" class="btn btn-outline" onclick="cerrarModalProv()">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> <span id="btnProvLabel">Guardar</span></button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editarProveedor(id, ruc, nombre, contacto, telefono) {
    document.getElementById('modalProvTitulo').innerHTML = '<i class="bi bi-pencil text-primary"></i> Editar Proveedor';
    document.getElementById('btnProvLabel').textContent = 'Actualizar';
    document.getElementById('formProveedor').action = `/proveedores/${id}`;
    document.getElementById('metodoProv').value = 'PUT';
    document.getElementById('provRuc').value      = ruc;
    document.getElementById('provNombre').value   = nombre;
    document.getElementById('provContacto').value = contacto;
    document.getElementById('provTelefono').value = telefono;
    openModal('modalProveedor');
}
function cerrarModalProv() {
    document.getElementById('modalProvTitulo').innerHTML = '<i class="bi bi-truck-front text-primary"></i> Nuevo Proveedor';
    document.getElementById('btnProvLabel').textContent = 'Guardar';
    document.getElementById('formProveedor').action = '{{ route("proveedores.store") }}';
    document.getElementById('metodoProv').value = 'POST';
    document.getElementById('formProveedor').reset();
    closeModal('modalProveedor');
}
</script>
@endpush
