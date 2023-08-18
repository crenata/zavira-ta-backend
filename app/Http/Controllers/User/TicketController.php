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
    protected string $ticketTable, $transactionTable;

    public function __construct() {
        $this->ticketTable = (new TicketModel())->getTable();
        $this->transactionTable = (new TransactionModel())->getTable();
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

    public function buy(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->ticketTable,id",
            "quantity" => "required|numeric|min:1",
            "date" => "required|string|date"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($request) {
            $ticket = TicketModel::find($request->id);

            $now = Carbon::now();
            $invoiceNumber = $now->format("Y-") . Str::random(4) . $now->format("-m-") . Str::random(4) . $now->format("-d-") . Str::random(12);
            $grossAmount = $ticket->price * $request->integer("quantity");

            Config::$serverKey = env("MIDTRANS_SERVER_KEY");
            Config::$isProduction = env("MIDTRANS_PRODUCTION");
            Config::$isSanitized = true;
            Config::$is3ds = true;
            Config::$overrideNotifUrl = env("MIDTRANS_OVERRIDE_NOTIFICATION_URL");

            $snapUrl = Snap::getSnapUrl([
                "transaction_details" => [
                    "order_id" => $invoiceNumber,
                    "gross_amount" => $grossAmount
                ]
            ]);

            $transaction = TransactionModel::create([
                "invoice_number" => $invoiceNumber,
                "user_id" => auth()->id(),
                "name" => $ticket->name,
                "description" => $ticket->description,
                "price" => $ticket->price,
                "image" => $ticket->image,
                "snap_url" => $snapUrl,
                "quantity" => $request->quantity,
                "date" => $request->date,
                "gross_amount" => $grossAmount
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
            "order_id" => "required|string|exists:$this->transactionTable,invoice_number",
            "transaction_status" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $transaction = TransactionModel::where("invoice_number", $request->order_id)->first();
        TransactionHistoryModel::create([
            "transaction_id" => $transaction->id,
            "status" => MidtransStatusConstant::getValueByName(strtoupper($request->transaction_status))
        ]);

        return ResponseHelper::response();
    }
}
