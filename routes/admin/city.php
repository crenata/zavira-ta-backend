<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "CityController@get");
Route::post("add", "CityController@add");
Route::post("edit", "CityController@edit");
Route::delete("delete/{id}", "CityController@delete");
