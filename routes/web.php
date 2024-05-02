<?php

use Illuminate\Support\Facades\Route;

Route::redirect('/', '/admin');
Route::redirect('/login', '/admin/login')->name('login');


Route::post('/orders', [\App\Http\Controllers\OrderController::class, 'store'])->withoutMiddleware(['web'])->middleware('api');
