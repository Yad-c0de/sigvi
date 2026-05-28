@extends('layouts.app')

@section('title', 'Registrar Compra')
@section('page-icon')<i class="bi bi-cart-plus-fill text-primary"></i>@endsection
@section('subtitle', 'Nueva compra a proveedor')

@section('topbar-actions')
    <a href="{{ route('compras.index') }}" class="btn btn-outline btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')

<form method="POST" action="{{ route('compras.store') }}" id="formCompra">
@csrf
<input type="hidden" name="items" id="hiddenItemsCompra">

<div class="row g-4">

    {{-- Datos de la compra --}}
    <div class="col-lg-5">
        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="bi bi-truck-front-fill text-primary"></i> Datos de la Compra</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label class="form-label">Proveedor <span class="req">*</span></label>
                    <select name="proveedor_id" class="form-select @error('proveedor_id') is-invalid @enderror" required>
                        <option value="">-- Seleccionar proveedor --</option>
                        @foreach($proveedores as $p)
                        <option value="{{ $p->id }}" {{ old('proveedor_id') == $p->id ? 'selected' : '' }}>
                            {{ $p->nombre_comercial }} · RUC {{ $p->ruc }}
                        </option>
                        @endforeach
                    </select>
                    @error('proveedor_id')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group">
                    <label class="form-label">N° Comprobante Proveedor <span class="req">*</span></label>
                    <input type="text" name="comprobante_numero" class="form-control @error('comprobante_numero') is-invalid @enderror"
                           value="{{ old('comprobante_numero') }}" placeholder="Ej: F001-00001234" required>
                    @error('comprobante_numero')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
                <div class="form-group mb-0">
                    <label class="form-label">Fecha de Compra <span class="req">*</span></label>
                    <input type="date" name="fecha_compra" class="form-control @error('fecha_compra') is-invalid @enderror"
                           value="{{ old('fecha_compra', now()->toDateString()) }}" required>
                    @error('fecha_compra')<span class="invalid-feedback">{{ $message }}</span>@enderror
                </div>
            </div>
        </div>

        {{-- Resumen --}}
        <div class="card" style="background:linear-gradient(135deg,var(--primary),var(--primary-mid));border:none">
            <div class="card-body">
                <div style="font-size:13px;font-weight:700;color:rgba(255,255,255,0.5);text-transform:uppercase;letter-spacing:1px;margin-bottom:12px">Total Compra</div>
                <div style="font-size:28px;font-weight:800;color:#fff" id="totalCompraDisplay">S/ 0.00</div>
                <button type="submit" class="btn btn-primary w-100 mt-3" id="btnConfirmarCompra" disabled
                        style="background:linear-gradient(135deg,var(--accent),var(--accent-light))">
                    <i class="bi bi-check-circle-fill"></i> Registrar Compra
                </button>
            </div>
        </div>
    </div>

    {{-- Productos a comprar --}}
    <div class="col-lg-7">
        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="bi bi-search text-primary"></i> Agregar Productos</h3>
            </div>
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col">
                        <label class="form-label">Producto</label>
                        <select id="selectProductoCompra" class="form-select">
                            <option value="">-- Seleccionar producto --</option>
                            @foreach($productos as $p)
                            <option value="{{ $p->id }}" data-nombre="{{ $p->nombre }}" data-codigo="{{ $p->codigo }}">
                                {{ $p->nombre }} ({{ $p->codigo }})
                            </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Cant.</label>
                        <input type="number" id="cantCompra" class="form-control" value="1" min="1" style="width:70px">
                    </div>
                    <div class="col-auto">
                        <label class="form-label">Precio (S/)</label>
                        <input type="number" id="precioCompra" class="form-control" step="0.01" min="0.01" placeholder="0.00" style="width:100px">
                    </div>
                    <div class="col-auto">
                        <label class="form-label">&nbsp;</label>
                        <button type="button" class="btn btn-primary d-block" onclick="agregarItemCompra()">
                            <i class="bi bi-plus"></i>
                        </button>
                    </div>
                </div>
            </div>
        </div>

        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-list-ul text-primary"></i> Detalle de Compra</h3>
                <span id="totalItemsCompra" style="background:var(--accent);color:#fff;border-radius:20px;padding:2px 10px;font-size:12px;font-weight:700">0 items</span>
            </div>
            <div class="carrito-wrapper">
                <div class="carrito-empty" id="carritoVacioCompra">
                    <i class="bi bi-cart-x" style="font-size:32px;display:block;margin-bottom:8px;color:var(--accent)"></i>
                    Selecciona productos para agregar
                </div>
                <div id="carritoItemsCompra"></div>
            </div>
        </div>
    </div>

</div>
</form>

@endsection

@push('scripts')
<script>
let carritoCompra = {};

function agregarItemCompra() {
    const sel     = document.getElementById('selectProductoCompra');
    const id      = sel.value;
    const nombre  = sel.options[sel.selectedIndex]?.dataset?.nombre || '';
    const codigo  = sel.options[sel.selectedIndex]?.dataset?.codigo || '';
    const cant    = parseInt(document.getElementById('cantCompra').value) || 1;
    const precio  = parseFloat(document.getElementById('precioCompra').value) || 0;

    if (!id)     { showToast('Selecciona un producto', 'warning'); return; }
    if (precio <= 0) { showToast('Ingresa el precio de compra', 'warning'); return; }

    if (carritoCompra[id]) {
        carritoCompra[id].cantidad += cant;
        carritoCompra[id].precio_compra = precio;
    } else {
        carritoCompra[id] = { producto_id: id, nombre, codigo, cantidad: cant, precio_compra: precio };
    }

    document.getElementById('selectProductoCompra').value = '';
    document.getElementById('cantCompra').value = 1;
    document.getElementById('precioCompra').value = '';

    renderCarritoCompra();
}

function quitarItemCompra(id) {
    delete carritoCompra[id];
    renderCarritoCompra();
}

function renderCarritoCompra() {
    const items = Object.values(carritoCompra);
    const cont  = document.getElementById('carritoItemsCompra');
    const vacio = document.getElementById('carritoVacioCompra');

    vacio.style.display = items.length ? 'none' : 'block';
    cont.innerHTML = '';

    let total = 0;
    items.forEach(item => {
        const sub = item.cantidad * item.precio_compra;
        total += sub;
        const div = document.createElement('div');
        div.className = 'carrito-item';
        div.style.gridTemplateColumns = '1fr auto auto auto';
        div.innerHTML = `
            <div>
                <div style="font-size:13.5px;font-weight:600">${item.nombre}</div>
                <div style="font-size:11px;color:var(--text-muted)">${item.codigo}</div>
            </div>
            <div style="font-size:13px">${item.cantidad} und.</div>
            <div style="font-weight:700;color:var(--accent)">S/ ${sub.toFixed(2)}</div>
            <button type="button" onclick="quitarItemCompra(${item.producto_id})"
                style="background:none;border:none;cursor:pointer;color:var(--danger);font-size:16px;padding:4px">
                <i class="bi bi-trash3"></i>
            </button>`;
        cont.appendChild(div);
    });

    document.getElementById('totalCompraDisplay').textContent = 'S/ ' + total.toFixed(2);
    document.getElementById('totalItemsCompra').textContent   = items.length + ' item' + (items.length !== 1 ? 's' : '');
    document.getElementById('btnConfirmarCompra').disabled    = items.length === 0;
    document.getElementById('hiddenItemsCompra').value        = JSON.stringify(items);
}
</script>
@endpush
