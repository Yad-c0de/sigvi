<?php

namespace App\Http\Controllers;

use App\Models\Garantia;
use Illuminate\Http\Request;

class GarantiaController extends Controller
{
    public function index()
    {
        $garantias = Garantia::with(['venta.cliente', 'producto'])
                              ->orderByDesc('created_at')
                              ->paginate(20);

        // Auto-actualizar garantías vencidas
        Garantia::where('estado', 'Vigente')
                 ->where('fecha_limite', '<', now()->toDateString())
                 ->update(['estado' => 'Vencida']);

        return view('garantias.index', compact('garantias'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'venta_id'    => ['required', 'exists:ventas,id'],
            'producto_id' => ['required', 'exists:productos,id'],
            'fecha_limite'=> ['required', 'date', 'after:today'],
            'estado'      => ['required', 'in:Vigente,Reclamada,Vencida'],
        ]);

        Garantia::create($validated);
        return back()->with('success', 'Garantía registrada correctamente.');
    }

    public function update(Request $request, Garantia $garantia)
    {
        $validated = $request->validate([
            'estado' => ['required', 'in:Vigente,Reclamada,Vencida'],
        ]);

        $garantia->update($validated);
        return back()->with('success', 'Estado de garantía actualizado.');
    }
}
