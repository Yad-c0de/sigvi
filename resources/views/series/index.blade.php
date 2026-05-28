@extends('layouts.app')

@section('title', 'Series de Comprobantes')
@section('page-icon')<i class="bi bi-123 text-primary"></i>@endsection
@section('subtitle', 'Configuración de series (B001 Boletas, F001 Facturas)')

@section('topbar-actions')
    <button class="btn btn-primary" onclick="openModal('modalSerie')">
        <i class="bi bi-plus-circle-fill"></i> <span>Nueva Serie</span>
    </button>
@endsection

@section('content')

<div class="alert alert-info alert-permanent mb-4">
    <i class="bi bi-info-circle-fill"></i>
    <div>
        <strong>Importante:</strong> Define una serie para <strong>Boleta</strong> (ej: B001) y otra para <strong>Factura</strong> (ej: F001).
        El sistema asignará correlativos automáticamente al registrar ventas.
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table>
            <thead>
                <tr><th>Tipo Comprobante</th><th>Serie</th><th>Último Correlativo</th><th>Próximo N°</th><th>Acciones</th></tr>
            </thead>
            <tbody>
                @forelse($series as $s)
                <tr>
                    <td>
                        <span class="badge-status {{ $s->tipo_comprobante === 'Boleta' ? 'badge-vigente' : 'badge-reclamada' }}">
                            <i class="bi bi-file-earmark-text"></i> {{ $s->tipo_comprobante }}
                        </span>
                    </td>
                    <td><code style="background:#f1f5f9;padding:3px 9px;border-radius:5px;font-size:14px;font-weight:700">{{ $s->serie }}</code></td>
                    <td>{{ str_pad($s->ultimo_correlativo, 8, '0', STR_PAD_LEFT) }}</td>
                    <td><strong style="color:var(--accent)">{{ $s->serie }}-{{ str_pad($s->ultimo_correlativo + 1, 8, '0', STR_PAD_LEFT) }}</strong></td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action edit" onclick="editarSerie({{ $s->id }},'{{ $s->tipo_comprobante }}','{{ $s->serie }}',{{ $s->ultimo_correlativo }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('series.destroy', $s) }}" class="d-inline" id="delSerie{{ $s->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-action delete"
                                        onclick="confirmDelete(document.getElementById('delSerie{{ $s->id }}'),'serie {{ $s->serie }}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <i class="bi bi-123" style="font-size:32px;display:block;margin-bottom:10px"></i>
                        Sin series configuradas. Crea una para poder registrar ventas.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<div class="sigvi-modal-overlay" id="modalSerie">
    <div class="sigvi-modal" style="max-width:440px">
        <div class="sigvi-modal-header">
            <h4 id="modalSerieTitulo"><i class="bi bi-123 text-primary"></i> Nueva Serie</h4>
            <button class="sigvi-modal-close" onclick="cerrarModalSerie()">&times;</button>
        </div>
        <form method="POST" id="formSerie" action="{{ route('series.store') }}">
            @csrf
            <input type="hidden" name="_method" id="metodoSerie" value="POST">
            <div class="sigvi-modal-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <label class="form-label">Tipo <span class="req">*</span></label>
                            <select name="tipo_comprobante" id="serieTipo" class="form-select" required>
                                <option value="Boleta">Boleta</option>
                                <option value="Factura">Factura</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <label class="form-label">Serie (4 car.) <span class="req">*</span></label>
                            <input type="text" name="serie" id="serieValor" class="form-control" maxlength="4" required
                                   placeholder="B001" style="text-transform:uppercase">
                        </div>
                    </div>
                    <div class="col-12">
                        <div class="form-group mb-0">
                            <label class="form-label">Último Correlativo</label>
                            <input type="number" name="ultimo_correlativo" id="serieCorrelativo" class="form-control" min="0" value="0">
                            <small style="color:var(--text-muted);font-size:11px">Deja en 0 para iniciar desde 1</small>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sigvi-modal-footer">
                <button type="button" class="btn btn-outline" onclick="cerrarModalSerie()">Cancelar</button>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> <span id="btnSerieLabel">Guardar</span></button>
            </div>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function editarSerie(id, tipo, serie, correlativo) {
    document.getElementById('modalSerieTitulo').innerHTML = '<i class="bi bi-pencil text-primary"></i> Editar Serie';
    document.getElementById('btnSerieLabel').textContent = 'Actualizar';
    document.getElementById('formSerie').action = `/series/${id}`;
    document.getElementById('metodoSerie').value = 'PUT';
    document.getElementById('serieTipo').value         = tipo;
    document.getElementById('serieValor').value        = serie;
    document.getElementById('serieCorrelativo').value  = correlativo;
    openModal('modalSerie');
}
function cerrarModalSerie() {
    document.getElementById('modalSerieTitulo').innerHTML = '<i class="bi bi-123 text-primary"></i> Nueva Serie';
    document.getElementById('btnSerieLabel').textContent = 'Guardar';
    document.getElementById('formSerie').action = '{{ route("series.store") }}';
    document.getElementById('metodoSerie').value = 'POST';
    document.getElementById('formSerie').reset();
    closeModal('modalSerie');
}
</script>
@endpush
