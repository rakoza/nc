<?php

use App\Http\Controllers\DockerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\TenantController;
use App\Http\Controllers\UserController;

// treba biti dozvoljeno svi korisnicima
Route::get('profile', [ProfileController::class, 'getProfile'])->name('profile.get');
Route::put('profile', [ProfileController::class, 'updateProfile'])->name('profile.update');
Route::delete('profile', [ProfileController::class, 'deleteAccount'])->name('account.delete');
Route::put('pass', [ProfileController::class, 'storenewpass']);

// place for protected routes, API routes for SPA
Route::apiResource('users', UserController::class);
Route::put('update-user-password/{user}', [HomeController::class, 'updateUserPassword']);
// Route::get('app/versions', [HomeController::class, 'getVersions']);
// Route::get('app/timezones', [HomeController::class, 'getTimezones']);

Route::apiResource('tenants', TenantController::class);

Route::get('docker/status', [DockerController::class, 'getStatus']);
Route::get('docker/nginx', [DockerController::class, 'getNginxContainer']);
Route::get('docker/containers', [DockerController::class, 'getContainers']);
Route::get('docker/container/{tenant}', [DockerController::class, 'getContainer']);
Route::post('docker/container/{tenant}/create', [DockerController::class, 'createContainer']);
Route::post('docker/container/{containerId}/start', [DockerController::class, 'startContainer']);
Route::post('docker/container/{containerId}/stop', [DockerController::class, 'stopContainer']);
Route::delete('docker/container/{containerId}/remove', [DockerController::class, 'removeContainer']);
