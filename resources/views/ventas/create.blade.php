@extends('layouts.app')

@section('title', 'Nueva Venta')
@section('page-icon')<i class="bi bi-receipt-cutoff text-primary"></i>@endsection
@section('subtitle', 'Punto de Venta · ' . now()->format('d/m/Y H:i'))

@section('topbar-actions')
    <a href="{{ route('ventas.index') }}" class="btn btn-outline btn-sm">
        <i class="bi bi-arrow-left"></i> Volver
    </a>
@endsection

@section('content')

<form method="POST" action="{{ route('ventas.store') }}" id="formVenta">
@csrf

{{-- Campos ocultos que se llenan por JS --}}
<input type="hidden" name="total" id="hiddenTotal">
<input type="hidden" name="total_letras" id="hiddenTotalLetras">
<input type="hidden" name="items" id="hiddenItems">

<div class="row g-4">

    {{-- ── Panel izquierdo: datos de la venta ──────────────────────── --}}
    <div class="col-lg-5">

        {{-- Cliente --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="bi bi-person-fill text-primary"></i> Cliente</h3>
                <button type="button" class="btn btn-outline btn-sm" onclick="openModal('modalNuevoCliente')">
                    <i class="bi bi-person-plus"></i> Nuevo
                </button>
            </div>
            <div class="card-body">
                <div class="form-group mb-0">
                    <label class="form-label">Seleccionar cliente <span class="req">*</span></label>
                    <select name="cliente_id" id="clienteSelect" class="form-select @error('cliente_id') is-invalid @enderror" required>
                        <option value="">-- Seleccionar cliente --</option>
                        @foreach($clientes as $c)
                        <option value="{{ $c->id }}" {{ old('cliente_id') == $c->id ? 'selected' : '' }}>
                            {{ $c->nombre_razon_social ?? 'Cliente General' }}
                            @if($c->numero_documento) · {{ $c->numero_documento }} @endif
                        </option>
                        @endforeach
                    </select>
                    @error('cliente_id')
                        <span class="invalid-feedback">{{ $message }}</span>
                    @enderror
                </div>
            </div>
        </div>

        {{-- Comprobante y pago --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="bi bi-file-earmark-text text-primary"></i> Comprobante</h3>
            </div>
            <div class="card-body">
                <div class="row g-3">
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <label class="form-label">Tipo <span class="req">*</span></label>
                            <select name="tipo_comprobante" id="tipoComprobante" class="form-select @error('tipo_comprobante') is-invalid @enderror" required>
                                <option value="">-- Tipo --</option>
                                @foreach($series as $s)
                                <option value="{{ $s->tipo_comprobante }}" {{ old('tipo_comprobante') == $s->tipo_comprobante ? 'selected' : '' }}>
                                    {{ $s->tipo_comprobante }} ({{ $s->serie }})
                                </option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                    <div class="col-6">
                        <div class="form-group mb-0">
                            <label class="form-label">Forma de Pago <span class="req">*</span></label>
                            <select name="forma_pago" class="form-select @error('forma_pago') is-invalid @enderror" required>
                                <option value="Efectivo" {{ old('forma_pago','Efectivo') === 'Efectivo' ? 'selected' : '' }}>Efectivo</option>
                                <option value="Yape"     {{ old('forma_pago') === 'Yape' ? 'selected' : '' }}>Yape</option>
                                <option value="Plin"     {{ old('forma_pago') === 'Plin' ? 'selected' : '' }}>Plin</option>
                                <option value="Tarjeta"  {{ old('forma_pago') === 'Tarjeta' ? 'selected' : '' }}>Tarjeta</option>
                                <option value="Transferencia" {{ old('forma_pago') === 'Transferencia' ? 'selected' : '' }}>Transferencia</option>
                            </select>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Resumen --}}
        <div class="card" style="background:linear-gradient(135deg,var(--primary) 0%,var(--primary-mid) 100%);border:none">
            <div class="card-body">
                <div class="carrito-footer" style="background:transparent;border:none;padding:0">
                    <div class="carrito-row" style="color:rgba(255,255,255,0.7)">
                        <span>Subtotal (sin IGV)</span>
                        <span id="resSubtotal">S/ 0.00</span>
                    </div>
                    <div class="carrito-row igv" style="color:rgba(255,255,255,0.55)">
                        <span>IGV (18%)</span>
                        <span id="resIgv">S/ 0.00</span>
                    </div>
                    <div class="carrito-row total" style="color:#fff;border-color:rgba(255,255,255,0.2)">
                        <span>TOTAL</span>
                        <span id="resTotal">S/ 0.00</span>
                    </div>
                </div>
                <div style="margin-top:14px;padding-top:12px;border-top:1px solid rgba(255,255,255,0.15);color:rgba(255,255,255,0.55);font-size:12px" id="totalLetrasDisplay"></div>
                <button type="submit" class="btn btn-primary w-100 mt-3" id="btnConfirmar" disabled style="background:linear-gradient(135deg,var(--accent),var(--accent-light))">
                    <i class="bi bi-check-circle-fill"></i> Confirmar Venta
                </button>
            </div>
        </div>
    </div>

    {{-- ── Panel derecho: búsqueda y carrito ───────────────────────── --}}
    <div class="col-lg-7">

        {{-- Buscar producto --}}
        <div class="card mb-3">
            <div class="card-header">
                <h3><i class="bi bi-search text-primary"></i> Buscar Producto</h3>
            </div>
            <div class="card-body">
                <div class="row g-2 align-items-end">
                    <div class="col">
                        <div class="form-group mb-0">
                            <label class="form-label">Código o nombre</label>
                            <div class="search-bar w-100">
                                <i class="bi bi-search search-icon"></i>
                                <input type="text" id="buscarProducto" placeholder="Escribe para buscar..." style="width:100%;border-radius:var(--radius-sm)">
                            </div>
                        </div>
                    </div>
                    <div class="col-auto">
                        <div class="form-group mb-0">
                            <label class="form-label">Cantidad</label>
                            <input type="number" id="cantidadInput" class="form-control" value="1" min="1" style="width:80px">
                        </div>
                    </div>
                </div>
                <div id="resultadosBusqueda" style="margin-top:10px;display:none;">
                    <div style="font-size:11px;color:var(--text-muted);margin-bottom:6px">Resultados:</div>
                    <div id="listaResultados" style="border:1.5px solid var(--border);border-radius:var(--radius-sm);overflow:hidden;max-height:220px;overflow-y:auto;background:#fff;"></div>
                </div>
            </div>
        </div>

        {{-- Carrito --}}
        <div class="card">
            <div class="card-header">
                <h3><i class="bi bi-cart3 text-primary"></i> Carrito</h3>
                <span id="totalItems" style="background:var(--accent);color:#fff;border-radius:20px;padding:2px 10px;font-size:12px;font-weight:700">0 items</span>
            </div>
            <div class="carrito-wrapper" id="carritoWrapper">
                <div class="carrito-empty" id="carritoVacio">
                    <i class="bi bi-cart-x" style="font-size:32px;display:block;margin-bottom:8px;color:var(--accent)"></i>
                    Agrega productos usando el buscador de arriba
                </div>
                <div id="carritoItems"></div>
            </div>
        </div>
    </div>

</div>
</form>

@endsection

@push('scripts')
<script>
// ── Datos de productos (JSON) ─────────────────────────────────────────
const productosData = @json($productos->keyBy('id'));
let carrito = {};

// ── Búsqueda con debounce ─────────────────────────────────────────────
let searchTimer;
document.getElementById('buscarProducto').addEventListener('input', function() {
    clearTimeout(searchTimer);
    const q = this.value.trim();
    if (q.length < 1) { document.getElementById('resultadosBusqueda').style.display = 'none'; return; }
    searchTimer = setTimeout(() => buscarProductos(q), 250);
});

function buscarProductos(q) {
    const ql = q.toLowerCase();
    const resultados = Object.values(productosData).filter(p =>
        p.nombre.toLowerCase().includes(ql) || p.codigo.toLowerCase().includes(ql)
    ).slice(0, 8);

    const lista = document.getElementById('listaResultados');
    lista.innerHTML = '';

    if (!resultados.length) {
        lista.innerHTML = '<div style="padding:14px;text-align:center;color:var(--text-muted);font-size:13px">Sin resultados</div>';
    } else {
        resultados.forEach(p => {
            const el = document.createElement('div');
            el.style.cssText = 'display:flex;align-items:center;justify-content:space-between;padding:10px 14px;border-bottom:1px solid #f1f5f9;cursor:pointer;transition:background .15s';
            el.innerHTML = `
                <div>
                    <div style="font-size:13.5px;font-weight:600">${p.nombre}</div>
                    <div style="font-size:11px;color:var(--text-muted)">${p.codigo} · Stock: <strong style="color:${p.stock<=5?'var(--danger)':'var(--success)'}">${p.stock}</strong></div>
                </div>
                <div style="text-align:right">
                    <div style="font-weight:700;color:var(--accent)">S/ ${parseFloat(p.precio_venta).toFixed(2)}</div>
                    ${p.meses_garantia>0 ? `<div style="font-size:10px;color:var(--text-muted)">${p.meses_garantia} meses garantía</div>` : ''}
                </div>`;
            el.addEventListener('mouseenter', () => el.style.background = '#f8fafc');
            el.addEventListener('mouseleave', () => el.style.background = '');
            el.addEventListener('click', () => agregarProducto(p.id));
            lista.appendChild(el);
        });
    }
    document.getElementById('resultadosBusqueda').style.display = 'block';
}

// ── Agregar al carrito ────────────────────────────────────────────────
function agregarProducto(id) {
    const p = productosData[id];
    if (!p) return;

    const cantidad = parseInt(document.getElementById('cantidadInput').value) || 1;

    if (carrito[id]) {
        const nuevaCant = carrito[id].cantidad + cantidad;
        if (nuevaCant > p.stock) {
            showToast(`Stock insuficiente. Disponible: ${p.stock}`, 'warning');
            return;
        }
        carrito[id].cantidad = nuevaCant;
    } else {
        if (cantidad > p.stock) {
            showToast(`Stock insuficiente. Disponible: ${p.stock}`, 'warning');
            return;
        }
        carrito[id] = { producto_id: id, nombre: p.nombre, codigo: p.codigo, precio_unitario: parseFloat(p.precio_venta), cantidad: cantidad, stock: p.stock };
    }

    document.getElementById('buscarProducto').value = '';
    document.getElementById('resultadosBusqueda').style.display = 'none';
    document.getElementById('cantidadInput').value = 1;

    renderCarrito();
    showToast(`"${p.nombre}" agregado al carrito`, 'success');
}

// ── Cambiar cantidad ──────────────────────────────────────────────────
function cambiarCantidad(id, delta) {
    if (!carrito[id]) return;
    const nueva = carrito[id].cantidad + delta;
    if (nueva <= 0) { eliminarItem(id); return; }
    if (nueva > carrito[id].stock) { showToast('Stock insuficiente', 'warning'); return; }
    carrito[id].cantidad = nueva;
    renderCarrito();
}

// ── Eliminar item ─────────────────────────────────────────────────────
function eliminarItem(id) {
    delete carrito[id];
    renderCarrito();
}

// ── Render carrito ────────────────────────────────────────────────────
function renderCarrito() {
    const items = Object.values(carrito);
    const cont  = document.getElementById('carritoItems');
    const vacio = document.getElementById('carritoVacio');

    vacio.style.display = items.length ? 'none' : 'block';
    cont.innerHTML = '';

    let total = 0;

    items.forEach(item => {
        const sub = item.precio_unitario * item.cantidad;
        total += sub;
        const div = document.createElement('div');
        div.className = 'carrito-item';
        div.style.gridTemplateColumns = '1fr auto auto auto';
        div.innerHTML = `
            <div>
                <div style="font-size:13.5px;font-weight:600">${item.nombre}</div>
                <div style="font-size:11px;color:var(--text-muted)">${item.codigo} · S/ ${item.precio_unitario.toFixed(2)} c/u</div>
            </div>
            <div style="display:flex;align-items:center;gap:6px">
                <button type="button" onclick="cambiarCantidad(${item.producto_id},-1)"
                    style="width:26px;height:26px;border-radius:50%;border:1.5px solid var(--border);background:none;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;color:var(--text-muted)">-</button>
                <span style="font-weight:700;min-width:20px;text-align:center">${item.cantidad}</span>
                <button type="button" onclick="cambiarCantidad(${item.producto_id},1)"
                    style="width:26px;height:26px;border-radius:50%;border:1.5px solid var(--border);background:none;cursor:pointer;font-size:14px;display:flex;align-items:center;justify-content:center;color:var(--text-muted)">+</button>
            </div>
            <div style="font-weight:700;color:var(--accent);min-width:70px;text-align:right">S/ ${sub.toFixed(2)}</div>
            <button type="button" onclick="eliminarItem(${item.producto_id})"
                style="background:none;border:none;cursor:pointer;color:var(--danger);font-size:16px;padding:4px">
                <i class="bi bi-trash3"></i>
            </button>`;
        cont.appendChild(div);
    });

    const subtotal = total / 1.18;
    const igv      = total - subtotal;

    document.getElementById('resSubtotal').textContent = 'S/ ' + subtotal.toFixed(2);
    document.getElementById('resIgv').textContent      = 'S/ ' + igv.toFixed(2);
    document.getElementById('resTotal').textContent    = 'S/ ' + total.toFixed(2);
    document.getElementById('totalItems').textContent  = items.length + ' item' + (items.length !== 1 ? 's' : '');

    if (total > 0) {
        document.getElementById('totalLetrasDisplay').textContent = 'Son: ' + numeroALetras(total);
    } else {
        document.getElementById('totalLetrasDisplay').textContent = '';
    }

    document.getElementById('btnConfirmar').disabled = items.length === 0;

    // Sync hidden inputs
    document.getElementById('hiddenTotal').value        = total.toFixed(2);
    document.getElementById('hiddenTotalLetras').value  = numeroALetras(total);
    document.getElementById('hiddenItems').value        = JSON.stringify(items.map(i => ({
        producto_id:     i.producto_id,
        cantidad:        i.cantidad,
        precio_unitario: i.precio_unitario
    })));
}

// ── Convertir número a letras (básico) ───────────────────────────────
function numeroALetras(num) {
    const unidades = ['','UNO','DOS','TRES','CUATRO','CINCO','SEIS','SIETE','OCHO','NUEVE',
                      'DIEZ','ONCE','DOCE','TRECE','CATORCE','QUINCE','DIECISÉIS','DIECISIETE','DIECIOCHO','DIECINUEVE'];
    const decenas  = ['','','VEINTE','TREINTA','CUARENTA','CINCUENTA','SESENTA','SETENTA','OCHENTA','NOVENTA'];
    const centenas = ['','CIENTO','DOSCIENTOS','TRESCIENTOS','CUATROCIENTOS','QUINIENTOS','SEISCIENTOS','SETECIENTOS','OCHOCIENTOS','NOVECIENTOS'];

    function decToWords(n) {
        if (n < 20) return unidades[n];
        const d = Math.floor(n / 10), u = n % 10;
        return decenas[d] + (u ? ' Y ' + unidades[u] : '');
    }
    function centToWords(n) {
        if (n === 100) return 'CIEN';
        if (n < 100) return decToWords(n);
        return centenas[Math.floor(n/100)] + (n%100 ? ' ' + decToWords(n%100) : '');
    }

    const partes    = num.toFixed(2).split('.');
    const entero    = parseInt(partes[0]);
    const centavos  = parseInt(partes[1]);

    let texto = '';
    if (entero === 0) texto = 'CERO';
    else if (entero < 1000) texto = centToWords(entero);
    else {
        const miles = Math.floor(entero / 1000);
        const resto = entero % 1000;
        texto = (miles === 1 ? 'MIL' : centToWords(miles) + ' MIL') + (resto ? ' ' + centToWords(resto) : '');
    }

    return `${texto} Y ${String(centavos).padStart(2,'0')}/100 SOLES`;
}

// ── Submit: validar antes de enviar ──────────────────────────────────
document.getElementById('formVenta').addEventListener('submit', function(e) {
    const items = Object.values(carrito);
    if (!items.length) {
        e.preventDefault();
        showToast('Agrega al menos un producto al carrito', 'error');
        return;
    }
    if (!document.getElementById('clienteSelect').value) {
        e.preventDefault();
        showToast('Selecciona un cliente', 'error');
        return;
    }
    if (!document.getElementById('tipoComprobante').value) {
        e.preventDefault();
        showToast('Selecciona el tipo de comprobante', 'error');
        return;
    }
});
</script>
@endpush
