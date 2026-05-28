<?php

namespace App\Http\Controllers;

use App\Models\Cliente;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class ClienteController extends Controller
{
    public function index()
    {
        $clientes   = Cliente::withCount('ventas')->orderBy('nombre_razon_social')->paginate(20);
        $totalItems = Cliente::count();

        return view('clientes.index', compact('clientes', 'totalItems'));
    }

    public function store(Request $request)
    {
        $validated = $this->validateCliente($request);
        $validated['estado'] = 'Activo'; // por defecto al crear
        Cliente::create($validated);
        return back()->with('success', 'Cliente registrado correctamente.');
    }

    public function update(Request $request, Cliente $cliente)
    {
        $validated = $this->validateCliente($request, $cliente->id);
        $cliente->update($validated);
        return back()->with('success', 'Cliente actualizado correctamente.');
    }

    public function destroy(Cliente $cliente)
    {
        $cliente->delete();
        return back()->with('success', 'Cliente eliminado.');
    }

    private function validateCliente(Request $request, $ignoreId = null)
    {
        $rules = [
            'tipo_documento'      => ['required', Rule::in(['DNI', 'RUC', 'VARIOS'])],
            'numero_documento'    => ['nullable', 'string', 'max:15'],
            'nombre_razon_social' => ['nullable', 'string', 'max:255'],
            'telefono'            => ['nullable', 'string', 'max:9'],
            'email'               => ['nullable', 'email', 'max:255'],
        ];

        // Si DNI o RUC → obligatoriedad y formato
        if (in_array($request->tipo_documento, ['DNI', 'RUC'])) {
            $rules['numero_documento'][] = 'required';
            $rules['nombre_razon_social'][] = 'required';
            $rules['telefono'][] = 'required';

            if ($request->tipo_documento === 'DNI') {
                $rules['numero_documento'][] = 'digits:8';
            } else {
                $rules['numero_documento'][] = 'digits:11';
            }
        }

        // Formato de teléfono (9 dígitos) si se ingresa
        if ($request->filled('telefono')) {
            $rules['telefono'][] = 'digits:9';
        }

        // Unicidad del número de documento
        $uniqueRule = Rule::unique('clientes', 'numero_documento');
        if ($ignoreId) {
            $uniqueRule->ignore($ignoreId);
        }
        $rules['numero_documento'][] = $uniqueRule;

        // Estado solo se valida si viene en la request (solo en edición)
        if ($request->has('estado')) {
            $rules['estado'] = ['required', Rule::in(['Activo', 'Inactivo'])];
        }

        return $request->validate($rules, [
            'tipo_documento.required'      => 'El tipo de documento es obligatorio.',
            'numero_documento.required'    => 'El número de documento es obligatorio.',
            'numero_documento.digits'      => 'El número de documento debe tener :digits dígitos.',
            'numero_documento.unique'      => 'El número de documento ya está registrado.',
            'nombre_razon_social.required' => 'El nombre o razón social es obligatorio.',
            'telefono.required'            => 'El teléfono es obligatorio.',
            'telefono.digits'              => 'El teléfono debe tener 9 dígitos.',
            'email.email'                  => 'El correo electrónico debe tener un formato válido (ejemplo@dominio.com).',
            'estado.required'              => 'El estado del cliente es obligatorio.',
            'estado.in'                    => 'El estado debe ser Activo o Inactivo.',
        ]);
    }
}
