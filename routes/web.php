<?php

use App\Livewire\Almacen\MovimientoAlmacenIndex;
use App\Livewire\Almacen\ProductoAlmacenIndex;
use App\Livewire\Almacen\TransferenciaAlmacenIndex;
use App\Livewire\Almacen\WarehouseAlmacenIndex;
use App\Livewire\Catalogo\BrandCatalogoIndex;
use App\Livewire\Catalogo\CategoryCatalogoIndex;
use App\Livewire\Catalogo\LineCatalogoIndex;
use App\Livewire\Catalogo\ProductoCatalogoIndex;
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
});

require __DIR__.'/auth.php';
