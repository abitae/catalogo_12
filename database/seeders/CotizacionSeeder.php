<?php

namespace Database\Seeders;

use App\Models\Catalogo\CotizacionCatalogo;
use App\Models\Catalogo\DetalleCotizacionCatalogo;
use App\Models\Catalogo\ProductoCatalogo;
use App\Models\Shared\Customer;
use Illuminate\Database\Seeder;

class CotizacionSeeder extends Seeder
{
    public function run(): void
    {
        // Crear algunos clientes si no existen
        $customers = Customer::all();
        if ($customers->isEmpty()) {
            $customers = Customer::factory(5)->create();
        }

        // Crear algunos productos si no existen
        $productos = ProductoCatalogo::where('isActive', true)->get();
        if ($productos->isEmpty()) {
            $productos = ProductoCatalogo::factory(10)->create(['isActive' => true]);
        }

        // Crear cotizaciones de ejemplo
        foreach ($customers as $customer) {
            $cotizacion = CotizacionCatalogo::factory()->create([
                'customer_id' => $customer->id,
                'cliente_nombre' => $customer->rznSocial,
                'cliente_email' => $customer->email,
            ]);

            // Agregar algunos productos a cada cotización
            $productosAleatorios = $productos->random(rand(2, 5));
            foreach ($productosAleatorios as $producto) {
                $cantidad = rand(1, 10);
                $precio = $producto->price_venta ?? rand(10, 1000);

                DetalleCotizacionCatalogo::factory()->create([
                    'cotizacion_id' => $cotizacion->id,
                    'producto_id' => $producto->id,
                    'cantidad' => $cantidad,
                    'precio_unitario' => $precio,
                    'subtotal' => $cantidad * $precio,
                ]);
            }

            // Recalcular totales
            $cotizacion->calcularTotales();
        }
    }
}
