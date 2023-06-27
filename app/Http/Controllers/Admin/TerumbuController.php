<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\CityModel;
use App\Models\TerumbuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TerumbuController extends Controller {
    protected string $terumbuTable, $cityTable;

    public function __construct() {
        $this->terumbuTable = (new TerumbuModel())->getTable();
        $this->cityTable = (new CityModel())->getTable();
    }

    public function get(Request $request) {
        $terumbus = TerumbuModel::with("city")->orderByDesc("id")->paginate();

        return ResponseHelper::response($terumbus);
    }

    public function cities(Request $request) {
        $cities = CityModel::all();

        return ResponseHelper::response($cities);
    }

    public function set(Request $request, TerumbuModel $terumbu) {
        $terumbu->city_id = $request->city_id;
        $terumbu->name = $request->name;
        $terumbu->zone = $request->zone;
        $terumbu->percentage = $request->percentage;
        $terumbu->year = $request->year;
        $terumbu->save();

        return ResponseHelper::response($terumbu);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "city_id" => "required|numeric|exists:$this->cityTable,id",
            "name" => "required|string",
            "zone" => "required|string",
            "percentage" => "required|numeric",
            "year" => "required|string|date_format:Y"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, new TerumbuModel());
    }

    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->terumbuTable,id",
            "city_id" => "required|numeric|exists:$this->cityTable,id",
            "name" => "required|string",
            "zone" => "required|string",
            "percentage" => "required|numeric",
            "year" => "required|string|date_format:Y"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, TerumbuModel::find($request->id));
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->terumbuTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        TerumbuModel::find($id)->delete();

        return ResponseHelper::response();
    }
}
