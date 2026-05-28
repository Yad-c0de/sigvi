{{-- Este archivo es: resources/views/categorias/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Categorías')
@section('page-icon')<i class="bi bi-tag-fill text-primary"></i>@endsection
@section('subtitle', 'Catálogo de categorías')

@section('topbar-actions')
    <div class="search-bar d-none d-md-flex">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Buscar...">
    </div>
    <button class="btn btn-primary" onclick="openModal('modalCategoria')">
        <i class="bi bi-plus-circle-fill"></i> <span>Nueva Categoría</span>
    </button>
@endsection

@section('content')
<div class="card">
    <div class="table-wrapper">
        <table id="mainTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nombre</th>
                    <th>Prefijo</th>
                    <th style="text-align:center">Productos</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($categorias as $c)
                <tr>
                    <td><strong>#{{ $c->id }}</strong></td>
                    <td><strong>{{ $c->nombre }}</strong></td>
                    <td><code style="background:#f1f5f9;padding:2px 7px;border-radius:5px;font-size:12px">{{ $c->prefijo ?? '—' }}</code></td>
                    <td style="text-align:center"><span class="badge-status badge-vigente">{{ $c->productos_count }}</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action edit" onclick="editarCat({{ $c->id }}, '{{ addslashes($c->nombre) }}', '{{ addslashes($c->prefijo ?? '') }}')">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('categorias.destroy', $c) }}" class="d-inline" id="delCat{{ $c->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-action delete"
                                        onclick="confirmDelete(document.getElementById('delCat{{ $c->id }}'),'{{ addslashes($c->nombre) }}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted)">Sin categorías registradas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categorias->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border);padding:12px 20px">{{ $categorias->links() }}</div>
    @endif
</div>

{{-- Modal Crear / Editar Categoría --}}
<div class="sigvi-modal-overlay" id="modalCategoria">
    <div class="sigvi-modal" style="max-width:480px">
        <div class="sigvi-modal-header">
            <h4 id="modalCatTitulo"><i class="bi bi-tag text-primary"></i> Nueva Categoría</h4>
            <button class="sigvi-modal-close" onclick="cerrarModalCat()">&times;</button>
        </div>
        <form method="POST" id="formCat" action="{{ route('categorias.store') }}">
            @csrf
            <input type="hidden" name="_method" id="metodoCat" value="POST">
            <div class="sigvi-modal-body">
                <div class="row g-3">
                    <div class="col-sm-7">
                        <div class="form-group mb-0">
                            <label class="form-label">Nombre <span class="req">*</span></label>
                            <input type="text" name="nombre" id="catNombre" class="form-control" required maxlength="100" placeholder="Ej: GPS Vehicular">
                        </div>
                    </div>
                    <div class="col-sm-5">
                        <div class="form-group mb-0">
                            <label class="form-label">Prefijo <span class="req">*</span></label>
                            <input type="text" name="prefijo" id="catPrefijo" class="form-control" required maxlength="4" placeholder="Ej: GPS" style="text-transform:uppercase">
                            <small style="color:var(--text-muted);font-size:11px">Código de 3-4 letras</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sigvi-modal-footer">
                <button type="button" class="btn btn-outline" onclick="cerrarModalCat()">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> <span id="btnCatLabel">Guardar</span></button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editarCat(id, nombre, prefijo) {
    document.getElementById('modalCatTitulo').innerHTML = '<i class="bi bi-pencil text-primary"></i> Editar Categoría';
    document.getElementById('btnCatLabel').textContent = 'Actualizar';
    document.getElementById('formCat').action = `/categorias/${id}`;
    document.getElementById('metodoCat').value = 'PUT';
    document.getElementById('catNombre').value = nombre;
    document.getElementById('catPrefijo').value = prefijo;
    openModal('modalCategoria');
}
function cerrarModalCat() {
    document.getElementById('modalCatTitulo').innerHTML = '<i class="bi bi-tag text-primary"></i> Nueva Categoría';
    document.getElementById('btnCatLabel').textContent = 'Guardar';
    document.getElementById('formCat').action = '{{ route("categorias.store") }}';
    document.getElementById('metodoCat').value = 'POST';
    document.getElementById('formCat').reset();
    closeModal('modalCategoria');
}
</script>
@endpush
