@extends('layouts.app')

@section('title', 'Compras')
@section('page-icon')<i class="bi bi-cart-plus-fill text-primary"></i>@endsection
@section('subtitle', 'Historial · ' . $totalItems . ' compras')

@section('topbar-actions')
    <div class="search-bar d-none d-md-flex">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Buscar compra...">
    </div>
    <a href="{{ route('compras.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Nueva Compra</span>
    </a>
@endsection

@section('content')

<div class="card">
    <div class="table-wrapper">
        <table id="mainTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Proveedor</th>
                    <th>Comprobante</th>
                    <th>Fecha Compra</th>
                    <th>Total</th>
                    <th>Registrado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($compras as $c)
                <tr>
                    <td><strong>#{{ $c->id }}</strong></td>
                    <td><strong>{{ $c->proveedor?->nombre_comercial }}</strong></td>
                    <td><code style="background:#f1f5f9;padding:2px 7px;border-radius:5px;font-size:12px">{{ $c->comprobante_numero }}</code></td>
                    <td>{{ \Carbon\Carbon::parse($c->fecha_compra)->format('d/m/Y') }}</td>
                    <td><strong style="color:var(--accent)">S/ {{ number_format($c->total, 2) }}</strong></td>
                    <td style="font-size:12px;color:var(--text-muted)">{{ $c->created_at->format('d/m/Y H:i') }}</td>
                    <td>
                        <a href="{{ route('compras.show', $c) }}" class="btn-action view" title="Ver detalle">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <i class="bi bi-cart-x" style="font-size:32px;display:block;margin-bottom:10px"></i>
                        Sin compras registradas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($compras->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border);padding:12px 20px">
        {{ $compras->links() }}
    </div>
    @endif
</div>

@endsection
