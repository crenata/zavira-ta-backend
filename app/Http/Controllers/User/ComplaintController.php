<?php

namespace App\Http\Controllers\User;

use App\Constants\ComplaintStatusConstant;
use App\Helpers\ResponseHelper;
use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\CityModel;
use App\Models\ComplaintHistoryModel;
use App\Models\ComplaintModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class ComplaintController extends Controller {
    protected string $complaintTable, $complaintHistoryTable, $cityTable;

    public function __construct() {
        $this->complaintTable = (new ComplaintModel())->getTable();
        $this->complaintHistoryTable = (new ComplaintHistoryModel())->getTable();
        $this->cityTable = (new CityModel())->getTable();
    }

    public function getCity(Request $request) {
        $cities = CityModel::all();

        return ResponseHelper::response($cities);
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

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "subject" => "required|string",
            "description" => "required|string",
            "location" => "required|numeric|exists:$this->cityTable,id",
            "date" => "required|string|date",
            "image" => "required|file|image",
            "video" => "required|file|mimes:mp4,mov,MP2T,x-mpegURL,x-flv,3gpp,quicktime,x-msvideo,x-ms-wmv"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($request) {
            $complaint = ComplaintModel::create([
                "user_id" => auth()->id(),
                "city_id" => $request->location,
                "name" => $request->name,
                "subject" => $request->subject,
                "description" => $request->description,
                "date" => $request->date,
                "image" => StorageHelper::save($request, "image", "complaints"),
                "video" => StorageHelper::save($request, "video", "complaints")
            ]);

            ComplaintHistoryModel::create([
                "complaint_id" => $complaint->id,
                "status" => ComplaintStatusConstant::PENDING
            ]);

            return ResponseHelper::response();
        });
    }
}
