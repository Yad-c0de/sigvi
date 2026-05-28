<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreVentaRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Cambia esto a true para permitir que los usuarios envíen la petición
        return true;
    }

    public function rules(): array
    {
        return [
            // Validaciones para la cabecera
            'cliente_id'       => 'required|exists:clientes,id',
            'tipo_comprobante' => 'required|string|max:20',
            'forma_pago'       => 'required|string',
            'total'            => 'required|numeric|min:0',
            'subtotal'         => 'required|numeric|min:0',
            'total_letras'     => 'required|string',

            // Validaciones para los items (array)
            'items'            => 'required|array|min:1', // Debe tener al menos 1 producto
            'items.*.producto_id' => 'required|exists:productos,id',
            'items.*.cantidad'    => 'required|integer|min:1',
            'items.*.precio_unitario' => 'required|numeric|min:0',
        ];
    }

    public function messages(): array
    {
        return [
            'cliente_id.exists' => 'El cliente seleccionado no es válido.',
            'items.required'    => 'Debes agregar al menos un producto a la venta.',
            'items.*.cantidad.min' => 'La cantidad debe ser al menos 1.',
        ];
    }
}
