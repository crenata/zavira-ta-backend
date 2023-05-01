<?php

namespace App\Http\Controllers\User;

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

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "subject" => "required|string",
            "description" => "required|string",
            "location" => "required|string",
            "date" => "required|string|date"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        ComplaintModel::create([
            "name" => $request->name,
            "subject" => $request->subject,
            "description" => $request->description,
            "location" => $request->location,
            "date" => $request->date
        ]);

        return ResponseHelper::response();
    }
}
