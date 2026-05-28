<?php

namespace App\Http\Controllers;

use App\Models\Marca;
use Illuminate\Http\Request;

class MarcaController extends Controller
{
    public function index()
    {
        $marcas = Marca::withCount('productos')->orderBy('nombre')->paginate(20);
        return view('marcas.index', compact('marcas'));
    }

    public function store(Request $request)
    {
        $request->validate(['nombre' => ['required', 'string', 'max:100', 'unique:marcas,nombre']]);
        Marca::create($request->only('nombre'));
        return back()->with('success', 'Marca creada correctamente.');
    }

    public function update(Request $request, Marca $marca)
    {
        $request->validate(['nombre' => ['required', 'string', 'max:100', 'unique:marcas,nombre,' . $marca->id]]);
        $marca->update($request->only('nombre'));
        return back()->with('success', 'Marca actualizada.');
    }

    public function destroy(Marca $marca)
    {
        if ($marca->productos()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene productos asociados.');
        }
        $marca->delete();
        return back()->with('success', 'Marca eliminada.');
    }
}
