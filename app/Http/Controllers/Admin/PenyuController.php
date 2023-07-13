<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\PenyuModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;
use PhpOffice\PhpSpreadsheet\Spreadsheet;

class PenyuController extends Controller {
    protected string $penyuTable;

    public function __construct() {
        $this->penyuTable = (new PenyuModel())->getTable();
    }

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

    public function set(Request $request) {
        $exploded = explode("-", $request->date);
        PenyuModel::updateOrCreate([
            "year" => $exploded[0],
            "month" => $exploded[1]
        ], [
            "adopt" => $request->adopt,
            "menetas" => $request->menetas,
            "gagal_menetas" => $request->gagal_menetas,
            "belum_menetas" => $request->belum_menetas
        ]);

        return ResponseHelper::response();
    }

    public function import(Request $request) {
        $validator = Validator::make($request->all(), [
            "file" => "required|file|mimes:xlsx,xls",
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $reader = new Xlsx();
        $reader->setReadDataOnly(true);
        $spreadsheet = $reader->load($request->file("file")->getPathname());

        $year = "";
        $adopt = [];
        $menetas = [];
        $gagalMenetas = [];
        $belumMenetas = [];

        foreach ($spreadsheet->getActiveSheet()->getRowIterator() as $row) {
            foreach ($row->getCellIterator() as $cell) {
                switch ($cell->getCoordinate()) {
                    case "B3":
                        $year = str_replace("TAHUN ", "", $cell->getCalculatedValue());
                        break;
                    case "D7":
                    case "E7":
                    case "F7":
                    case "G7":
                    case "H7":
                    case "I7":
                    case "J7":
                    case "K7":
                    case "L7":
                    case "M7":
                    case "N7":
                    case "O7":
                        array_push($adopt, str_replace("-", "0", $cell->getCalculatedValue()));
                        break;
                    case "D8":
                    case "E8":
                    case "F8":
                    case "G8":
                    case "H8":
                    case "I8":
                    case "J8":
                    case "K8":
                    case "L8":
                    case "M8":
                    case "N8":
                    case "O8":
                        array_push($menetas, str_replace("-", "0", $cell->getCalculatedValue()));
                        break;
                    case "D9":
                    case "E9":
                    case "F9":
                    case "G9":
                    case "H9":
                    case "I9":
                    case "J9":
                    case "K9":
                    case "L9":
                    case "M9":
                    case "N9":
                    case "O9":
                        array_push($gagalMenetas, str_replace("-", "0", $cell->getCalculatedValue()));
                        break;
                    case "D10":
                    case "E10":
                    case "F10":
                    case "G10":
                    case "H10":
                    case "I10":
                    case "J10":
                    case "K10":
                    case "L10":
                    case "M10":
                    case "N10":
                    case "O10":
                        array_push($belumMenetas, str_replace("-", "0", $cell->getCalculatedValue()));
                        break;
                    default:
                        break;
                }
            }
        }

        foreach (range(0, 11) as $index) {
            PenyuModel::updateOrCreate([
                "year" => $year,
                "month" => sprintf("%02d", $index + 1)
            ], [
                "adopt" => (int) $adopt[$index],
                "menetas" => (int) $menetas[$index],
                "gagal_menetas" => (int) $gagalMenetas[$index],
                "belum_menetas" => (int) $belumMenetas[$index]
            ]);
        }

        return ResponseHelper::response();
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "adopt" => "nullable|numeric",
            "menetas" => "nullable|numeric",
            "gagal_menetas" => "nullable|numeric",
            "belum_menetas" => "nullable|numeric",
            "date" => "required|string|date_format:Y-m"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request);
    }

    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->penyuTable,id",
            "adopt" => "nullable|numeric",
            "menetas" => "nullable|numeric",
            "gagal_menetas" => "nullable|numeric",
            "belum_menetas" => "nullable|numeric",
            "date" => "required|string|date_format:Y-m"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request);
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
