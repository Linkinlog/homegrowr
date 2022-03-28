<?php

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

//Landing routes
Route::prefix('/')->group(__DIR__.'/landing/landingRoutes.php');


//API routes
Route::prefix('/api')->group(__DIR__.'/api/apiRoutes.php');
