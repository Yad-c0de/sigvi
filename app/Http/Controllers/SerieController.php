<?php

namespace App\Http\Controllers;

use App\Models\Serie;
use Illuminate\Http\Request;

class SerieController extends Controller
{
    public function index()
    {
        $series = Serie::orderBy('tipo_comprobante')->paginate(20);
        return view('series.index', compact('series'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'tipo_comprobante'  => ['required', 'in:Boleta,Factura'],
            'serie'             => ['required', 'string', 'size:4'],
            'ultimo_correlativo'=> ['required', 'integer', 'min:0'],
        ]);

        Serie::create($validated);
        return back()->with('success', 'Serie creada correctamente.');
    }

    public function update(Request $request, Serie $serie)
    {
        $validated = $request->validate([
            'tipo_comprobante'  => ['required', 'in:Boleta,Factura'],
            'serie'             => ['required', 'string', 'size:4'],
            'ultimo_correlativo'=> ['required', 'integer', 'min:0'],
        ]);

        $serie->update($validated);
        return back()->with('success', 'Serie actualizada correctamente.');
    }

    public function destroy(Serie $serie)
    {
        $serie->delete();
        return back()->with('success', 'Serie eliminada.');
    }
}
