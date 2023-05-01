<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "ComplaintController@get");
Route::delete("delete/{id}", "ComplaintController@delete");
