<?php

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
    Route::get('catalogo/marcas', App\Livewire\Catalogo\BrandCatalogoTable::class)->name('catalogo.brands');
    Route::get('catalogo/categorias', App\Livewire\Catalogo\CategoryCatalogoTable::class)->name('catalogo.categories');
    Route::get('catalogo/lineas', App\Livewire\Catalogo\LineCatalogoTable::class)->name('catalogo.lines');
    Route::get('catalogo/productos', App\Livewire\Catalogo\ProductoCatalogoTable::class)->name('catalogo.products');
});

require __DIR__.'/auth.php';
