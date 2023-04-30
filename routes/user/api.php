<?php

use App\Constants\ApiConstant;
use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::prefix(ApiConstant::PREFIX_AUTH)->namespace(ucfirst(ApiConstant::PREFIX_AUTH))->group(__DIR__ . "/" . ApiConstant::PREFIX_AUTH . ".php");
Route::namespace(ucfirst(ApiConstant::PREFIX_USER))->group(function () {
    Route::prefix(ApiConstant::PREFIX_GALLERY)->group(__DIR__ . "/" . ApiConstant::PREFIX_GALLERY . ".php");
    Route::prefix(ApiConstant::PREFIX_STRUCTURE_ORGANIZATION)->group(__DIR__ . "/" . ApiConstant::PREFIX_STRUCTURE_ORGANIZATION . ".php");
    Route::prefix(ApiConstant::PREFIX_ARTICLE)->group(__DIR__ . "/" . ApiConstant::PREFIX_ARTICLE . ".php");
    Route::prefix(ApiConstant::PREFIX_TICKET)->group(__DIR__ . "/" . ApiConstant::PREFIX_TICKET . ".php");

    Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_USER])->group(function () {
        Route::prefix(ApiConstant::PREFIX_TRANSACTION)->group(__DIR__ . "/" . ApiConstant::PREFIX_TRANSACTION . ".php");
    });
});
