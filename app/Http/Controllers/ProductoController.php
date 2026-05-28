<?php

namespace App\Http\Controllers;

use App\Models\Producto;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProductoController extends Controller
{
    public function index()
    {
        $productos   = Producto::with(['categoria', 'marca', 'proveedor'])->orderBy('nombre')->paginate(20);
        $categorias  = Categoria::orderBy('nombre')->get();
        $marcas      = Marca::orderBy('nombre')->get();
        $proveedores = Proveedor::orderBy('nombre_comercial')->get();
        $totalItems  = Producto::count();

        return view('productos.index', compact('productos', 'categorias', 'marcas', 'proveedores', 'totalItems'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'codigo'              => ['nullable', 'string', 'max:50', 'unique:productos,codigo'],
            'nombre'              => ['required', 'string', 'max:255'],
            'descripcion_tecnica' => ['nullable', 'string'],
            'foto'                => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'categoria_id'        => ['required', 'exists:categorias,id'],
            'marca_id'            => ['required', 'exists:marcas,id'],
            'proveedor_id'        => ['nullable', 'exists:proveedores,id'],
            'precio_costo'        => ['required', 'numeric', 'min:0'],
            'precio_venta'        => ['required', 'numeric', 'min:0.01', 'gte:precio_costo'],
            'stock'               => ['required', 'integer', 'min:0'],
            'stock_minimo'        => ['required', 'integer', 'min:0'],
            'alertar_stock'       => ['nullable', 'boolean'],
        ]);

        // Generación automática de código SKU si se deja vacío
        if (empty($validated['codigo'])) {
            $categoria = Categoria::find($validated['categoria_id']);
            $prefijo = $categoria->prefijo ?? 'PROD';
            $ultimo = Producto::where('codigo', 'like', $prefijo . '-%')->orderBy('id', 'desc')->first();
            $numero = $ultimo ? intval(explode('-', $ultimo->codigo)[1]) + 1 : 1;
            $validated['codigo'] = $prefijo . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
        }

        // Almacenamiento de imagen
        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('productos', 'public');
            $validated['foto'] = $path;
        }

        // Forzar valor correcto de alertar_stock
        $validated['alertar_stock'] = $request->has('alertar_stock') && $request->input('alertar_stock') == '1' ? 1 : 0;

        Producto::create($validated);
        return back()->with('success', 'Producto registrado correctamente.');
    }

    public function update(Request $request, Producto $producto)
    {
        $validated = $request->validate([
            'codigo'              => ['nullable', 'string', 'max:50', 'unique:productos,codigo,' . $producto->id],
            'nombre'              => ['required', 'string', 'max:255'],
            'descripcion_tecnica' => ['nullable', 'string'],
            'foto'                => ['nullable', 'image', 'mimes:jpg,jpeg,png', 'max:2048'],
            'categoria_id'        => ['required', 'exists:categorias,id'],
            'marca_id'            => ['required', 'exists:marcas,id'],
            'proveedor_id'        => ['nullable', 'exists:proveedores,id'],
            'precio_costo'        => ['required', 'numeric', 'min:0'],
            'precio_venta'        => ['required', 'numeric', 'min:0.01', 'gte:precio_costo'],
            'stock'               => ['required', 'integer', 'min:0'],
            'stock_minimo'        => ['required', 'integer', 'min:0'],
            'alertar_stock'       => ['nullable', 'boolean'],
        ]);

        if ($request->hasFile('foto')) {
            $path = $request->file('foto')->store('productos', 'public');
            $validated['foto'] = $path;
        }

        $validated['alertar_stock'] = $request->has('alertar_stock') && $request->input('alertar_stock') == '1' ? 1 : 0;

        $producto->update($validated);
        return back()->with('success', 'Producto actualizado correctamente.');
    }

    public function destroy(Producto $producto)
    {
        $producto->delete();
        return back()->with('success', 'Producto eliminado.');
    }

    public function sugerirCodigo(Request $request)
    {
        $request->validate(['categoria_id' => 'required|exists:categorias,id']);
        $categoria = Categoria::find($request->categoria_id);
        $prefijo = $categoria->prefijo ?? 'PROD';
        $ultimo = Producto::where('codigo', 'like', $prefijo . '-%')->orderBy('id', 'desc')->first();
        $numero = $ultimo ? intval(explode('-', $ultimo->codigo)[1]) + 1 : 1;
        $codigoSugerido = $prefijo . '-' . str_pad($numero, 4, '0', STR_PAD_LEFT);
        return response()->json(['codigo' => $codigoSugerido]);
    }
}
