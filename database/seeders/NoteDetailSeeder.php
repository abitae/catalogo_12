<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Facturacion\NoteDetail;
use App\Models\Facturacion\Note;

class NoteDetailSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Verificar que existan notas
        $notes = Note::all();

        if ($notes->isEmpty()) {
            $this->command->warn('No se pueden crear detalles de notas sin notas. Ejecute primero NoteSeeder.');
            return;
        }

        // Crear entre 1 y 5 detalles por cada nota
        foreach ($notes as $note) {
            $numDetails = rand(1, 5);

            NoteDetail::factory($numDetails)->create([
                'note_id' => $note->id,
            ]);
        }

        $totalDetails = NoteDetail::count();
        $this->command->info("Se crearon {$totalDetails} detalles de notas exitosamente.");
    }
}
