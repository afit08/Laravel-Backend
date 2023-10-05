<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\CommentsController;
use App\Http\Controllers\NewsController;
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

Route::post('/login', [AuthController::class, 'login']);
Route::post('/register', [AuthController::class, 'register']);
// Route::group(function () {
//     Route::get('/news/all', 'App\Http\Controllers\NewsController@index');
//     Route::get('/user', 'App\Http\Controllers\AuthController@user');
//     Route::post('/logout', 'App\Http\Controllers\AuthController@logout');
// });

Route::group(['middleware' => ['auth:api',]], function () {
    // API NEWS
    Route::get('/news/all', [NewsController::class, 'index']);
    Route::post('/news/create', [NewsController::class, 'create']);
    Route::post('/news/update/{id}', [NewsController::class, 'update']);
    Route::delete('/news/delete/{id}', [NewsController::class, 'delete']);
    Route::get('/news/detail/{id}', [NewsController::class, 'detail']);

    // API COMMENTS
    Route::post('/comments/{id}', [CommentsController::class, 'postComments']);
});
