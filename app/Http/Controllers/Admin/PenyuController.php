<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\PenyuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PenyuController extends Controller {
    protected string $penyuTable;

    public function __construct() {
        $this->penyuTable = (new PenyuModel())->getTable();
    }

    public function years(Request $request) {
        $years = PenyuModel::selectRaw(implode(",", [
            "extract(year from date) as year"
        ]))
            ->groupByRaw("1")
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

        $penyus = PenyuModel::whereRaw("date_part('year', date) = $year")
            ->get();

        return ResponseHelper::response($penyus);
    }

    public function get(Request $request) {
        $penyus = PenyuModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($penyus);
    }

    public function set(Request $request, PenyuModel $penyu) {
        $penyu->menetas = $request->menetas;
        $penyu->gagal_menetas = $request->gagal_menetas;
        $penyu->jumlah_telur = $request->jumlah_telur;
        $penyu->date = $request->date;
        $penyu->save();

        return ResponseHelper::response($penyu);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "menetas" => "nullable|numeric",
            "gagal_menetas" => "nullable|numeric",
            "jumlah_telur" => "nullable|numeric",
            "date" => "required|date|unique:$this->penyuTable,date"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, new PenyuModel());
    }

    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->penyuTable,id",
            "menetas" => "nullable|numeric",
            "gagal_menetas" => "nullable|numeric",
            "jumlah_telur" => "nullable|numeric",
            "date" => "required|date|unique:$this->penyuTable,date"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, PenyuModel::find($request->id));
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->penyuTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        PenyuModel::find($id)->delete();

        return ResponseHelper::response();
    }
}
