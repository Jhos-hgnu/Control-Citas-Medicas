<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::view('/citas', 'appointments.index')->name('appointments.index');
