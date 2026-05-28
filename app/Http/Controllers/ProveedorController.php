<?php

namespace App\Http\Controllers;

use App\Models\Proveedor;
use Illuminate\Http\Request;

class ProveedorController extends Controller
{
    public function index()
    {
        $proveedores = Proveedor::withCount('compras')->orderBy('nombre_comercial')->paginate(20);
        return view('proveedores.index', compact('proveedores'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ruc'             => ['required', 'string', 'size:11', 'unique:proveedores,ruc'],
            'nombre_comercial'=> ['required', 'string', 'max:255'],
            'contacto_nombre' => ['nullable', 'string', 'max:255'],
            'telefono'        => ['nullable', 'string', 'max:20'],
        ]);

        Proveedor::create($validated);
        return back()->with('success', 'Proveedor registrado correctamente.');
    }

    public function update(Request $request, Proveedor $proveedor)
    {
        $validated = $request->validate([
            'ruc'             => ['required', 'string', 'size:11', 'unique:proveedores,ruc,' . $proveedor->id],
            'nombre_comercial'=> ['required', 'string', 'max:255'],
            'contacto_nombre' => ['nullable', 'string', 'max:255'],
            'telefono'        => ['nullable', 'string', 'max:20'],
        ]);

        $proveedor->update($validated);
        return back()->with('success', 'Proveedor actualizado correctamente.');
    }

    public function destroy(Proveedor $proveedor)
    {
        $proveedor->delete();
        return back()->with('success', 'Proveedor eliminado.');
    }
}
