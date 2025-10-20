<?php

use App\Http\Controllers\Api\PropertyController;

Route::get('/properties', [PropertyController::class, 'index']);
