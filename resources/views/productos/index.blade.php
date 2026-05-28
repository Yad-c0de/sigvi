@extends('layouts.app')

@section('title', 'Productos')
@section('page-icon')<i class="bi bi-boxes text-primary"></i>@endsection
@section('subtitle', 'Inventario · ' . $totalItems . ' productos')

@section('topbar-actions')
    <div class="search-bar d-none d-md-flex">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInput" placeholder="Buscar producto...">
    </div>
    <button class="btn btn-primary" onclick="abrirNuevoProducto()">
        <i class="bi bi-plus-circle-fill"></i>
        <span>Nuevo Producto</span>
    </button>
@endsection

@section('content')
<div class="d-md-none mb-3">
    <div class="search-bar w-100">
        <i class="bi bi-search search-icon"></i>
        <input type="text" id="searchInputMobile" placeholder="Buscar producto..." style="width:100%">
    </div>
</div>

<div class="card">
    <div class="table-wrapper">
        <table id="mainTable">
            <thead>
                <tr>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Categoría</th>
                    <th>Marca</th>
                    <th>Costo</th>
                    <th>Venta</th>
                    <th style="text-align:center">Stock</th>
                    <th style="text-align:center">Mínimo</th>
                    <th style="text-align:center">Alerta</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($productos as $p)
                <tr>
                    <td><code style="background:#f1f5f9;padding:2px 7px;border-radius:5px;font-size:12px">{{ $p->codigo }}</code></td>
                    <td><strong>{{ $p->nombre }}</strong></td>
                    <td><span style="font-size:12px;color:var(--text-muted)">{{ $p->categoria?->nombre }}</span></td>
                    <td><span style="font-size:12px;color:var(--text-muted)">{{ $p->marca?->nombre }}</span></td>
                    <td>S/ {{ number_format($p->precio_costo, 2) }}</td>
                    <td><strong style="color:var(--accent)">S/ {{ number_format($p->precio_venta, 2) }}</strong></td>
                    <td style="text-align:center">
                        <span class="{{ $p->stock === 0 ? 'stock-out' : ($p->stock <= $p->stock_minimo ? 'stock-low' : 'stock-ok') }}">
                            {{ $p->stock }}
                        </span>
                    </td>
                    <td style="text-align:center">{{ $p->stock_minimo }}</td>
                    <td style="text-align:center">
                        @if($p->alertar_stock)
                            <i class="bi bi-bell-fill text-warning" style="font-size: 16px;" title="Alerta activada"></i>
                        @else
                            <i class="bi bi-bell-slash text-muted" style="font-size: 16px;" title="Alerta desactivada"></i>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            <button class="btn-action edit" title="Editar"
                                    onclick="editarProducto({{ $p->id }}, '{{ addslashes($p->codigo) }}', '{{ addslashes($p->nombre) }}', {{ $p->categoria_id }}, {{ $p->marca_id }}, {{ $p->precio_costo }}, {{ $p->precio_venta }}, {{ $p->stock }}, {{ $p->stock_minimo }}, {{ $p->proveedor_id ?? 'null' }}, '{{ addslashes($p->descripcion_tecnica ?? '') }}', '{{ $p->foto ?? '' }}', {{ $p->alertar_stock ? 'true' : 'false' }})">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <form method="POST" action="{{ route('productos.destroy', $p) }}" class="d-inline" id="delProd{{ $p->id }}">
                                @csrf @method('DELETE')
                                <button type="button" class="btn-action delete" title="Eliminar"
                                        onclick="confirmDelete(document.getElementById('delProd{{ $p->id }}'), '{{ addslashes($p->nombre) }}')">
                                    <i class="bi bi-trash3"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:40px;color:var(--text-muted)">
                        <i class="bi bi-boxes" style="font-size:32px;display:block;margin-bottom:10px"></i>
                        Sin productos registrados.
                    </td>
                <tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($productos->hasPages())
    <div class="card-body" style="border-top:1px solid var(--border);padding:12px 20px">
        {{ $productos->links() }}
    </div>
    @endif
</div>

{{-- Modal Nuevo / Editar Producto --}}
<div class="sigvi-modal-overlay" id="modalProducto" data-close-fn="cerrarModalProducto">
    <div class="sigvi-modal modal-lg" style="max-width: 1100px;">
        <div class="sigvi-modal-header">
            <h4 id="modalProductoTitulo"><i class="bi bi-boxes text-primary"></i> Nuevo Producto</h4>
            <button class="sigvi-modal-close" onclick="cerrarModalProducto()">&times;</button>
        </div>
        <form method="POST" id="formProducto" action="{{ route('productos.store') }}" enctype="multipart/form-data" novalidate>
            @csrf
            <input type="hidden" name="_method" id="metodoProd" value="POST">
            <div class="sigvi-modal-body" style="max-height: 70vh; overflow-y: auto;">
                {{-- PRIMERA FILA: información general + foto --}}
                <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="card" style="margin:0;">
                        <div class="card-header">
                            <h3><i class="bi bi-card-text"></i> Información General</h3>
                        </div>
                        <div class="card-body">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div class="form-group">
                                    <label class="form-label">CÓDIGO ÚNICO (SKU)</label>
                                    <input type="text" name="codigo" id="prodCodigo" class="form-control" placeholder="Se generará automáticamente" readonly>
                                    <small class="text-muted">Se genera al seleccionar categoría.</small>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">CATEGORÍA <span class="req">*</span></label>
                                    <select name="categoria_id" id="prodCategoria" class="form-select" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($categorias as $c)
                                            <option value="{{ $c->id }}">{{ $c->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="errorCategoria">Seleccione una categoría.</div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">NOMBRE DEL PRODUCTO <span class="req">*</span></label>
                                <input type="text" name="nombre" id="prodNombre" class="form-control" required placeholder="Ejemplo: Filtro de Aceite Sintético Premium">
                                <div class="invalid-feedback" id="errorNombre">Ingrese el nombre del producto.</div>
                            </div>
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                <div class="form-group">
                                    <label class="form-label">MARCA <span class="req">*</span></label>
                                    <select name="marca_id" id="prodMarca" class="form-select" required>
                                        <option value="">Seleccione...</option>
                                        @foreach($marcas as $m)
                                            <option value="{{ $m->id }}">{{ $m->nombre }}</option>
                                        @endforeach
                                    </select>
                                    <div class="invalid-feedback" id="errorMarca">Seleccione una marca.</div>
                                </div>
                                <div class="form-group">
                                    <label class="form-label">DESCRIPCIÓN TÉCNICA</label>
                                    <textarea name="descripcion_tecnica" id="prodDescripcion" class="form-control" rows="3" placeholder="Detalles sobre compatibilidad, dimensiones..."></textarea>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="card" style="margin:0;">
                        <div class="card-body" style="display: flex; flex-direction: column; justify-content: center; align-items: center; background: #f8fafc; border: 2px dashed var(--border); border-radius: var(--radius); min-height: 250px;">
                            <i class="bi bi-camera" style="font-size: 40px; color: var(--text-muted); margin-bottom: 10px;"></i>
                            <span class="form-label" style="text-align: center; color: var(--text-muted);">Subir fotografía del producto</span>
                            <input type="file" name="foto" id="prodFoto" class="form-control" style="margin-top: 15px;" accept="image/png, image/jpeg">
                            <img id="previewFoto" src="#" alt="Vista previa" style="max-width: 100%; max-height: 120px; margin-top: 10px; display: none; border-radius: 8px;">
                            <small class="text-muted" style="margin-top: 10px; font-size: 11px; text-align: center;">Recomendado: 800x800px,<br>formato JPG o PNG.</small>
                        </div>
                    </div>
                </div>

                {{-- SEGUNDA FILA: precios + inventario --}}
                <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div class="card" style="margin:0;">
                        <div class="card-header">
                            <h3><i class="bi bi-cash-stack"></i> Gestión de Precios</h3>
                        </div>
                        <div class="card-body">
                            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                                {{-- Precio Costo --}}
                                <div class="form-group">
                                    <label class="form-label">PRECIO DE COSTO</label>
                                    <div class="search-bar">
                                        <span class="search-icon" style="color: var(--text);">S/</span>
                                        <input type="number" step="0.01" min="0" name="precio_costo" id="prodCosto" class="form-control" style="padding-left: 30px;" value="0.00" required
                                               onkeydown="if(event.key === '-') event.preventDefault()"
                                               oninput="if(this.value < 0) this.value = 0">
                                    </div>
                                    <div class="invalid-feedback">El precio de costo no puede ser negativo.</div>
                                </div>

                                {{-- Precio Venta --}}
                                <div class="form-group">
                                    <label class="form-label">PRECIO DE VENTA <span class="req">*</span></label>
                                    <div class="search-bar">
                                        <span class="search-icon" style="color: var(--text);">S/</span>
                                        <input type="number" step="0.01" min="0.01" name="precio_venta" id="prodPrecio" class="form-control" style="padding-left: 30px;" value="0.00" required
                                               onkeydown="if(event.key === '-') event.preventDefault()"
                                               oninput="if(this.value < 0.01) this.value = 0.01">
                                    </div>
                                    <div class="invalid-feedback" id="errorPrecioVenta">El precio de venta debe ser mayor o igual al costo.</div>
                                </div>
                            </div>
                            <div style="background: var(--bg); padding: 12px; border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center; margin-top: 10px;">
                                <span style="font-size: 13px; font-weight: 600; color: var(--text-muted);">Margen de utilidad proyectado:</span>
                                <span id="margenUtilidad" style="font-size: 16px; font-weight: 800; color: var(--info);">0.00%</span>
                            </div>
                        </div>
                    </div>

                    <div class="card" style="margin:0;">
                        <div class="card-header">
                            <h3><i class="bi bi-box-seam"></i> Stock Inicial</h3>
                        </div>
                        <div class="card-body">
                            <div class="form-group">
                                <label class="form-label">STOCK INICIAL</label>
                                <input type="number" step="1" min="0" name="stock" id="prodStock" class="form-control" value="0" required
                                       onkeydown="if(event.key === '-') event.preventDefault()"
                                       oninput="if(this.value < 0) this.value = 0">
                                <div class="invalid-feedback">El stock no puede ser negativo.</div>
                            </div>
                            <div class="form-group">
                                <label class="form-label">STOCK MÍNIMO (ALERTA)</label>
                                <input type="number" step="1" min="0" name="stock_minimo" id="prodStockMinimo" class="form-control" value="5" required
                                       onkeydown="if(event.key === '-') event.preventDefault()"
                                       oninput="if(this.value < 0) this.value = 0">
                                <div class="invalid-feedback">El stock mínimo no puede ser negativo.</div>
                            </div>
                            <div class="form-group" style="margin-bottom: 0;">
                                <label class="form-label">ALERTAR CUANDO LLEGUE AL MÍNIMO</label>
                                <div class="form-check form-switch">
                                    <input type="hidden" name="alertar_stock" value="0">
                                    <input class="form-check-input" type="checkbox" name="alertar_stock" id="alertarStock" value="1" {{ old('alertar_stock', true) ? 'checked' : '' }} style="width: 40px; height: 20px;">
                                    <label class="form-check-label" for="alertarStock" style="font-size: 13px;">Activar notificación de stock bajo</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- TERCERA FILA: proveedor y botones --}}
                <div class="card" style="background: var(--primary); color: #fff; margin-bottom: 0;">
                    <div class="card-body" style="display: flex; align-items: center; justify-content: space-between; padding: 16px 20px;">
                        <div style="display: flex; align-items: center; gap: 15px;">
                            <div style="background: rgba(255,255,255,0.1); width: 40px; height: 40px; border-radius: 8px; display: flex; align-items: center; justify-content: center; font-size: 20px;">
                                <i class="bi bi-truck"></i>
                            </div>
                            <div>
                                <h4 style="margin: 0; font-size: 15px; font-weight: 700;">Asignar Proveedor Principal</h4>
                                <p style="margin: 0; font-size: 12px; color: rgba(255,255,255,0.6);">Vincule este producto a un proveedor para órdenes automáticas.</p>
                            </div>
                        </div>
                        <div style="display: flex; gap: 10px; align-items: center;">
                            <select name="proveedor_id" id="prodProveedor" class="form-select" style="background: rgba(0,0,0,0.3); border-color: transparent; color: #fff; min-width: 200px;">
                                <option value="">Sin proveedor asignado</option>
                                @foreach($proveedores as $prov)
                                    <option value="{{ $prov->id }}">{{ $prov->nombre_comercial }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>
                </div>
            </div>
            <div class="sigvi-modal-footer">
                <button type="button" class="btn btn-outline" onclick="cerrarModalProducto()">Cancelar</button>
                <button type="submit" class="btn btn-primary" id="btnProdLabel"><i class="bi bi-check-circle"></i> Guardar Producto</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
// ================================
// 1. Elementos del DOM
// ================================
const form = document.getElementById('formProducto');
const tituloModal = document.getElementById('modalProductoTitulo');
const btnLabel = document.getElementById('btnProdLabel');
const metodo = document.getElementById('metodoProd');

const codigoInput = document.getElementById('prodCodigo');
const categoriaSelect = document.getElementById('prodCategoria');
const nombreInput = document.getElementById('prodNombre');
const marcaSelect = document.getElementById('prodMarca');
const descripcionInput = document.getElementById('prodDescripcion');
const fotoInput = document.getElementById('prodFoto');
const costoInput = document.getElementById('prodCosto');
const precioInput = document.getElementById('prodPrecio');
const stockInput = document.getElementById('prodStock');
const stockMinimoInput = document.getElementById('prodStockMinimo');
const alertarStockCheck = document.getElementById('alertarStock');
const proveedorSelect = document.getElementById('prodProveedor');
const alertaPrecio = document.getElementById('errorPrecioVenta');
const margenSpan = document.getElementById('margenUtilidad');
const previewImg = document.getElementById('previewFoto');

const errorNombre = document.getElementById('errorNombre');
const errorCategoria = document.getElementById('errorCategoria');
const errorMarca = document.getElementById('errorMarca');

// ================================
// 2. Hacer que los ceros desaparezcan al hacer foco (solo precios)
// ================================
function setupClearZeroOnFocus(field) {
    field.addEventListener('focus', function() {
        if (this.value === '0' || this.value === '0.00') {
            this.value = '';
        }
    });
    field.addEventListener('blur', function() {
        if (this.value === '') {
            this.value = '0.00';
        }
        actualizarMargen();
    });
}
setupClearZeroOnFocus(costoInput);
setupClearZeroOnFocus(precioInput);

// ================================
// 3. Validación antes de enviar
// ================================
function validateForm() {
    let isValid = true;

    if (!nombreInput.value.trim()) {
        nombreInput.classList.add('is-invalid');
        errorNombre.style.display = 'block';
        isValid = false;
    } else {
        nombreInput.classList.remove('is-invalid');
        errorNombre.style.display = 'none';
    }

    if (!categoriaSelect.value) {
        categoriaSelect.classList.add('is-invalid');
        errorCategoria.style.display = 'block';
        isValid = false;
    } else {
        categoriaSelect.classList.remove('is-invalid');
        errorCategoria.style.display = 'none';
    }

    if (!marcaSelect.value) {
        marcaSelect.classList.add('is-invalid');
        errorMarca.style.display = 'block';
        isValid = false;
    } else {
        marcaSelect.classList.remove('is-invalid');
        errorMarca.style.display = 'none';
    }

    let costo = parseFloat(costoInput.value) || 0;
    let venta = parseFloat(precioInput.value) || 0;
    if (venta < costo) {
        precioInput.classList.add('is-invalid');
        alertaPrecio.style.display = 'block';
        isValid = false;
    } else {
        precioInput.classList.remove('is-invalid');
        alertaPrecio.style.display = 'none';
    }

    return isValid;
}

// ================================
// 4. Limpiar errores en tiempo real
// ================================
nombreInput.addEventListener('input', function() {
    if (this.value.trim()) {
        this.classList.remove('is-invalid');
        errorNombre.style.display = 'none';
    }
});
categoriaSelect.addEventListener('change', function() {
    if (this.value) {
        this.classList.remove('is-invalid');
        errorCategoria.style.display = 'none';
    }
});
marcaSelect.addEventListener('change', function() {
    if (this.value) {
        this.classList.remove('is-invalid');
        errorMarca.style.display = 'none';
    }
});

// ================================
// 5. Generación de código SKU al cambiar categoría
// ================================
categoriaSelect.addEventListener('change', function() {
    const categoriaId = this.value;
    if (!categoriaId) {
        codigoInput.value = '';
        codigoInput.placeholder = 'Se generará automáticamente';
        return;
    }
    fetch(`/productos/sugerir-codigo?categoria_id=${categoriaId}`)
        .then(res => res.json())
        .then(data => {
            codigoInput.value = data.codigo;
        })
        .catch(err => console.error('Error al generar código', err));
});

// ================================
// 6. Calcular margen y validar precios
// ================================
function actualizarMargen() {
    let costo = parseFloat(costoInput.value) || 0;
    let venta = parseFloat(precioInput.value) || 0;
    if (venta >= costo) {
        precioInput.classList.remove('is-invalid');
        alertaPrecio.style.display = 'none';
        let margen = costo === 0 ? 0 : ((venta - costo) / costo * 100);
        margenSpan.textContent = margen.toFixed(2) + '%';
        margenSpan.style.color = margen < 0 ? 'var(--danger)' : 'var(--success)';
    } else {
        precioInput.classList.add('is-invalid');
        alertaPrecio.style.display = 'block';
        margenSpan.textContent = '0.00%';
        margenSpan.style.color = 'var(--danger)';
    }
}
costoInput.addEventListener('input', actualizarMargen);
precioInput.addEventListener('input', actualizarMargen);

// ================================
// 7. Vista previa de imagen
// ================================
fotoInput.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file) {
        const reader = new FileReader();
        reader.onload = function(ev) {
            previewImg.src = ev.target.result;
            previewImg.style.display = 'block';
        };
        reader.readAsDataURL(file);
    } else {
        previewImg.style.display = 'none';
        previewImg.src = '#';
    }
});

// ================================
// 8. Abrir nuevo producto
// ================================
function abrirNuevoProducto() {
    form.reset();
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
    metodo.value = 'POST';
    form.action = '{{ route("productos.store") }}';
    tituloModal.innerHTML = '<i class="bi bi-boxes text-primary"></i> Nuevo Producto';
    btnLabel.innerHTML = '<i class="bi bi-check-circle"></i> Guardar Producto';
    codigoInput.value = '';
    codigoInput.placeholder = 'Se generará automáticamente';
    costoInput.value = '0.00';
    precioInput.value = '0.00';
    previewImg.style.display = 'none';
    previewImg.src = '#';
    alertarStockCheck.checked = true;
    actualizarMargen();
    openModal('modalProducto');
}

// ================================
// 9. Editar producto
// ================================
function editarProducto(id, codigo, nombre, catId, marcaId, costo, precio, stock, stockMinimo, proveedorId, descripcion, fotoActual, alertarStock) {
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
    tituloModal.innerHTML = '<i class="bi bi-pencil text-primary"></i> Editar Producto';
    btnLabel.innerHTML = '<i class="bi bi-check-circle"></i> Actualizar Producto';
    metodo.value = 'PUT';
    form.action = `/productos/${id}`;
    codigoInput.value = codigo;
    nombreInput.value = nombre;
    categoriaSelect.value = catId;
    marcaSelect.value = marcaId;
    costoInput.value = costo;
    precioInput.value = precio;
    stockInput.value = stock;
    stockMinimoInput.value = stockMinimo;
    alertarStockCheck.checked = alertarStock === true || alertarStock === '1';
    if (proveedorId) proveedorSelect.value = proveedorId;
    else proveedorSelect.value = '';
    descripcionInput.value = descripcion || '';
    if (fotoActual) {
        previewImg.src = `/storage/${fotoActual}`;
        previewImg.style.display = 'block';
    } else {
        previewImg.style.display = 'none';
        previewImg.src = '#';
    }
    actualizarMargen();
    openModal('modalProducto');
}

// ================================
// 10. Cerrar modal
// ================================
function cerrarModalProducto() {
    form.reset();
    document.querySelectorAll('.is-invalid').forEach(el => el.classList.remove('is-invalid'));
    document.querySelectorAll('.invalid-feedback').forEach(el => el.style.display = 'none');
    metodo.value = 'POST';
    form.action = '{{ route("productos.store") }}';
    tituloModal.innerHTML = '<i class="bi bi-boxes text-primary"></i> Nuevo Producto';
    btnLabel.innerHTML = '<i class="bi bi-check-circle"></i> Guardar Producto';
    codigoInput.value = '';
    codigoInput.placeholder = 'Se generará automáticamente';
    costoInput.value = '0.00';
    precioInput.value = '0.00';
    previewImg.style.display = 'none';
    previewImg.src = '#';
    alertarStockCheck.checked = true;
    actualizarMargen();
    closeModal('modalProducto');
}

// ================================
// 11. Prevenir envío si hay errores
// ================================
form.addEventListener('submit', function(e) {
    if (!validateForm()) {
        e.preventDefault();
    }
});

// ================================
// 12. Recuperar valores si hay errores del servidor
// ================================
@if($errors->any())
    document.addEventListener('DOMContentLoaded', function() {
        @php
            $old = session()->getOldInput();
        @endphp
        metodo.value = 'POST';
        form.action = '{{ route("productos.store") }}';
        tituloModal.innerHTML = '<i class="bi bi-boxes text-primary"></i> Nuevo Producto';
        btnLabel.innerHTML = '<i class="bi bi-check-circle"></i> Guardar Producto';
        codigoInput.value = '{{ old('codigo') }}';
        nombreInput.value = '{{ old('nombre') }}';
        categoriaSelect.value = '{{ old('categoria_id') }}';
        marcaSelect.value = '{{ old('marca_id') }}';
        costoInput.value = '{{ old('precio_costo', 0) }}';
        precioInput.value = '{{ old('precio_venta', 0) }}';
        stockInput.value = '{{ old('stock', 0) }}';
        stockMinimoInput.value = '{{ old('stock_minimo', 5) }}';
        @php
            $alertarStockOld = old('alertar_stock', true);
        @endphp
        alertarStockCheck.checked = {{ $alertarStockOld ? 'true' : 'false' }};
        proveedorSelect.value = '{{ old('proveedor_id') }}';
        descripcionInput.value = '{{ old('descripcion_tecnica') }}';
        actualizarMargen();
        openModal('modalProducto');
        @foreach($errors->all() as $error)
            showToast('{{ $error }}', 'error');
        @endforeach
    });
@endif
</script>
@endpush
