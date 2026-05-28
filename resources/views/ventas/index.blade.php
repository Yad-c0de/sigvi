@extends('layouts.app')

@section('title', 'Ventas')
@section('page-icon')<i class="bi bi-receipt-cutoff text-primary"></i>@endsection
@section('subtitle', 'Historial · ' . $ventas->total() . ' registros')

@section('topbar-actions')
    <div class="search-bar d-none d-md-flex">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Buscar venta...">
    </div>
    <a href="{{ route('ventas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Nueva Venta</span>
    </a>
@endsection

@section('content')
<div class="d-md-none mb-3">
    <div class="search-bar w-100">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInputMobile" placeholder="Buscar venta..." style="width:100%">
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table id="mainTable">
            <thead>
                <tr><th>#ID</th><th>Comprobante</th><th>Cliente</th><th>Vendedor</th><th>Subtotal</th><th>IGV</th><th>Total</th><th>Pago</th><th>Estado</th><th>Fecha</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($ventas as $v)
                <tr>
                    <td><strong>#{{ $v->id }}</strong></td>
                    <td><span style="font-size:11px;color:var(--text-muted);display:block">{{ $v->tipo_comprobante }}</span><strong>{{ $v->serie }}-{{ str_pad($v->correlativo, 8, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>{{ $v->cliente?->nombre_razon_social ?? 'N/A' }}</td>
                    <td style="color:var(--text-muted);font-size:12px">{{ $v->user?->name ?? 'N/A' }}</td>
                    <td>S/ {{ number_format($v->subtotal, 2) }}</td>
                    <td>S/ {{ number_format($v->igv, 2) }}</td>
                    <td><strong>S/ {{ number_format($v->total, 2) }}</strong></td>
                    <td style="font-size:12px">{{ $v->forma_pago }}</td>
                    <td><span class="badge-status {{ $v->estado === 'Completada' ? 'badge-completada' : 'badge-anulada' }}">{{ $v->estado }}</span></td>
                    <td style="font-size:12px;color:var(--text-muted)">{{ $v->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('ventas.show', $v) }}" class="btn-action view" title="Ver detalle"><i class="bi bi-eye"></i></a>
                            @if($v->estado === 'Completada' && auth()->user()->rol === 'Admin')
                            <button class="btn-action anular" title="Anular venta" onclick="abrirAnular({{ $v->id }}, '{{ $v->serie }}-{{ str_pad($v->correlativo,8,'0',STR_PAD_LEFT) }}')">
                                <i class="bi bi-x-circle"></i>
                            </button>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="11" style="text-align:center;padding:40px;color:var(--text-muted)"><i class="bi bi-inbox" style="font-size:32px;display:block;margin-bottom:10px"></i>Sin ventas registradas aún</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($ventas->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border);padding:12px 20px">{{ $ventas->links() }}</div>
    @endif
</div>

{{-- Modal Anular (solo para Admin) --}}
@if(auth()->user()->rol === 'Admin')
<div class="sigvi-modal-overlay" id="modalAnular">
    <div class="sigvi-modal">
        <div class="sigvi-modal-header">
            <h4><i class="bi bi-x-circle text-danger"></i> Anular Venta</h4>
            <button class="sigvi-modal-close" onclick="closeModal('modalAnular')">&times;</button>
        </div>
        <form id="formAnular" method="POST">
            @csrf
            @method('POST')
            <div class="sigvi-modal-body">
                <div class="alert alert-warning alert-permanent">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div><strong>¿Seguro que deseas anular la venta <span id="anularNumero"></span>?</strong><br><small>El stock será restaurado. <strong>No se realizan devoluciones de dinero</strong> (Política Install D).</small></div>
                </div>
                <div class="form-group">
                    <label class="form-label">Motivo de anulación <span class="req">*</span></label>
                    <textarea name="motivo" class="form-control" rows="3" placeholder="Describe el motivo de la anulación (mínimo 10 caracteres)..." required minlength="10"></textarea>
                </div>
            </div>
            <div class="sigvi-modal-footer">
                <button type="button" class="btn btn-outline" onclick="closeModal('modalAnular')">Cancelar</button>
                <button type="submit" class="btn btn-danger"><i class="bi bi-x-circle"></i> Confirmar Anulación</button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
function abrirAnular(id, numero) {
    document.getElementById('anularNumero').textContent = numero;
    document.getElementById('formAnular').action = `/ventas/${id}/anular`;
    openModal('modalAnular');
}
</script>
@endpush
