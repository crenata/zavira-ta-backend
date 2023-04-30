<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\StructureOrganizationModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class StructureOrganizationController extends Controller {
    protected string $structureOrganizationTable;

    public function __construct() {
        $this->structureOrganizationTable = (new StructureOrganizationModel())->getTable();
    }

    public function get(Request $request) {
        $structure = StructureOrganizationModel::orderByDesc("id")->first();

        return ResponseHelper::response($structure);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "image" => "required|file|image"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $structureOrganization = StructureOrganizationModel::create([
            "image" => StorageHelper::save($request, "image", "structure-organizations")
        ]);

        return ResponseHelper::response($structureOrganization);
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->structureOrganizationTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        StructureOrganizationModel::find($id)->delete();

        return ResponseHelper::response();
    }
}
