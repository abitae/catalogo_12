<?php

namespace App\Traits;

use Livewire\WithPagination;

trait TableTrait
{
    use WithPagination;

    // Propiedades comunes para tablas
    public $search = '';
    public $sortField = 'name';
    public $sortDirection = 'asc';
    public $perPage = 10;
    public $showFilters = false;

    /**
     * Actualiza la búsqueda y resetea la página
     */
    public function updatingSearch()
    {
        $this->resetPage();
    }

    /**
     * Ordena por campo
     */
    public function sortBy($field)
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Limpia todos los filtros
     */
    public function clearFilters()
    {
        $this->reset([
            'search',
            'sortField',
            'sortDirection',
            'perPage',
        ]);
        $this->resetPage();
    }

    /**
     * Alterna la visibilidad de los filtros
     */
    public function toggleFilters()
    {
        $this->showFilters = !$this->showFilters;
    }

    /**
     * Aplica búsqueda a una consulta
     */
    protected function applySearch($query, $searchFields = ['name'])
    {
        return $query->when($this->search, function ($query) use ($searchFields) {
            $query->where(function ($q) use ($searchFields) {
                foreach ($searchFields as $field) {
                    $q->orWhere($field, 'like', '%' . $this->search . '%');
                }
            });
        });
    }

    /**
     * Aplica ordenamiento a una consulta
     */
    protected function applySorting($query)
    {
        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    /**
     * Aplica paginación a una consulta
     */
    protected function applyPagination($query)
    {
        return $query->paginate($this->perPage);
    }

    /**
     * Obtiene las opciones de registros por página
     */
    protected function getPerPageOptions()
    {
        return [10, 25, 50, 100, 200, 500, 1000];
    }

    /**
     * Obtiene el ícono de ordenamiento
     */
    protected function getSortIcon($field)
    {
        if ($this->sortField === $field) {
            return $this->sortDirection === 'asc' ? 'arrow-up' : 'arrow-down';
        }
        return 'arrow-up-down';
    }
}
