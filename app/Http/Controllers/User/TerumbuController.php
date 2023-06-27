<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TerumbuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TerumbuController extends Controller {
    public function get(Request $request) {
        $terumbus = TerumbuModel::with("city")->orderByDesc("id")->get();

        return ResponseHelper::response($terumbus);
    }
}
