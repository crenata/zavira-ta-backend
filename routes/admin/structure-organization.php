<?php

use Illuminate\Support\Facades\Route;

Route::get("get", "StructureOrganizationController@get");
Route::post("add", "StructureOrganizationController@add");
Route::delete("delete/{id}", "StructureOrganizationController@delete");
