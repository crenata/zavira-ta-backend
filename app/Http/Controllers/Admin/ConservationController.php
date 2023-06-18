<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\CityModel;
use App\Models\ConservationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ConservationController extends Controller {
    protected string $conservationTable, $cityTable;

    public function __construct() {
        $this->conservationTable = (new ConservationModel())->getTable();
        $this->cityTable = (new CityModel())->getTable();
    }

    public function get(Request $request) {
        $conservations = ConservationModel::with("city")->orderByDesc("id")->paginate();

        return ResponseHelper::response($conservations);
    }

    public function cities(Request $request) {
        $cities = CityModel::all();

        return ResponseHelper::response($cities);
    }

    public function set(Request $request, ConservationModel $conservation) {
        $conservation->city_id = $request->city_id;
        $conservation->name = $request->name;
        $conservation->wide = $request->wide;
        $conservation->target = $request->target;
        $conservation->penetapan = $request->penetapan;
        $conservation->save();

        return ResponseHelper::response($conservation);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "city_id" => "required|numeric|exists:$this->cityTable,id",
            "name" => "nullable|string",
            "wide" => "nullable|numeric",
            "target" => "nullable|string",
            "penetapan" => "nullable|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, new ConservationModel());
    }

    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->conservationTable,id",
            "city_id" => "required|numeric|exists:$this->cityTable,id",
            "name" => "nullable|string",
            "wide" => "nullable|numeric",
            "target" => "nullable|string",
            "penetapan" => "nullable|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, ConservationModel::find($request->id));
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->conservationTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        ConservationModel::find($id)->delete();

        return ResponseHelper::response();
    }
}
