<?php

use App\Http\Controllers\FlightController;
use App\Http\Controllers\FlightPriceCrawlerController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;




Route::post('/flight-price-crawler', FlightPriceCrawlerController::class);
