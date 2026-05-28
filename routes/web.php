<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\VentaController;
use App\Http\Controllers\ClienteController;
use App\Http\Controllers\ProductoController;
use App\Http\Controllers\CompraController;
use App\Http\Controllers\ProveedorController;
use App\Http\Controllers\CategoriaController;
use App\Http\Controllers\MarcaController;
use App\Http\Controllers\GarantiaController;
use App\Http\Controllers\EmpresaController;
use App\Http\Controllers\SerieController;
use App\Http\Controllers\Auth\GoogleController;

// Redirigir raíz al dashboard
Route::get('/', fn() => redirect()->route('dashboard'));

// Rutas públicas de Google
Route::get('/auth/google', [GoogleController::class, 'redirect'])->name('auth.google');
Route::get('/auth/google/callback', [GoogleController::class, 'callback']);

// ─── Rutas protegidas por autenticación ──────────────────────────────────────
Route::middleware(['auth'])->group(function () {

    // Dashboard (accesible por ambos roles)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // =================================================================
    // RUTAS ACCESIBLES PARA ADMINISTRADOR Y VENDEDOR
    // =================================================================
    Route::middleware(['role:Admin,Vendedor'])->group(function () {

        // ── VENTAS ──────────────────────────────────────────────────────────
        Route::prefix('ventas')->name('ventas.')->group(function () {
            Route::get('/',             [VentaController::class, 'index'])->name('index');
            Route::get('/crear',        [VentaController::class, 'create'])->name('create');
            Route::post('/',            [VentaController::class, 'store'])->name('store');
            Route::get('/{venta}',      [VentaController::class, 'show'])->name('show');
            Route::get('/api/buscar-producto', [VentaController::class, 'buscarProducto'])->name('buscar-producto');
        });

        // ── CLIENTES ─────────────────────────────────────────────────────────
        Route::resource('clientes', ClienteController::class)->only(['index', 'store', 'update', 'destroy']);

        // ── GARANTÍAS ────────────────────────────────────────────────────────
        Route::resource('garantias', GarantiaController::class)->only(['index', 'store', 'update']);
    });

    // =================================================================
    // RUTAS EXCLUSIVAS PARA ADMINISTRADOR
    // =================================================================
    Route::middleware(['role:Admin'])->group(function () {

        // ── PRODUCTOS ────────────────────────────────────────────────────────
        Route::resource('productos', ProductoController::class)->only(['index', 'store', 'update', 'destroy']);
        Route::get('/productos/sugerir-codigo', [ProductoController::class, 'sugerirCodigo'])->name('productos.sugerirCodigo');

        // ── COMPRAS (actualizar stock) ───────────────────────────────────────
        Route::prefix('compras')->name('compras.')->group(function () {
            Route::get('/',      [CompraController::class, 'index'])->name('index');
            Route::get('/crear', [CompraController::class, 'create'])->name('create');
            Route::post('/',     [CompraController::class, 'store'])->name('store');
            Route::get('/{compra}', [CompraController::class, 'show'])->name('show');
        });

        // ── PROVEEDORES ───────────────────────────────────────────────────────
        Route::resource('proveedores', ProveedorController::class)->only(['index', 'store', 'update', 'destroy']);

        // ── CATEGORÍAS ───────────────────────────────────────────────────────
        Route::resource('categorias', CategoriaController::class)->only(['index', 'store', 'update', 'destroy']);

        // ── MARCAS ───────────────────────────────────────────────────────────
        Route::resource('marcas', MarcaController::class)->only(['index', 'store', 'update', 'destroy']);

        // ── EMPRESA ───────────────────────────────────────────────────────────
        Route::get('/empresa',       [EmpresaController::class, 'index'])->name('empresa.index');
        Route::post('/empresa',      [EmpresaController::class, 'store'])->name('empresa.store');

        // ── SERIES ────────────────────────────────────────────────────────────
        Route::resource('series', SerieController::class)->only(['index', 'store', 'update', 'destroy']);

        // ── ANULACIÓN DE VENTA (solo Administrador) ───────────────────────────
        Route::post('/ventas/{venta}/anular', [VentaController::class, 'anular'])->name('ventas.anular');
    });
});

require __DIR__.'/auth.php';
