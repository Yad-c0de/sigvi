<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\Serie;
use App\Models\Categoria;
use App\Models\Marca;
use App\Models\Cliente;
use App\Models\Empresa;
use App\Models\Proveedor; // Asegúrate de importar el modelo Proveedor

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ── Usuario Administrador (rol Admin) ────────────────────────────
        User::updateOrCreate(
            ['email' => 'admin@installd.pe'],
            [
                'name'     => 'Administrador',
                'password' => Hash::make('password'),
                'rol'      => 'Admin',
            ]
        );

        // ── Usuario Vendedor (rol Vendedor) ──────────────────────────────
        User::firstOrCreate(
            ['email' => 'vendedor@installd.pe'],
            [
                'name'     => 'Vendedor Prueba',
                'password' => Hash::make('password'),
                'rol'      => 'Vendedor',
            ]
        );

        // ── Series de comprobantes ────────────────────────────────────────
        Serie::firstOrCreate(['tipo_comprobante' => 'Boleta'],  ['serie' => 'B001', 'ultimo_correlativo' => 0]);
        Serie::firstOrCreate(['tipo_comprobante' => 'Factura'], ['serie' => 'F001', 'ultimo_correlativo' => 0]);

        // ── Categorías con prefijos ───────────────────────────────────────
        $categorias = [
            ['nombre' => 'GPS Vehicular',   'prefijo' => 'GPS'],
            ['nombre' => 'Alarmas',         'prefijo' => 'ALR'],
            ['nombre' => 'Focos LED',       'prefijo' => 'FOC'],
            ['nombre' => 'Amplificadoras',  'prefijo' => 'AMP'],
            ['nombre' => 'Parlantes',       'prefijo' => 'PAR'],
            ['nombre' => 'Cámaras',         'prefijo' => 'CAM'],
            ['nombre' => 'Accesorios',      'prefijo' => 'ACC'],
        ];

        foreach ($categorias as $cat) {
            Categoria::updateOrCreate(
                ['nombre' => $cat['nombre']],
                ['prefijo' => $cat['prefijo']]
            );
        }

        // ── Marcas iniciales ──────────────────────────────────────────────
        $marcas = ['TOYOTA', 'HONDA', 'Garmin', 'Pioneer', 'JVC', 'Alpine', 'Clarion', 'Sony', 'Philips', 'Genérico'];
        foreach ($marcas as $marca) {
            Marca::firstOrCreate(['nombre' => $marca]);
        }

        // ── Cliente General (con nuevos campos) ───────────────────────────
        Cliente::firstOrCreate(
            ['tipo_documento' => 'VARIOS'],
            [
                'nombre_razon_social' => 'Cliente General',
                'numero_documento'    => null,
                'telefono'            => null,
                'email'               => null,
                'estado'              => 'Activo',
            ]
        );

        // ── Proveedores de ejemplo ────────────────────────────────────────
        Proveedor::firstOrCreate(
            ['ruc' => '20123456789'],
            [
                'nombre_comercial' => 'Importaciones Automotrices S.A.C.',
                'contacto_nombre'  => 'Carlos López',
                'telefono'         => '987654321',
            ]
        );
        Proveedor::firstOrCreate(
            ['ruc' => '20456789012'],
            [
                'nombre_comercial' => 'Distribuidora Instal D E.I.R.L.',
                'contacto_nombre'  => 'María Fernández',
                'telefono'         => '912345678',
            ]
        );
        Proveedor::firstOrCreate(
            ['ruc' => '20678901234'],
            [
                'nombre_comercial' => 'Autorepuestos del Norte',
                'contacto_nombre'  => 'Juan Pérez',
                'telefono'         => '998877665',
            ]
        );

        // ── Empresa ───────────────────────────────────────────────────────
        Empresa::firstOrCreate(
            ['id' => 1],
            [
                'ruc'          => '20600000001',
                'razon_social' => 'Install D S.A.C.',
                'direccion'    => 'Av. Principal 123',
                'distrito'     => 'Trujillo',
                'ciudad'       => 'Trujillo',
            ]
        );
    }
}
