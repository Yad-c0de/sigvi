<?php

namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use App\Models\Venta;
use App\Models\Serie;
use App\Models\Producto;
use App\Models\Garantia;
use App\Models\Anulacion;
use Exception;

class VentaService
{
    /**
     * Registra una venta completa.
     * - Bloquea la serie de correlativos para evitar duplicados concurrentes.
     * - El precio de venta YA INCLUYE IGV 18%.
     *   Total  = suma de (precio × cantidad)
     *   Subtotal (base imponible) = Total / 1.18
     *   IGV = Total - Subtotal
     */
    public function registrarVenta(array $datos): Venta
    {
        return DB::transaction(function () use ($datos) {

            // 1. Serie — bloqueo pesimista para evitar correlativos duplicados
            $serie = Serie::where('tipo_comprobante', $datos['tipo_comprobante'])
                          ->lockForUpdate()
                          ->firstOrFail();

            $serie->ultimo_correlativo++;
            $serie->save();

            // 2. Cálculo de importes (precios YA incluyen IGV)
            $total    = round((float) $datos['total'], 2);
            $subtotal = round($total / 1.18, 2);
            $igv      = round($total - $subtotal, 2);

            // 3. Cabecera de la venta
            $venta = Venta::create([
                'cliente_id'      => $datos['cliente_id'],
                'user_id'         => Auth::id(),
                'tipo_comprobante'=> $datos['tipo_comprobante'],
                'serie'           => $serie->serie,
                'correlativo'     => $serie->ultimo_correlativo,
                'subtotal'        => $subtotal,
                'igv'             => $igv,
                'total'           => $total,
                'total_letras'    => $datos['total_letras'],
                'forma_pago'      => $datos['forma_pago'],
                'estado'          => 'Completada',
            ]);

            // 4. Detalle + descuento de stock + garantías
            foreach ($datos['items'] as $item) {
                $producto = Producto::lockForUpdate()->findOrFail($item['producto_id']);

                // Regla de negocio: stock insuficiente = lanzar excepción
                if ($producto->stock < $item['cantidad']) {
                    throw new Exception(
                        "Stock insuficiente para «{$producto->nombre}». "
                        . "Disponible: {$producto->stock}, solicitado: {$item['cantidad']}."
                    );
                }

                $precio  = (float) $item['precio_unitario'];
                $subDet  = round($item['cantidad'] * $precio, 2);

                $venta->detalles()->create([
                    'producto_id'    => $producto->id,
                    'cantidad'       => $item['cantidad'],
                    'precio_unitario'=> $precio,
                    'subtotal'       => $subDet,
                ]);

                // Descontar stock
                $producto->decrement('stock', $item['cantidad']);

                // Registrar garantía si el producto tiene meses de garantía
                if ($producto->meses_garantia > 0) {
                    Garantia::create([
                        'venta_id'    => $venta->id,
                        'producto_id' => $producto->id,
                        'fecha_limite'=> now()->addMonths($producto->meses_garantia)->toDateString(),
                        'estado'      => 'Vigente',
                    ]);
                }
            }

            return $venta;
        });
    }

    /**
     * Anula una venta confirmada.
     * - Restaura el stock de cada ítem.
     * - Marca la venta como "Anulada".
     * - Registra la anulación con motivo.
     * - NO realiza devolución de dinero (política Install D).
     */
    public function anularVenta(Venta $venta, string $motivo): void
    {
        if ($venta->estado === 'Anulada') {
            throw new Exception('Esta venta ya fue anulada anteriormente.');
        }

        DB::transaction(function () use ($venta, $motivo) {

            // Restaurar stock de cada producto del detalle
            foreach ($venta->detalles()->with('producto')->get() as $detalle) {
                $detalle->producto->increment('stock', $detalle->cantidad);
            }

            // Marcar venta como anulada
            $venta->update(['estado' => 'Anulada']);

            // Registrar la anulación con motivo y usuario responsable
            Anulacion::create([
                'venta_id' => $venta->id,
                'user_id'  => Auth::id(),
                'motivo'   => $motivo,
            ]);

            // Invalidar garantías de esta venta
            Garantia::where('venta_id', $venta->id)->update(['estado' => 'Vencida']);
        });
    }
}
