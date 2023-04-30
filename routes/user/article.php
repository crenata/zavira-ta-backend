<?php

use Illuminate\Support\Facades\Route;

Route::get("get/home", "ArticleController@getHome");
Route::get("get/program", "ArticleController@getProgram");
Route::get("get/information", "ArticleController@getInformation");
Route::get("get/sop", "ArticleController@getSop");
Route::get("get/detail/{id}", "ArticleController@getDetail");
