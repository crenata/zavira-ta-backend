<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "ComplaintController@get");
Route::get("get/city", "ComplaintController@getCity");
Route::post("add", "ComplaintController@add");
