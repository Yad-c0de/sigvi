<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Venta;
use App\Models\Cliente;
use App\Models\Producto;
use App\Models\Serie;
use App\Services\VentaService;
use Illuminate\Http\Request;
use Exception;

class VentaController extends Controller
{
    protected VentaService $ventaService;

    // Inyectamos el Service en el constructor
    public function __construct(VentaService $ventaService)
    {
        $this->ventaService = $ventaService;
    }

    public function index()
    {
        $ventas = Venta::with(['cliente', 'user'])->latest()->paginate(20);
        return view('ventas.index', compact('ventas'));
    }

    public function create()
    {
        $clientes = Cliente::orderBy('nombre_razon_social')->get();
        $productos = Producto::where('stock', '>', 0)->orderBy('nombre')->get();
        $series = Serie::all();

        return view('ventas.create', compact('clientes', 'productos', 'series'));
    }

    public function store(Request $request)
    {
        $datos = $request->validate([
            'cliente_id'       => 'required|exists:clientes,id',
            'tipo_comprobante' => 'required|in:Boleta,Factura',
            'forma_pago'       => 'required|string',
            'total'            => 'required|numeric',
            'total_letras'     => 'required|string',
            'items'            => 'required|string',
        ]);

        $items = json_decode($datos['items'], true);

        if (!is_array($items) || count($items) < 1) {
            return back()->withInput()->with('error', 'Debes agregar al menos un producto al carrito.');
        }

        foreach ($items as $index => $item) {
            if (empty($item['producto_id']) || empty($item['cantidad']) || empty($item['precio_unitario'])) {
                return back()->withInput()->with('error', "El producto en la posición ".($index+1)." tiene datos incompletos.");
            }

            $producto = Producto::find($item['producto_id']);
            if (!$producto) {
                return back()->withInput()->with('error', "El producto seleccionado no existe.");
            }
            if ($producto->stock < $item['cantidad']) {
                return back()->withInput()->with('error', "Stock insuficiente para «{$producto->nombre}». Disponible: {$producto->stock}, solicitado: {$item['cantidad']}.");
            }
        }

        try {
            $venta = $this->ventaService->registrarVenta(
                array_merge($datos, ['items' => $items])
            );
            return redirect()->route('ventas.show', $venta)
                             ->with('success', 'Venta #' . $venta->correlativo . ' registrada con éxito.');
        } catch (Exception $e) {
            return back()->withInput()->with('error', $e->getMessage());
        }
    }

    public function show(Venta $venta)
    {
        $venta->load(['cliente', 'user', 'detalles.producto', 'garantias']);
        return view('ventas.show', compact('venta'));
    }

    public function anular(Request $request, Venta $venta)
    {
        $request->validate(['motivo' => 'required|string|min:10']);

        try {
            $this->ventaService->anularVenta($venta, $request->motivo);
            return redirect()->route('ventas.show', $venta)
                             ->with('success', 'Venta anulada y stock restaurado.');
        } catch (Exception $e) {
            return back()->with('error', $e->getMessage());
        }
    }
}
