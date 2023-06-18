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
        $complaints = ComplaintModel::with("latestHistory", "histories", "user")
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
            "assignee_id" => "nullable|numeric|exists:$this->complaintTable,id",
            "status" => ["required", "numeric", Rule::in([ComplaintStatusConstant::PENDING, ComplaintStatusConstant::DROPPED, ComplaintStatusConstant::PROCESSED, ComplaintStatusConstant::RESOLVED])],
            "tracking_type" => ["required", "numeric", Rule::in([ComplaintTrackingTypeConstant::ASSIGNEE, ComplaintTrackingTypeConstant::STATUS])]
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $latestHistory = ComplaintHistoryModel::where("complaint_id", $request->id)->orderByDesc("id")->first();
        $assigneeId = null;
        if ($request->tracking_type === ComplaintTrackingTypeConstant::ASSIGNEE) {
            $assigneeId = $request->assignee_id;
        } else {
            if (!empty($latestHistory->assignee_id)) $assigneeId = $latestHistory->assignee_id;
        }
        ComplaintHistoryModel::create([
            "complaint_id" => $request->id,
            "assignee_id" => $assigneeId,
            "modifier_id" => auth()->id(),
            "status" => $request->status,
            "tracking_type" => $request->tracking_type
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
