<?php

use App\Http\Controllers\EventController;
use App\Http\Controllers\RoomController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

// ======
// ROOM
// ======
// crear sala
Route::post('/room', [RoomController::class, 'store']);
// mostrar salas
Route::get('/room',[RoomController::class, 'index']);
// cambiar nombre
Route::put('/room/{id}',[RoomController::class, 'update']);

// ======
// EVENT
// ======
// crea eventos
Route::post('/event/create1',[EventController::class,'store']);
Route::post('/event/create2',[EventController::class, 'crearOpcion2']);
// eventos Activos
Route::post('/event/activos', [EventController::class, 'eventActivos']);
// cancelar evento
Route::post('/event/cancelar', [EventController::class, 'cancelEventXnombre']);

// ====
// REPORTE de ocupacion por fecha
Route::post('/eventos/reporte-ocupacion', [EventController::class, 'reporteOcupacion']);










