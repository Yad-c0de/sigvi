@extends('layouts.app')

@section('title', 'Garantías')
@section('page-icon')<i class="bi bi-shield-check text-primary"></i>@endsection
@section('subtitle', 'Control de garantías de productos')

@section('topbar-actions')
    <div class="search-bar d-none d-md-flex">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Buscar garantía...">
    </div>
@endsection

@section('content')

<div class="card">
    <div class="table-wrapper">
        <table id="mainTable">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Venta</th>
                    <th>Cliente</th>
                    <th>Producto</th>
                    <th>Fecha Límite</th>
                    <th>Días Restantes</th>
                    <th>Estado</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($garantias as $g)
                @php
                    $diasRestantes = now()->diffInDays($g->fecha_limite, false);
                    $esVencida     = $diasRestantes < 0;
                @endphp
                <tr>
                    <td><strong>#{{ $g->id }}</strong></td>
                    <td>
                        <a href="{{ route('ventas.show', $g->venta_id) }}" style="color:var(--accent);font-weight:600;text-decoration:none">
                            #{{ $g->venta_id }}
                        </a>
                    </td>
                    <td style="font-size:13px">{{ $g->venta?->cliente?->nombre_razon_social ?? 'N/A' }}</td>
                    <td><strong>{{ $g->producto?->nombre ?? 'N/A' }}</strong></td>
                    <td>{{ \Carbon\Carbon::parse($g->fecha_limite)->format('d/m/Y') }}</td>
                    <td>
                        @if($esVencida)
                            <span style="color:var(--danger);font-weight:600">Vencida</span>
                        @elseif($diasRestantes <= 30)
                            <span style="color:var(--warning);font-weight:600">{{ $diasRestantes }} días</span>
                        @else
                            <span style="color:var(--success);font-weight:600">{{ $diasRestantes }} días</span>
                        @endif
                    </td>
                    <td>
                        <span class="badge-status badge-{{ strtolower($g->estado) }}">
                            <i class="bi bi-{{ $g->estado === 'Vigente' ? 'shield-check' : ($g->estado === 'Reclamada' ? 'shield-exclamation' : 'shield-x') }}"></i>
                            {{ $g->estado }}
                        </span>
                    </td>
                    <td>
                        @if($g->estado === 'Vigente')
                        <form method="POST" action="{{ route('garantias.update', $g) }}" class="d-inline">
                            @csrf @method('PUT')
                            <input type="hidden" name="estado" value="Reclamada">
                            <button type="submit" class="btn btn-outline btn-sm" title="Marcar como reclamada"
                                    onclick="return confirm('¿Marcar esta garantía como Reclamada?')">
                                <i class="bi bi-shield-exclamation"></i> Reclamar
                            </button>
                        </form>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <i class="bi bi-shield" style="font-size:32px;display:block;margin-bottom:10px"></i>
                        Sin garantías registradas
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($garantias->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border);padding:12px 20px">{{ $garantias->links() }}</div>
    @endif
</div>

@endsection
