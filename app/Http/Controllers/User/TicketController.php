<?php

namespace App\Http\Controllers\User;

use App\Constants\MidtransStatusConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TicketModel;
use App\Models\TransactionHistoryModel;
use App\Models\TransactionModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Midtrans\Config;
use Midtrans\Snap;

class TicketController extends Controller {
    protected string $ticketTable;

    public function __construct() {
        $this->ticketTable = (new TicketModel())->getTable();
    }

    public function get(Request $request) {
        $tickets = TicketModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($tickets);
    }

    public function getDetail(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->ticketTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $tickets = TicketModel::find($id);

        return ResponseHelper::response($tickets);
    }

    public function buy(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->ticketTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($id) {
            $ticket = TicketModel::find($id);

            $now = Carbon::now();
            $invoiceNumber = $now->format("Y-") . Str::random(4) . $now->format("-m-") . Str::random(4) . $now->format("-d-") . Str::random(12);

            Config::$serverKey = env("MIDTRANS_SERVER_KEY");
            Config::$isProduction = env("MIDTRANS_PRODUCTION");
            Config::$isSanitized = true;
            Config::$is3ds = true;
            Config::$overrideNotifUrl = env("MIDTRANS_OVERRIDE_NOTIFICATION_URL");

            $snapUrl = Snap::getSnapUrl([
                "transaction_details" => [
                    "order_id" => $invoiceNumber,
                    "gross_amount" => $ticket->price
                ]
            ]);

            $transaction = TransactionModel::create([
                "invoice_number" => $invoiceNumber,
                "user_id" => auth()->id(),
                "name" => $ticket->name,
                "description" => $ticket->description,
                "price" => $ticket->price,
                "snap_url" => $snapUrl
            ]);
            TransactionHistoryModel::create([
                "transaction_id" => $transaction->id,
                "status" => MidtransStatusConstant::PENDING
            ]);

            return ResponseHelper::response($snapUrl);
        });
    }

    public function update(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->ticketTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return ResponseHelper::response();
    }
}
