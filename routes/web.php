<?php
use App\Http\Controllers\RecetteController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/
Route::group(['prefix' => BaseHelper::getAdminPrefix(), 'middleware' => 'auth'], function () {


    Route::get('/recette', [RecetteController::class, 'index'])->name('recette.index');
    Route::post('/recette', [RecetteController::class, 'index'])->name('recette.index');
    Route::get('/create-recette', [RecetteController::class, 'create'])->name('recette.create');

});

