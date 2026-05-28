@extends('layouts.app')

@section('title', 'Detalle de Venta')
@section('page-icon')<i class="bi bi-receipt-cutoff text-primary"></i>@endsection
@section('subtitle', $venta->serie . '-' . str_pad($venta->correlativo, 8, '0', STR_PAD_LEFT))

@section('topbar-actions')
    <a href="{{ route('ventas.index') }}" class="btn btn-outline btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    @if($venta->estado === 'Completada' && auth()->user()->rol === 'Admin')
    <button class="btn btn-danger btn-sm" onclick="openModal('modalAnular')">
        <i class="bi bi-x-circle"></i> Anular
    </button>
    @endif
    <button class="btn btn-outline btn-sm" onclick="window.print()">
        <i class="bi bi-printer"></i> Imprimir
    </button>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header">
                <h3><i class="bi bi-file-earmark-text text-primary"></i> {{ $venta->tipo_comprobante }}</h3>
                <span class="badge-status {{ $venta->estado === 'Completada' ? 'badge-completada' : 'badge-anulada' }}">
                    {{ $venta->estado }}
                </span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.8px">N° Comprobante</div>
                        <div style="font-size:18px;font-weight:800;color:var(--text)">{{ $venta->serie }}-{{ str_pad($venta->correlativo, 8, '0', STR_PAD_LEFT) }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.8px">Fecha</div>
                        <div style="font-size:15px;font-weight:600">{{ $venta->created_at->format('d/m/Y H:i') }}</div>
                    </div>
                    <div class="col-sm-6">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.8px">Cliente</div>
                        <div style="font-weight:600">{{ $venta->cliente?->nombre_razon_social ?? 'N/A' }}</div>
                        @if($venta->cliente?->numero_documento)
                        <div style="font-size:12px;color:var(--text-muted)">{{ $venta->cliente->tipo_documento }}: {{ $venta->cliente->numero_documento }}</div>
                        @endif
                    </div>
                    <div class="col-sm-6">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase;letter-spacing:.8px">Vendedor / Forma de Pago</div>
                        <div style="font-weight:600">{{ $venta->user?->name ?? 'N/A' }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">{{ $venta->forma_pago }}</div>
                    </div>
                </div>

                <div class="table-wrapper" style="border-radius:var(--radius-sm);border:1.5px solid var(--border)">
                    <table>
                        <thead>
                            <tr><th>#</th><th>Producto</th><th>Precio Unit.</th><th>Cantidad</th><th>Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach($venta->detalles as $i => $d)
                            <tr>
                                <td>{{ $i + 1 }}</td>
                                <td><strong>{{ $d->producto?->nombre ?? 'N/A' }}</strong>
                                    @if($d->producto?->codigo)<br><span style="font-size:11px;color:var(--text-muted)">{{ $d->producto->codigo }}</span>@endif
                                </td>
                                <td>S/ {{ number_format($d->precio_unitario, 2) }}</td>
                                <td>{{ $d->cantidad }}</td>
                                <td><strong>S/ {{ number_format($d->subtotal, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        @if($venta->estado === 'Anulada' && $venta->anulacion)
        <div class="card" style="border-color:rgba(220,38,38,0.3);background:rgba(220,38,38,0.03)">
            <div class="card-header" style="border-color:rgba(220,38,38,0.2)">
                <h3 style="color:var(--danger)"><i class="bi bi-x-circle-fill"></i> Motivo de Anulación</h3>
            </div>
            <div class="card-body">
                <p style="color:var(--text)">{{ $venta->anulacion->motivo }}</p>
                <small style="color:var(--text-muted)">Anulada por <strong>{{ $venta->anulacion->user?->name }}</strong> el {{ $venta->anulacion->created_at->format('d/m/Y H:i') }}</small>
            </div>
        </div>
        @endif
    </div>

    <div class="col-lg-4">
        <div class="card" style="background:linear-gradient(135deg,var(--primary),var(--primary-mid));border:none">
            <div class="card-body">
                <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1px;margin-bottom:16px">Resumen de Importes</div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;color:rgba(255,255,255,0.7);font-size:14px"><span>Subtotal (BI)</span><span>S/ {{ number_format($venta->subtotal, 2) }}</span></div>
                <div style="display:flex;justify-content:space-between;margin-bottom:8px;color:rgba(255,255,255,0.55);font-size:12px"><span>IGV 18%</span><span>S/ {{ number_format($venta->igv, 2) }}</span></div>
                <div style="display:flex;justify-content:space-between;padding-top:12px;border-top:1px solid rgba(255,255,255,0.2);font-size:22px;font-weight:800;color:#fff"><span>TOTAL</span><span>S/ {{ number_format($venta->total, 2) }}</span></div>
                <div style="margin-top:12px;font-size:11px;color:rgba(255,255,255,0.4);line-height:1.5">{{ $venta->total_letras }}</div>
            </div>
        </div>
    </div>
</div>

{{-- Modal Anular (solo para Admin) --}}
@if($venta->estado === 'Completada' && auth()->user()->rol === 'Admin')
<div class="sigvi-modal-overlay" id="modalAnular">
    <div class="sigvi-modal">
        <div class="sigvi-modal-header">
            <h4><i class="bi bi-x-circle text-danger"></i> Anular Venta</h4>
            <button class="sigvi-modal-close" onclick="closeModal('modalAnular')">&times;</button>
        </div>
        <form method="POST" action="{{ route('ventas.anular', $venta) }}">
            @csrf
            <div class="sigvi-modal-body">
                <div class="alert alert-warning alert-permanent">
                    <i class="bi bi-exclamation-triangle-fill"></i>
                    <div>
                        <strong>¿Anular la venta {{ $venta->serie }}-{{ str_pad($venta->correlativo,8,'0',STR_PAD_LEFT) }}?</strong><br>
                        <small>El stock será restaurado. <strong>No se realizan devoluciones de dinero</strong>.</small>
                    </div>
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Motivo <span class="req">*</span></label>
                    <textarea name="motivo" class="form-control" rows="3" required minlength="10"
                              placeholder="Describe el motivo de la anulación..."></textarea>
                    @error('motivo')<span class="invalid-feedback d-block">{{ $message }}</span>@enderror
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
