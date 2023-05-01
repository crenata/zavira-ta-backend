<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ComplaintModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ComplaintController extends Controller {
    protected string $complaintTable;

    public function __construct() {
        $this->complaintTable = (new ComplaintModel())->getTable();
    }

    public function get(Request $request) {
        $complaints = ComplaintModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($complaints);
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->complaintTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        ComplaintModel::find($id)->delete();

        return ResponseHelper::response();
    }
}
