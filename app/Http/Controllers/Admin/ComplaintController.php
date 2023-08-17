<?php

namespace App\Http\Controllers\Admin;

use App\Constants\ComplaintStatusConstant;
use App\Constants\ComplaintTrackingTypeConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ComplaintHistoryModel;
use App\Models\ComplaintModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller {
    protected string $complaintTable, $complaintHistoryTable;

    public function __construct() {
        $this->complaintTable = (new ComplaintModel())->getTable();
        $this->complaintHistoryTable = (new ComplaintHistoryModel())->getTable();
    }

    public function get(Request $request) {
        $validator = Validator::make($request->all(), [
            "status" => ["required", "numeric", Rule::in([ComplaintStatusConstant::PENDING, ComplaintStatusConstant::DROPPED, ComplaintStatusConstant::PROCESSED, ComplaintStatusConstant::RESOLVED])]
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $detailIds = DB::table("$this->complaintHistoryTable as detail_mx")
            ->selectRaw("max(detail_mx.id) as detail_id, detail_mx.complaint_id")
            ->groupBy("detail_mx.complaint_id")
            ->toSql();
        $detailData = DB::table("$this->complaintHistoryTable as detail_data")
            ->selectRaw("detail_data.id, detail_data.status")
            ->toSql();
        $complaints = ComplaintModel::with("latestHistory", "histories", "user", "city")
            ->select("$this->complaintTable.*")
            ->leftJoinSub(
                $detailIds,
                "detail_max",
                "$this->complaintTable.id",
                "=",
                "detail_max.complaint_id"
            )
            ->leftJoinSub(
                $detailData,
                "detail",
                "detail.id",
                "=",
                "detail_max.detail_id"
            );
        if (!empty($request->status)) $complaints = $complaints->whereRaw("detail.status = $request->status");
        $complaints = $complaints->orderByDesc("$this->complaintTable.id")
            ->paginate();

        return ResponseHelper::response($complaints);
    }

    public function set(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->complaintTable,id",
            "status" => ["required", "numeric", Rule::in([ComplaintStatusConstant::PENDING, ComplaintStatusConstant::DROPPED, ComplaintStatusConstant::PROCESSED, ComplaintStatusConstant::RESOLVED])]
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        ComplaintHistoryModel::create([
            "complaint_id" => $request->id,
            "status" => $request->status
        ]);

        return ResponseHelper::response();
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
