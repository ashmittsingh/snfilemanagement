<?php


use App\Http\Controllers\Frontend\HomePageController;
use Illuminate\Support\Facades\Route;


Route::get('/', [HomePageController::class, 'index'])
    ->name('frontend.index');




