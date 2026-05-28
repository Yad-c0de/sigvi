@extends('layouts.app')

@section('title', 'Marcas')
@section('page-icon')<i class="bi bi-bookmark-star-fill text-primary"></i>@endsection
@section('subtitle', 'Catálogo de marcas')

@section('topbar-actions')
    <div class="search-bar d-none d-md-flex">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Buscar marca...">
    </div>
    <button class="btn btn-primary" onclick="openModal('modalMarca')">
        <i class="bi bi-plus-circle-fill"></i> <span>Nueva Marca</span>
    </button>
@endsection

@section('content')
<div class="card">
    <div class="table-wrapper">
        <table id="mainTable">
            <thead><tr><th>#</th><th>Nombre</th><th style="text-align:center">Productos</th><th>Acciones</th></tr></thead>
            <tbody>
                @forelse($marcas as $m)
                <tr>
                    <td><strong>#{{ $m->id }}</strong></td>
                    <td><strong>{{ $m->nombre }}</strong></td>
                    <td style="text-align:center"><span class="badge-status badge-vigente">{{ $m->productos_count }}</span></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action edit" onclick="editarMarca({{ $m->id }},'{{ addslashes($m->nombre) }}')"><i class="bi bi-pencil"></i></button>
                            <form method="POST" action="{{ route('marcas.destroy', $m) }}" class="d-inline" id="delMarca{{ $m->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-action delete"
                                        onclick="confirmDelete(document.getElementById('delMarca{{ $m->id }}'),'{{ addslashes($m->nombre) }}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="4" style="text-align:center;padding:40px;color:var(--text-muted)">Sin marcas registradas</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($marcas->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border);padding:12px 20px">{{ $marcas->links() }}</div>
    @endif
</div>

<div class="sigvi-modal-overlay" id="modalMarca">
    <div class="sigvi-modal" style="max-width:420px">
        <div class="sigvi-modal-header">
            <h4 id="modalMarcaTitulo"><i class="bi bi-bookmark-star text-primary"></i> Nueva Marca</h4>
            <button class="sigvi-modal-close" onclick="cerrarModalMarca()">&times;</button>
        </div>
        <form method="POST" id="formMarca" action="{{ route('marcas.store') }}">
            @csrf
            <input type="hidden" name="_method" id="metodoMarca" value="POST">
            <div class="sigvi-modal-body">
                <div class="form-group mb-0">
                    <label class="form-label">Nombre <span class="req">*</span></label>
                    <input type="text" name="nombre" id="marcaNombre" class="form-control" required maxlength="100" placeholder="Ej: TOYOTA">
                </div>
            </div>
            <div class="sigvi-modal-footer">
                <button type="button" class="btn btn-outline" onclick="cerrarModalMarca()">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> <span id="btnMarcaLabel">Guardar</span></button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editarMarca(id, nombre) {
    document.getElementById('modalMarcaTitulo').innerHTML = '<i class="bi bi-pencil text-primary"></i> Editar Marca';
    document.getElementById('btnMarcaLabel').textContent = 'Actualizar';
    document.getElementById('formMarca').action = `/marcas/${id}`;
    document.getElementById('metodoMarca').value = 'PUT';
    document.getElementById('marcaNombre').value = nombre;
    openModal('modalMarca');
}
function cerrarModalMarca() {
    document.getElementById('modalMarcaTitulo').innerHTML = '<i class="bi bi-bookmark-star text-primary"></i> Nueva Marca';
    document.getElementById('btnMarcaLabel').textContent = 'Guardar';
    document.getElementById('formMarca').action = '{{ route("marcas.store") }}';
    document.getElementById('metodoMarca').value = 'POST';
    document.getElementById('formMarca').reset();
    closeModal('modalMarca');
}
</script>
@endpush
