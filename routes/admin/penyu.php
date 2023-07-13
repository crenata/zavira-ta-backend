<?php

use Illuminate\Support\Facades\Route;

Route::get("years", "PenyuController@years");
Route::get("get", "PenyuController@get");
Route::get("get/{year}", "PenyuController@getPerYear");
Route::post("import", "PenyuController@import");
Route::post("add", "PenyuController@add");
Route::post("edit", "PenyuController@edit");
Route::delete("delete/{id}", "PenyuController@delete");
