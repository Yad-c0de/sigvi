<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function index()
    {
        $empresa = Empresa::first();
        return view('empresa.index', compact('empresa'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'ruc'          => ['required', 'string', 'size:11'],
            'razon_social' => ['required', 'string', 'max:255'],
            'direccion'    => ['required', 'string', 'max:255'],
            'distrito'     => ['required', 'string', 'max:100'],
            'ciudad'       => ['required', 'string', 'max:100'],
        ]);

        Empresa::updateOrCreate(['id' => 1], $validated);

        return back()->with('success', 'Datos de empresa guardados correctamente.');
    }
}
