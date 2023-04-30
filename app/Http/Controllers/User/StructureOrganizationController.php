<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\StructureOrganizationModel;
use Illuminate\Http\Request;

class StructureOrganizationController extends Controller {
    public function get(Request $request) {
        $structure = StructureOrganizationModel::orderByDesc("id")->first();

        return ResponseHelper::response($structure);
    }
}
