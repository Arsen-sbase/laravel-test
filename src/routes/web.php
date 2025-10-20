<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\PropertyController;

Route::view('/', 'search');

Route::get('/api/properties', [PropertyController::class, 'index']);
