<?php

use App\Constants\ApiConstant;
use App\Constants\TokenConstant;
use Illuminate\Support\Facades\Route;

Route::prefix(ApiConstant::PREFIX_AUTH)->namespace(ucfirst(ApiConstant::PREFIX_AUTH))->group(__DIR__ . "/" . ApiConstant::PREFIX_AUTH . ".php");
Route::middleware([TokenConstant::AUTH_SANCTUM, TokenConstant::AUTH_ADMIN])
    ->namespace(ucfirst(ApiConstant::PREFIX_ADMIN))
    ->group(function () {
    Route::prefix(ApiConstant::PREFIX_GALLERY)->group(__DIR__ . "/" . ApiConstant::PREFIX_GALLERY . ".php");
    Route::prefix(ApiConstant::PREFIX_STRUCTURE_ORGANIZATION)->group(__DIR__ . "/" . ApiConstant::PREFIX_STRUCTURE_ORGANIZATION . ".php");
    Route::prefix(ApiConstant::PREFIX_ARTICLE)->group(__DIR__ . "/" . ApiConstant::PREFIX_ARTICLE . ".php");
    Route::prefix(ApiConstant::PREFIX_TICKET)->group(__DIR__ . "/" . ApiConstant::PREFIX_TICKET . ".php");
    Route::prefix(ApiConstant::PREFIX_TRANSACTION)->group(__DIR__ . "/" . ApiConstant::PREFIX_TRANSACTION . ".php");
    Route::prefix(ApiConstant::PREFIX_COMPLAINT)->group(__DIR__ . "/" . ApiConstant::PREFIX_COMPLAINT . ".php");
    Route::prefix(ApiConstant::PREFIX_PENYU)->group(__DIR__ . "/" . ApiConstant::PREFIX_PENYU . ".php");
    Route::prefix(ApiConstant::PREFIX_CONSERVATION)->group(__DIR__ . "/" . ApiConstant::PREFIX_CONSERVATION . ".php");
    Route::prefix(ApiConstant::PREFIX_CITY)->group(__DIR__ . "/" . ApiConstant::PREFIX_CITY . ".php");
    Route::prefix(ApiConstant::PREFIX_TERUMBU)->group(__DIR__ . "/" . ApiConstant::PREFIX_TERUMBU . ".php");
});
