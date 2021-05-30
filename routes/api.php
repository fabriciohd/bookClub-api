<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

use App\Http\Controllers\{
    AuthController,
    TitleController,
    ReservationController,
};

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/


Route::get('/ping', function() {
    return ['pong'=>true];
});

Route::get('/401', [AuthController::class, 'unauthorized'])->name('login');

Route::post('/auth/login', [AuthController::class, 'login']);
Route::post('/auth/register', [AuthController::class, 'register']);

Route::middleware('auth:api')->group(function(){
    Route::post('/auth/validate', [AuthController::class, 'validateToken']);
    Route::post('/auth/logout', [AuthController::class, 'logout']);
    Route::get('/user', function (Request $request) {
        return $request->user();
    });

    Route::get('/titles', [TitleController::class, 'getAll']);
    Route::post('/title', [TitleController::class, 'addTitle']);
    //OBRIGATORIOS: title
    Route::get('/title/disableddates', [TitleController::class, 'getDisabledDates']);
    //obrigatorios: title_id
    Route::delete('/title/{id}', [TitleController::class, 'delTitle']);
    
    Route::post('/reservation', [ReservationController::class, 'addReservation']);
    //obrigatorios: title_id, start_date(YYYY-MM-DD), end_date(YYYY-MM-DD)
});
