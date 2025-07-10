<?php

namespace App\Services;

use App\Models\Crm\OpportunityCrm;
use App\Models\Crm\ContactCrm;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class OpportunityService
{
    /**
     * Crea una nueva oportunidad
     */
    public function createOpportunity(array $data): OpportunityCrm
    {
        return DB::transaction(function () use ($data) {
            $opportunity = OpportunityCrm::create($data);

            $this->logAudit('create_opportunity', $opportunity, $data);

            return $opportunity;
        });
    }

    /**
     * Actualiza una oportunidad existente
     */
    public function updateOpportunity(int $id, array $data): OpportunityCrm
    {
        return DB::transaction(function () use ($id, $data) {
            $opportunity = OpportunityCrm::findOrFail($id);
            $opportunity->update($data);

            $this->logAudit('update_opportunity', $opportunity, $data);

            return $opportunity;
        });
    }

    /**
     * Elimina una oportunidad
     */
    public function deleteOpportunity(int $id): bool
    {
        return DB::transaction(function () use ($id) {
            $opportunity = OpportunityCrm::findOrFail($id);

            // Eliminar archivos asociados
            $this->deleteOpportunityFiles($opportunity);

            $opportunity->delete();

            $this->logAudit('delete_opportunity', $opportunity, []);

            return true;
        });
    }

    /**
     * Valida que el contacto pertenece al cliente
     */
    public function validateContactCustomerRelationship(int $contactId, int $customerId): bool
    {
        return ContactCrm::where('id', $contactId)
            ->where('customer_id', $customerId)
            ->exists();
    }

    /**
     * Obtiene oportunidades con filtros
     */
    public function getOpportunitiesWithFilters(array $filters = []): \Illuminate\Contracts\Pagination\LengthAwarePaginator
    {
        $query = OpportunityCrm::query()
            ->with(['tipoNegocio', 'marca', 'cliente', 'usuario']);

        // Aplicar filtros
        $this->applyFilters($query, $filters);

        return $query->latest()->paginate($filters['perPage'] ?? 10);
    }

    /**
     * Aplica filtros a la consulta
     */
    private function applyFilters($query, array $filters): void
    {
        if (!empty($filters['search'])) {
            $query->where(function ($q) use ($filters) {
                $q->where('nombre', 'like', '%' . $filters['search'] . '%')
                    ->orWhere('descripcion', 'like', '%' . $filters['search'] . '%');
            });
        }

        if (!empty($filters['estado'])) {
            $query->where('estado', $filters['estado']);
        }

        if (!empty($filters['tipo_negocio_id'])) {
            $query->where('tipo_negocio_id', $filters['tipo_negocio_id']);
        }

        if (!empty($filters['marca_id'])) {
            $query->where('marca_id', $filters['marca_id']);
        }

        if (!empty($filters['customer_id'])) {
            $query->where('customer_id', $filters['customer_id']);
        }

        if (!empty($filters['user_id'])) {
            $query->where('user_id', $filters['user_id']);
        }

        if (!empty($filters['etapa'])) {
            $query->where('etapa', $filters['etapa']);
        }

        if (!empty($filters['sortField']) && !empty($filters['sortDirection'])) {
            $query->orderBy($filters['sortField'], $filters['sortDirection']);
        }
    }

    /**
     * Elimina archivos asociados a una oportunidad
     */
    private function deleteOpportunityFiles(OpportunityCrm $opportunity): void
    {
        if ($opportunity->image) {
            $this->deleteFileFromStorage($opportunity->image);
        }

        if ($opportunity->archivo) {
            $this->deleteFileFromStorage($opportunity->archivo);
        }
    }

    /**
     * Elimina un archivo del storage de forma segura
     */
    private function deleteFileFromStorage(?string $filePath): bool
    {
        if (!$filePath) {
            return false;
        }

        try {
            if (Storage::disk('public')->exists($filePath)) {
                return Storage::disk('public')->delete($filePath);
            }
            return false;
        } catch (\Exception $e) {
            Log::warning('Error al eliminar archivo: ' . $e->getMessage(), [
                'file_path' => $filePath,
                'user_id' => Auth::id() ?? 'guest'
            ]);
            return false;
        }
    }

    /**
     * Registra auditoría de la oportunidad
     */
    private function logAudit(string $action, OpportunityCrm $opportunity, array $data): void
    {
        Log::info('Auditoría: Oportunidad ' . str_replace('_', ' ', $action), [
            'user_id' => Auth::id(),
            'user_name' => Auth::user()->name ?? 'N/A',
            'action' => $action,
            'opportunity_id' => $opportunity->id,
            'opportunity_name' => $opportunity->nombre,
            'customer_id' => $opportunity->customer_id,
            'valor' => $opportunity->valor,
            'etapa' => $opportunity->etapa,
            'probabilidad' => $opportunity->probabilidad,
            'timestamp' => now(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent()
        ]);
    }

    /**
     * Obtiene estadísticas de oportunidades
     */
    public function getOpportunityStats(): array
    {
        return [
            'total' => OpportunityCrm::count(),
            'nuevas' => OpportunityCrm::where('estado', 'nueva')->count(),
            'en_proceso' => OpportunityCrm::where('estado', 'en_proceso')->count(),
            'ganadas' => OpportunityCrm::where('estado', 'ganada')->count(),
            'perdidas' => OpportunityCrm::where('estado', 'perdida')->count(),
            'valor_total' => OpportunityCrm::sum('valor'),
            'valor_promedio' => OpportunityCrm::avg('valor'),
        ];
    }

    /**
     * Obtiene oportunidades por etapa
     */
    public function getOpportunitiesByStage(): array
    {
        return OpportunityCrm::select('etapa', DB::raw('count(*) as total'))
            ->groupBy('etapa')
            ->pluck('total', 'etapa')
            ->toArray();
    }

    /**
     * Obtiene oportunidades por usuario
     */
    public function getOpportunitiesByUser(): array
    {
        return OpportunityCrm::with('usuario')
            ->select('user_id', DB::raw('count(*) as total'))
            ->groupBy('user_id')
            ->get()
            ->mapWithKeys(function ($item) {
                return [$item->usuario->name ?? 'Sin asignar' => $item->total];
            })
            ->toArray();
    }
}
