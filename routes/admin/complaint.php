<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "ComplaintController@get");
Route::post("set", "ComplaintController@set");
Route::delete("delete/{id}", "ComplaintController@delete");
