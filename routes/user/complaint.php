<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "ComplaintController@get");
Route::post("add", "ComplaintController@add");
