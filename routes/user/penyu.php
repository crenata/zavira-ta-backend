<?php

use Illuminate\Support\Facades\Route;

Route::get("years", "PenyuController@years");
Route::get("get", "PenyuController@get");
Route::get("get/{year}", "PenyuController@getPerYear");
