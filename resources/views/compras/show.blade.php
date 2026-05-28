@extends('layouts.app')

@section('title', 'Detalle de Compra')
@section('page-icon')<i class="bi bi-cart-plus-fill text-primary"></i>@endsection
@section('subtitle', 'Compra #' . $compra->id)

@section('topbar-actions')
    <a href="{{ route('compras.index') }}" class="btn btn-outline btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
    <button class="btn btn-outline btn-sm" onclick="window.print()">
        <i class="bi bi-printer"></i> Imprimir
    </button>
@endsection

@section('content')
<div class="row g-4">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-file-earmark-text text-primary"></i> Compra #{{ $compra->id }}</h3>
                <span class="badge-status badge-vigente">Registrada</span>
            </div>
            <div class="card-body">
                <div class="row g-3 mb-4">
                    <div class="col-sm-6">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase">Proveedor</div>
                        <div style="font-weight:700">{{ $compra->proveedor->nombre_comercial }}</div>
                        <div style="font-size:12px;color:var(--text-muted)">RUC: {{ $compra->proveedor->ruc }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase">Comprobante</div>
                        <div style="font-weight:600">{{ $compra->comprobante_numero }}</div>
                    </div>
                    <div class="col-sm-3">
                        <div style="font-size:11px;color:var(--text-muted);font-weight:700;text-transform:uppercase">Fecha</div>
                        <div style="font-weight:600">{{ \Carbon\Carbon::parse($compra->fecha_compra)->format('d/m/Y') }}</div>
                    </div>
                </div>
                <div class="table-wrapper" style="border:1.5px solid var(--border);border-radius:var(--radius-sm)">
                    <table>
                        <thead>
                            <tr><th>#</th><th>Producto</th><th>Código</th><th>Cantidad</th><th>Precio Compra</th><th>Subtotal</th></tr>
                        </thead>
                        <tbody>
                            @foreach($compra->detalles as $i => $d)
                            <tr>
                                <td>{{ $i+1 }}</td>
                                <td><strong>{{ $d->producto?->nombre }}</strong></td>
                                <td><code style="font-size:11px">{{ $d->producto?->codigo }}</code></td>
                                <td>{{ $d->cantidad }}</td>
                                <td>S/ {{ number_format($d->precio_compra, 2) }}</td>
                                <td><strong>S/ {{ number_format($d->cantidad * $d->precio_compra, 2) }}</strong></td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-4">
        <div class="card" style="background:linear-gradient(135deg,var(--primary),var(--primary-mid));border:none">
            <div class="card-body">
                <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1px;margin-bottom:12px">Total Compra</div>
                <div style="font-size:28px;font-weight:800;color:#fff">S/ {{ number_format($compra->total, 2) }}</div>
                <div style="margin-top:12px;font-size:12px;color:rgba(255,255,255,0.4)">
                    Registrada el {{ $compra->created_at->format('d/m/Y H:i') }}
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
