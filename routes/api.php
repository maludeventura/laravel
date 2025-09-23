<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UsuarioController;
use App\Http\Controllers\PostController;

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


    Route::prefix('usuario')->group(function() {
        Route::post('registrar-se', [UsuarioController::class, 'registrar']);
        Route::post('login', [UsuarioController::class, 'login']);

    Route::middleware('auth:sanctum')->group(function() {
        Route::post('logout', [UsuarioController::class, 'logout']);
        Route::post('perfil', [UsuarioController::class, 'perfil']);
        Route::post('editar', [UsuarioController::class, 'editar']);
        Route::post('desativar-conta', [UsuarioController::class, 'desativarConta']);
        Route::post('foto-upload', [UsuarioController::class, 'fotoUpload']);
        Route::get('posts', [PostController::class, 'index']);
        Route::post('posts', [PostController::class, 'store']);
    });
});

Route::middleware('auth:sanctum')->delete('posts/{post}', [PostController::class, 'destroy']);





