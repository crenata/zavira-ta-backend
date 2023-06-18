<?php

namespace App\Http\Controllers\User;

use App\Constants\ComplaintStatusConstant;
use App\Helpers\ResponseHelper;
use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\ComplaintHistoryModel;
use App\Models\ComplaintModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
            "date" => "required|string|date",
            "image" => "required|file|image"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($request) {
            $complaint = ComplaintModel::create([
                "user_id" => auth()->id(),
                "name" => $request->name,
                "subject" => $request->subject,
                "description" => $request->description,
                "location" => $request->location,
                "date" => $request->date,
                "image" => StorageHelper::save($request, "image", "complaints")
            ]);

            ComplaintHistoryModel::create([
                "complaint_id" => $complaint->id,
                "status" => ComplaintStatusConstant::PENDING
            ]);

            return ResponseHelper::response();
        });
    }
}
