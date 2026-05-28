@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-icon')<i class="bi bi-speedometer2 text-primary"></i>@endsection
@section('subtitle', now()->translatedFormat('l, d \d\e F \d\e Y'))

@section('topbar-actions')
    <a href="{{ route('ventas.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Nueva Venta</span>
    </a>
@endsection

@section('content')

{{-- ── Stats Grid ─────────────────────────────────────────────────────── --}}
<div class="stats-grid mb-4">
    <div class="stat-card">
        <div class="stat-icon orange"><i class="bi bi-cash-stack"></i></div>
        <div class="stat-info">
            <div class="stat-value">S/ {{ number_format($totalVentas->sum ?? 0, 0, '.', ',') }}</div>
            <div class="stat-label">Total en Ventas</div>
            <div class="stat-change up"><i class="bi bi-arrow-up-short"></i>{{ $totalVentas->count ?? 0 }} ventas completadas</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="bi bi-boxes"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalProductos }}</div>
            <div class="stat-label">Productos en Catálogo</div>
            @if($stockBajo > 0)
                <div class="stat-change down"><i class="bi bi-exclamation-triangle"></i>{{ $stockBajo }} con stock bajo</div>
            @else
                <div class="stat-change up"><i class="bi bi-check-circle"></i>Stock en orden</div>
            @endif
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="bi bi-people-fill"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $totalClientes }}</div>
            <div class="stat-label">Clientes Registrados</div>
            <div class="stat-change up"><i class="bi bi-person-plus"></i>Base activa</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon cyan"><i class="bi bi-shield-check"></i></div>
        <div class="stat-info">
            <div class="stat-value">{{ $garantiasVig }}</div>
            <div class="stat-label">Garantías Vigentes</div>
            <div class="stat-change {{ $garantiasVig > 0 ? 'down' : 'up' }}">
                <i class="bi bi-shield-{{ $garantiasVig > 0 ? 'exclamation' : 'check' }}"></i>
                {{ $garantiasVig > 0 ? 'Requieren atención' : 'Sin pendientes' }}
            </div>
        </div>
    </div>
</div>

{{-- ── Acciones rápidas ─────────────────────────────────────────────── --}}
<div class="card mb-4">
    <div class="card-body py-3">
        <div class="d-flex align-items-center gap-2 flex-wrap">
            <span class="fw-600 text-muted me-1" style="font-size:13px">Acciones rápidas:</span>
            <a href="{{ route('ventas.create') }}"    class="btn btn-primary btn-sm"><i class="bi bi-receipt-cutoff"></i> Nueva Venta</a>
            <a href="{{ route('productos.index') }}"  class="btn btn-outline btn-sm"><i class="bi bi-plus-circle"></i> Agregar Producto</a>
            <a href="{{ route('clientes.index') }}"   class="btn btn-outline btn-sm"><i class="bi bi-person-plus"></i> Nuevo Cliente</a>
            <a href="{{ route('compras.create') }}"   class="btn btn-outline btn-sm"><i class="bi bi-cart-plus"></i> Registrar Compra</a>
            <a href="{{ route('garantias.index') }}"  class="btn btn-outline btn-sm"><i class="bi bi-shield-plus"></i> Garantías</a>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- ── Últimas ventas ─────────────────────────────────────────────── --}}
    <div class="col-lg-7 col-12">
        <div class="card h-100">
            <div class="card-header">
                <h3><i class="bi bi-receipt-cutoff text-primary"></i> Últimas Ventas</h3>
                <a href="{{ route('ventas.index') }}" class="btn btn-outline btn-sm">
                    Ver todas <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Comprobante</th>
                            <th>Cliente</th>
                            <th>Total</th>
                            <th>Estado</th>
                            <th>Fecha</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($ultimasVentas as $v)
                        <tr>
                            <td><strong>#{{ $v->id }}</strong></td>
                            <td>
                                <span style="font-size:11px;color:var(--text-muted)">{{ $v->tipo_comprobante }}</span><br>
                                <strong>{{ $v->serie }}-{{ str_pad($v->correlativo, 8, '0', STR_PAD_LEFT) }}</strong>
                            </td>
                            <td>{{ $v->cliente?->nombre_razon_social ?? 'N/A' }}</td>
                            <td><strong>S/ {{ number_format($v->total, 2) }}</strong></td>
                            <td>
                                <span class="badge-status {{ $v->estado === 'Completada' ? 'badge-completada' : 'badge-anulada' }}">
                                    <i class="bi bi-{{ $v->estado === 'Completada' ? 'check-circle' : 'x-circle' }}"></i>
                                    {{ $v->estado }}
                                </span>
                            </td>
                            <td style="font-size:12px;color:var(--text-muted)">{{ $v->created_at->format('d/m/Y H:i') }}</td>
                        </tr>
                        @empty
                        <tr><td colspan="6" style="text-align:center;padding:32px;color:var(--text-muted)">
                            <i class="bi bi-inbox" style="font-size:28px;display:block;margin-bottom:8px"></i>
                            Sin ventas registradas aún
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    {{-- ── Stock bajo ────────────────────────────────────────────────── --}}
    <div class="col-lg-5 col-12">
        <div class="card h-100">
            <div class="card-header">
                <h3><i class="bi bi-exclamation-triangle text-warning"></i> Stock Bajo</h3>
                <a href="{{ route('productos.index') }}" class="btn btn-outline btn-sm">
                    Ver productos <i class="bi bi-arrow-right ms-1"></i>
                </a>
            </div>
            <div class="table-wrapper">
                <table>
                    <thead>
                        <tr>
                            <th>Producto</th>
                            <th>Categoría</th>
                            <th style="text-align:center">Stock</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($stockAlerta as $p)
                        <tr>
                            <td>
                                <strong style="font-size:13px">{{ $p->nombre }}</strong><br>
                                <span style="font-size:11px;color:var(--text-muted)">{{ $p->codigo }}</span>
                            </td>
                            <td style="font-size:12px;color:var(--text-muted)">{{ $p->categoria?->nombre }}</td>
                            <td style="text-align:center">
                                <span class="{{ $p->stock === 0 ? 'stock-out' : ($p->stock <= 5 ? 'stock-low' : 'stock-ok') }}">
                                    {{ $p->stock }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="3" style="text-align:center;padding:32px;color:var(--text-muted)">
                            <i class="bi bi-check-circle-fill text-success" style="font-size:24px;display:block;margin-bottom:8px"></i>
                            ¡Todo el stock en orden!
                        </td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

@endsection
