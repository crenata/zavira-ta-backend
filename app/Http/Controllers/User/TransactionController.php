<?php

namespace App\Http\Controllers\User;

use App\Constants\MidtransStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TransactionHistoryModel;
use App\Models\TransactionModel;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class TransactionController extends Controller {
    protected $transactionTable, $transactionHistoryTable;

    public function __construct() {
        $this->transactionTable = (new TransactionModel())->getTable();
        $this->transactionHistoryTable = (new TransactionHistoryModel())->getTable();
    }

    public function getData(array $conditions = [], $id = null) {
        $detailIds = DB::table("$this->transactionHistoryTable as detail_mx")
            ->selectRaw("max(detail_mx.id) as detail_id, detail_mx.transaction_id")
            ->groupBy("detail_mx.transaction_id")
            ->toSql();
        $detailData = DB::table("$this->transactionHistoryTable as detail_data")
            ->selectRaw("detail_data.id, detail_data.status")
            ->toSql();
        $data = TransactionModel::with("latestHistory", "histories", "user")
            ->select("$this->transactionTable.*")
            ->leftJoinSub(
                $detailIds,
                "detail_max",
                "$this->transactionTable.id",
                "=",
                "detail_max.transaction_id"
            )
            ->leftJoinSub(
                $detailData,
                "detail",
                "detail.id",
                "=",
                "detail_max.detail_id"
            )
            ->whereRaw(implode(" and ", $conditions))
            ->orderByDesc("$this->transactionTable.id");

        if (empty($id)) $data = $data->paginate();
        else $data = $data->find($id);

        return $data;
    }

    public function get(Request $request) {
        $data = TransactionModel::with("latestHistory", "histories")
            ->orderByDesc("id")
            ->paginate();

        return ResponseHelper::response($data);
    }

    public function active(Request $request) {
        return ResponseHelper::response($this->getData([
            "detail.status in (" . implode(",", [
                MidtransStatusConstant::SETTLEMENT,
                MidtransStatusConstant::CHECK_IN
            ]) . ")"
        ]));
    }

    public function generate(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id,
        ], [
            "id" => "required|numeric|exists:$this->transactionTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $transaction = TransactionModel::with("latestHistory", "histories", "user")->find($id);

        return Pdf::loadView("pdfs.invoice", [
            "transaction" => $transaction
        ])->download("{$transaction->invoice_number}.pdf");
    }
}
