<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\CityModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CityController extends Controller {
    protected string $cityTable;

    public function __construct() {
        $this->cityTable = (new CityModel())->getTable();
    }

    public function get(Request $request) {
        $cities = CityModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($cities);
    }

    public function set(Request $request, CityModel $city) {
        $city->name = $request->name;
        $city->save();

        return ResponseHelper::response($city);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, new CityModel());
    }

    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->cityTable,id",
            "name" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, CityModel::find($request->id));
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->cityTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        CityModel::find($id)->delete();

        return ResponseHelper::response();
    }
}
