<?php

namespace App\Http\Controllers;

use App\Models\Venta;
use App\Models\Producto;
use App\Models\Cliente;
use App\Models\Garantia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        // Total de ventas completadas (suma y conteo)
        $totalVentas = Venta::where('estado', 'Completada')->selectRaw('SUM(total) as sum, COUNT(*) as count')->first();

        // Total de productos
        $totalProductos = Producto::count();

        // Total de clientes
        $totalClientes = Cliente::count();

        // Productos con stock bajo (stock <= stock_minimo y alertar_stock = true)
        $stockBajo = Producto::where('alertar_stock', true)
                        ->whereRaw('stock <= stock_minimo')
                        ->count();

        // Garantías vigentes
        $garantiasVig = Garantia::where('estado', 'Vigente')->count();

        // Últimas 8 ventas completadas con relaciones
        $ultimasVentas = Venta::where('estado', 'Completada')
                            ->with(['cliente', 'user'])
                            ->latest()
                            ->limit(8)
                            ->get();

        // Productos con stock bajo para mostrar en la tabla
        $stockAlerta = Producto::where('alertar_stock', true)
                            ->whereRaw('stock <= stock_minimo')
                            ->with('categoria')
                            ->orderBy('stock', 'asc')
                            ->limit(6)
                            ->get();

        return view('dashboard', compact(
            'totalVentas',
            'totalProductos',
            'totalClientes',
            'stockBajo',
            'garantiasVig',
            'ultimasVentas',
            'stockAlerta'
        ));
    }
}
