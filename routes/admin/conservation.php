<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "ConservationController@get");
Route::get("cities", "ConservationController@cities");
Route::post("add", "ConservationController@add");
Route::post("edit", "ConservationController@edit");
Route::delete("delete/{id}", "ConservationController@delete");
