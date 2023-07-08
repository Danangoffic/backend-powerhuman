<?php

use App\Http\Controllers\API\CompanyController;
use App\Http\Controllers\API\RoleController;
use App\Http\Controllers\API\TeamController;
use App\Http\Controllers\API\UserAuthController;
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

Route::prefix('company')->middleware('auth:sanctum')->name('company.')->group(function () {
    // get all companies or by id and limit
    Route::get('/', [CompanyController::class, 'index'])->name('fetch');
    // create a company
    Route::post('/', [CompanyController::class, 'create'])->name('create');
    // get a company by company id
    Route::get('/{id}', [CompanyController::class, 'show'])->name('show');
    // update a company by company id
    Route::put('/{id}', [CompanyController::class, 'update'])->name('update');
    // delete a company by company id
    Route::delete('/{id}', [CompanyController::class, 'destroy'])->name('destroy');
});

Route::prefix('team')->middleware('auth:sanctum')->name('team.')->group(function () {
    Route::post('/', [TeamController::class, 'create'])->name('create');
    Route::get('/', [TeamController::class, 'fetch'])->name('fetch');
    Route::post('/{id}', [TeamController::class, 'update'])->name('update');
    Route::post('/{id}/delete', [TeamController::class, 'delete'])->name('destroy');
});

Route::prefix('role')->middleware('auth:sanctum')->name('role.')->group(function () {
    Route::get('/', [RoleController::class, 'fetch'])->name('fetch');
    Route::post('/', [RoleController::class, 'create'])->name('create');
    Route::post('/{id}', [RoleController::class, 'update'])->name('update');
    Route::post('/{id}/delete', [RoleController::class, 'delete'])->name('destroy');
});

// Route::prefix('company')->middleware('auth:sanctum')->group(function () {
//     Route::get('/', [CompanyController::class, 'index']); //->name('api.companies.index');
//     Route::post('/', [CompanyController::class, 'create']); //->name('api.companies.create');
//     Route::get('/{id}', [CompanyController::class, 'show']); //->name('api.companies.show');
//     Route::put('/{id}', [CompanyController::class, 'update']); //->name('api.companies.update');
//     Route::delete('/{id}', [CompanyController::class, 'destroy']); //->name('api.companies.destroy');
// });
// Route::get('/companies', [CompanyController::class, 'index'])->name('api.companies.index');

Route::name('auth.')->group(function () {
    Route::post('/login', [UserAuthController::class, 'login'])->name('login');
    Route::post('/register', [UserAuthController::class, 'register'])->name('register');

    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/logout', [UserAuthController::class, 'logout'])->name('logout');
        Route::get('/user', [UserAuthController::class, 'fetch_user'])->name('fetch');
    });
});
// ->middleware('auth:sanctum')->name('api.login');
// ->middleware('auth:sanctum')->name('api.register');
// ->middleware('auth:sanctum')->name('api.logout');
// ->middleware('auth:sanctum')->name('api.user');
