<?php

namespace Database\Factories\Crm;

use App\Models\Crm\MarcaCrm;
use Illuminate\Database\Eloquent\Factories\Factory;

class MarcaCrmFactory extends Factory
{
    protected $model = MarcaCrm::class;

    public function definition()
    {
        $categorias = ['Tecnología', 'Alimentación', 'Automotriz', 'Moda', 'Salud', 'Entretenimiento', 'Deportes', 'Hogar'];

        return [
            'nombre' => $this->faker->unique()->company(),
            'codigo' => $this->faker->unique()->regexify('[A-Z]{2}[0-9]{3}'),
            'categoria' => $this->faker->randomElement($categorias),
            'descripcion' => $this->faker->sentence(),
            'logo' => null, // No generamos logos en el factory por simplicidad
            'activo' => $this->faker->boolean(85) // 85% probabilidad de estar activa
        ];
    }

    public function activa()
    {
        return $this->state(function (array $attributes) {
            return [
                'activo' => true,
            ];
        });
    }

    public function inactiva()
    {
        return $this->state(function (array $attributes) {
            return [
                'activo' => false,
            ];
        });
    }

    public function porCategoria($categoria)
    {
        return $this->state(function (array $attributes) use ($categoria) {
            return [
                'categoria' => $categoria,
            ];
        });
    }
}
