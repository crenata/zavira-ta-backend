<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\PenyuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenyuController extends Controller {
    public function years(Request $request) {
        $years = PenyuModel::select("year")
            ->distinct()
            ->pluck("year");

        return ResponseHelper::response($years);
    }

    public function getPerYear(Request $request, $year) {
        $validator = Validator::make([
            "year" => $year
        ], [
            "year" => "required|date_format:Y"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $penyus = PenyuModel::where("year", $year)->get();

        return ResponseHelper::response($penyus);
    }

    public function get(Request $request) {
        $penyus = PenyuModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($penyus);
    }
}
