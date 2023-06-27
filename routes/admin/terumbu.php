<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "TerumbuController@get");
Route::get("cities", "TerumbuController@cities");
Route::post("add", "TerumbuController@add");
Route::post("edit", "TerumbuController@edit");
Route::delete("delete/{id}", "TerumbuController@delete");
