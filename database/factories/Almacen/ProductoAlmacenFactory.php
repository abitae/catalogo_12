<?php

namespace Database\Factories\Almacen;

use App\Models\Almacen\ProductoAlmacen;
use App\Models\Almacen\WarehouseAlmacen;
use Illuminate\Database\Eloquent\Factories\Factory;

class ProductoAlmacenFactory extends Factory
{
    protected $model = ProductoAlmacen::class;

    public function definition()
    {
        return [
            'code' => 'PROD-' . $this->faker->unique()->numberBetween(1000, 9999),
            'codes_exit' => ['SAL-' . $this->faker->unique()->numberBetween(1000, 9999)],
            'nombre' => $this->faker->word,
            'descripcion' => $this->faker->sentence,
            'categoria' => $this->faker->randomElement(['Electrónica', 'Hogar', 'Oficina', 'Herramientas']),
            'unidad_medida' => $this->faker->randomElement(['unidad', 'kg', 'litro', 'metro']),
            'stock_minimo' => $this->faker->randomFloat(2, 5, 20),
            'stock_actual' => $this->faker->randomFloat(2, 20, 100),
            'precio_unitario' => $this->faker->randomFloat(2, 10, 1000),
            'almacen_id' => WarehouseAlmacen::inRandomOrder()->first()->id,
            'lote' => 'LOTE-' . $this->faker->unique()->numberBetween(1000, 9999) . '-' . $this->faker->date('Y'),
            'estado' => true,
            'codigo_barras' => $this->faker->ean13,
            'marca' => $this->faker->company,
            'modelo' => $this->faker->bothify('MOD-####-???'),
            'imagen' => $this->faker->imageUrl(640, 480, 'product')
        ];
    }
}
