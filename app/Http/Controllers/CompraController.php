<?php
// =========================================================
// CompraController.php
// =========================================================
namespace App\Http\Controllers;

use App\Models\Compra;
use App\Models\DetalleCompra;
use App\Models\Proveedor;
use App\Models\Producto;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CompraController extends Controller
{
    public function index()
    {
        $compras    = Compra::with('proveedor')->latest('fecha_compra')->paginate(20);
        $totalItems = Compra::count();

        return view('compras.index', compact('compras', 'totalItems'));
    }

    public function create()
    {
        $proveedores = Proveedor::orderBy('nombre_comercial')->get();
        $productos   = Producto::with(['categoria', 'marca'])->orderBy('nombre')->get();

        return view('compras.create', compact('proveedores', 'productos'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'proveedor_id'      => ['required', 'exists:proveedores,id'],
            'comprobante_numero'=> ['required', 'string', 'max:50'],
            'fecha_compra'      => ['required', 'date'],
            'items'             => ['required', 'array', 'min:1'],
            'items.*.producto_id'   => ['required', 'exists:productos,id'],
            'items.*.cantidad'      => ['required', 'integer', 'min:1'],
            'items.*.precio_compra' => ['required', 'numeric', 'min:0.01'],
        ]);

        DB::transaction(function () use ($validated) {
            $total = collect($validated['items'])
                        ->sum(fn($i) => $i['cantidad'] * $i['precio_compra']);

            $compra = Compra::create([
                'proveedor_id'      => $validated['proveedor_id'],
                'comprobante_numero'=> $validated['comprobante_numero'],
                'fecha_compra'      => $validated['fecha_compra'],
                'total'             => round($total, 2),
            ]);

            foreach ($validated['items'] as $item) {
                DetalleCompra::create([
                    'compra_id'    => $compra->id,
                    'producto_id'  => $item['producto_id'],
                    'cantidad'     => $item['cantidad'],
                    'precio_compra'=> $item['precio_compra'],
                ]);

                // Incrementar stock del producto
                Producto::find($item['producto_id'])->increment('stock', $item['cantidad']);
            }
        });

        return redirect()->route('compras.index')->with('success', 'Compra registrada y stock actualizado.');
    }

    public function show(Compra $compra)
    {
        $compra->load(['proveedor', 'detalles.producto']);
        return view('compras.show', compact('compra'));
    }
}
