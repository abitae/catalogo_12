<?php

use App\Livewire\Almacen\MovimientoAlmacenIndex;
use App\Livewire\Almacen\ProductoAlmacenIndex;
use App\Livewire\Almacen\TransferenciaAlmacenIndex;
use App\Livewire\Almacen\WarehouseAlmacenIndex;
use App\Livewire\Almacen\ReporteLotesIndex;
use App\Livewire\Almacen\AlertasLotesIndex;
use App\Livewire\Catalogo\BrandCatalogoIndex;
use App\Livewire\Catalogo\CategoryCatalogoIndex;
use App\Livewire\Catalogo\LineCatalogoIndex;
use App\Livewire\Catalogo\ProductoCatalogoIndex;
use App\Livewire\Crm\ContactCrmIndex;
use App\Livewire\Crm\OpportunityCrmIndex;
use App\Livewire\Crm\ActivityCrmIndex;
use App\Livewire\Crm\MarcaCrmIndex;
use App\Livewire\Crm\TipoNegocioCrmIndex;
use App\Livewire\Shared\CustomerIndex;
use App\Livewire\Shared\TipoCustomerIndex;
use Illuminate\Support\Facades\Route;
use Livewire\Volt\Volt;

Route::get('/', function () {
    return view('welcome');
})->name('home');

Route::view('dashboard', 'dashboard')
    ->middleware(['auth', 'verified'])
    ->name('dashboard');

Route::middleware(['auth'])->group(function () {
    Route::redirect('settings', 'settings/profile');

    Volt::route('settings/profile', 'settings.profile')->name('settings.profile');
    Volt::route('settings/password', 'settings.password')->name('settings.password');
    Volt::route('settings/appearance', 'settings.appearance')->name('settings.appearance');

    // Rutas del Catálogo
    Route::get('catalogo/marcas', BrandCatalogoIndex::class)->name('catalogo.brands');
    Route::get('catalogo/categorias', CategoryCatalogoIndex::class)->name('catalogo.categories');
    Route::get('catalogo/lineas', LineCatalogoIndex::class)->name('catalogo.lines');
    Route::get('catalogo/productos', ProductoCatalogoIndex::class)->name('catalogo.products');

    // Rutas del Almacén
    Route::get('almacen/almacenes', WarehouseAlmacenIndex::class)->name('almacen.warehouses');
    Route::get('almacen/productos', ProductoAlmacenIndex::class)->name('almacen.products');
    Route::get('almacen/transferencias', TransferenciaAlmacenIndex::class)->name('almacen.transfers');
    Route::get('almacen/movimientos', MovimientoAlmacenIndex::class)->name('almacen.movements');

    // Rutas de Lotes (Nuevas)
    Route::get('almacen/reportes/lotes', ReporteLotesIndex::class)->name('almacen.reportes.lotes');
    Route::get('almacen/alertas/lotes', AlertasLotesIndex::class)->name('almacen.alertas.lotes');

    // Rutas adicionales para funcionalidades de lotes
    Route::get('almacen/reportes/lotes/export', function () {
        // Ruta para exportación de reportes de lotes
        return redirect()->route('almacen.reportes.lotes');
    })->name('almacen.reportes.lotes.export');

    Route::get('almacen/lotes/{lote}/detalle', function ($lote) {
        // Ruta para ver detalles específicos de un lote
        return redirect()->route('almacen.reportes.lotes', ['lote_filter' => $lote]);
    })->name('almacen.lotes.detalle');

    Route::get('almacen/lotes/{lote}/movimientos', function ($lote) {
        // Ruta para ver movimientos de un lote específico
        return redirect()->route('almacen.movements', ['lote_filter' => $lote]);
    })->name('almacen.lotes.movimientos');

    // Rutas del CRM
    Route::get('crm/opportunities', OpportunityCrmIndex::class)->name('crm.opportunities');
    Route::get('crm/contacts', ContactCrmIndex::class)->name('crm.contacts');
    Route::get('crm/activities', ActivityCrmIndex::class)->name('crm.activities');
    Route::get('crm/marcas', MarcaCrmIndex::class)->name('crm.marcas');
    Route::get('crm/tipos-negocio', TipoNegocioCrmIndex::class)->name('crm.tipos-negocio');

    // Rutas de Shared
    Route::get('shared/customers', CustomerIndex::class)->name('shared.customers');
    Route::get('shared/tipos-customer', TipoCustomerIndex::class)->name('shared.tipos-customer');
});

require __DIR__.'/auth.php';
