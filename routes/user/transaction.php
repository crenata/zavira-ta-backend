<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "TransactionController@get");
Route::get("active", "TransactionController@active");
Route::get("generate/{id}", "TransactionController@generate");
