<?php

namespace Database\Factories\Crm;

use App\Models\Crm\ActivityCrm;
use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\ContactCrm;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ActivityCrmFactory extends Factory
{
    protected $model = ActivityCrm::class;

    public function definition()
    {
        return [
            'tipo' => $this->faker->randomElement(['llamada', 'reunion', 'email', 'tarea']),
            'asunto' => $this->faker->sentence(),
            'descripcion' => $this->faker->optional()->paragraph(),
            'estado' => $this->faker->randomElement(['pendiente', 'completada', 'cancelada']),
            'prioridad' => $this->faker->randomElement(['baja', 'normal', 'alta', 'urgente']),
            'image' => null, // No generamos imágenes en el factory por simplicidad
            'archivo' => null, // No generamos archivos en el factory por simplicidad
            'opportunity_id' => OpportunityCrm::inRandomOrder()->first()?->id ?? OpportunityCrm::factory(),
            'contact_id' => ContactCrm::inRandomOrder()->first()?->id ?? ContactCrm::factory(),
            'user_id' => User::inRandomOrder()->first()?->id ?? User::factory(),
        ];
    }

    public function llamada()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo' => 'llamada',
                'asunto' => 'Llamada con ' . $this->faker->name(),
            ];
        });
    }

    public function reunion()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo' => 'reunion',
                'asunto' => 'Reunión: ' . $this->faker->sentence(3),
            ];
        });
    }

    public function email()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo' => 'email',
                'asunto' => 'Email: ' . $this->faker->sentence(3),
            ];
        });
    }

    public function tarea()
    {
        return $this->state(function (array $attributes) {
            return [
                'tipo' => 'tarea',
                'asunto' => 'Tarea: ' . $this->faker->sentence(3),
            ];
        });
    }

    public function pendiente()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'pendiente',
            ];
        });
    }

    public function completada()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'completada',
            ];
        });
    }

    public function cancelada()
    {
        return $this->state(function (array $attributes) {
            return [
                'estado' => 'cancelada',
            ];
        });
    }

    public function altaPrioridad()
    {
        return $this->state(function (array $attributes) {
            return [
                'prioridad' => $this->faker->randomElement(['alta', 'urgente']),
            ];
        });
    }

    public function bajaPrioridad()
    {
        return $this->state(function (array $attributes) {
            return [
                'prioridad' => $this->faker->randomElement(['baja', 'normal']),
            ];
        });
    }
}
