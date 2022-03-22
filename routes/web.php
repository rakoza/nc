<?php

use App\Http\Controllers\HomeController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', [HomeController::class, 'index']);

// na produkciji moramo odgovoriti na refresh + /assets
// tj povesti korisnika na spa index page
Route::any('/{any}', [HomeController::class, 'index'])->where('any', '^(?!spa).*$');
