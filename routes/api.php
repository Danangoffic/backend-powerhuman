<?php

use App\Http\Controllers\API\CompanyController;
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

// Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
//     return $request->user();
// });

Route::prefix('company')->middleware('auth:sanctum')->group(function () {
    Route::get('/', [CompanyController::class, 'index']); //->name('api.companies.index');
    Route::post('/', [CompanyController::class, 'create']); //->name('api.companies.create');
    Route::get('/{id}', [CompanyController::class, 'show']); //->name('api.companies.show');
    Route::put('/{id}', [CompanyController::class, 'update']); //->name('api.companies.update');
    Route::delete('/{id}', [CompanyController::class, 'destroy']); //->name('api.companies.destroy');
});
// Route::get('/companies', [CompanyController::class, 'index'])->name('api.companies.index');

Route::post('/login', [UserAuthController::class, 'login']);
Route::post('/register', [UserAuthController::class, 'register']);

Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [UserAuthController::class, 'logout']);
    Route::get('/user', [UserAuthController::class, 'fetch_user']);
});
// ->middleware('auth:sanctum')->name('api.login');
// ->middleware('auth:sanctum')->name('api.register');
// ->middleware('auth:sanctum')->name('api.logout');
// ->middleware('auth:sanctum')->name('api.user');
