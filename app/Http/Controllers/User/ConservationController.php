<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ConservationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConservationController extends Controller {
    public function get(Request $request) {
        $conservations = ConservationModel::with("city")->orderByDesc("id")->get();

        return ResponseHelper::response($conservations);
    }
}
