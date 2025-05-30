<?php

namespace Database\Factories\Almacen;

use App\Models\Almacen\WarehouseAlmacen;
use Illuminate\Database\Eloquent\Factories\Factory;

class WarehouseAlmacenFactory extends Factory
{
    protected $model = WarehouseAlmacen::class;

    public function definition()
    {
        return [
            'code' => 'ALM-' . $this->faker->unique()->numberBetween(1000, 9999),
            'nombre' => $this->faker->company,
            'direccion' => $this->faker->address,
            'telefono' => $this->faker->phoneNumber,
            'email' => $this->faker->companyEmail,
            'estado' => true,
            'capacidad' => $this->faker->randomFloat(2, 100, 1000),
            'responsable' => $this->faker->name
        ];
    }
}
