<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});


use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

use App\Http\Controllers\EmpleadoController;

Route::resource('empleados', EmpleadoController::class);


use App\Http\Controllers\DiaController;

Route::post('/dia', [DiaController::class, 'store'])->name('dia.store');
Route::get('/dias-registrados/{empleado}', [DiaController::class, 'registrados']);
use App\Http\Controllers\ExtraController;

// Guardar horas extras (POST)
Route::post('/extras/store', [ExtraController::class, 'store'])->name('extras.store');
Route::get('/extras-registrados/{empleado}', [ExtraController::class, 'registrados']);

use App\Http\Controllers\NominaController;
// Nóminas
Route::resource('nominas', NominaController::class)->except(['edit', 'update', 'destroy']);

// Ruta extra para cerrar nómina
Route::put('nominas/{nomina}/cerrar', [NominaController::class, 'cerrar'])->name('nominas.cerrar');

Route::get('/nominas/preview/{anio}/{mes}/{quincena}', [App\Http\Controllers\NominaController::class, 'preview'])
    ->name('nominas.preview');
