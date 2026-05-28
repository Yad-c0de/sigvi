<?php

namespace App\Http\Controllers;

use App\Models\Categoria;
use Illuminate\Http\Request;

class CategoriaController extends Controller
{
    public function index()
    {
        $categorias = Categoria::withCount('productos')->orderBy('nombre')->paginate(20);
        return view('categorias.index', compact('categorias'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'nombre'  => ['required', 'string', 'max:100', 'unique:categorias,nombre'],
            'prefijo' => ['required', 'string', 'max:4', 'unique:categorias,prefijo'],
        ]);

        Categoria::create($request->only('nombre', 'prefijo'));

        return back()->with('success', 'Categoría creada correctamente.');
    }

    public function update(Request $request, Categoria $categoria)
    {
        $request->validate([
            'nombre'  => ['required', 'string', 'max:100', 'unique:categorias,nombre,' . $categoria->id],
            'prefijo' => ['required', 'string', 'max:4', 'unique:categorias,prefijo,' . $categoria->id],
        ]);

        $categoria->update($request->only('nombre', 'prefijo'));

        return back()->with('success', 'Categoría actualizada.');
    }

    public function destroy(Categoria $categoria)
    {
        if ($categoria->productos()->exists()) {
            return back()->with('error', 'No se puede eliminar: tiene productos asociados.');
        }

        $categoria->delete();

        return back()->with('success', 'Categoría eliminada.');
    }
}
