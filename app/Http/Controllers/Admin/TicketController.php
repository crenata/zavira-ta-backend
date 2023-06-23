<?php

namespace App\Http\Controllers\Admin;

use App\Helpers\ResponseHelper;
use App\Helpers\StorageHelper;
use App\Http\Controllers\Controller;
use App\Models\TicketModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class TicketController extends Controller {
    protected string $ticketTable;

    public function __construct() {
        $this->ticketTable = (new TicketModel())->getTable();
    }

    public function get(Request $request) {
        $tickets = TicketModel::orderByDesc("id")->paginate();

        return ResponseHelper::response($tickets);
    }

    public function set(Request $request, TicketModel $ticket) {
        $ticket->name = $request->name;
        $ticket->description = $request->description;
        $ticket->price = $request->price;
        if ($request->hasFile("image")) $ticket->image = StorageHelper::save($request, "image", "tickets");
        $ticket->save();

        return ResponseHelper::response($ticket);
    }

    public function add(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "price" => "required|numeric|min:1",
            "description" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, new TicketModel());
    }

    public function edit(Request $request) {
        $validator = Validator::make($request->all(), [
            "id" => "required|numeric|exists:$this->ticketTable,id",
            "name" => "required|string",
            "price" => "required|numeric|min:1",
            "description" => "required|string"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return $this->set($request, TicketModel::find($request->id));
    }

    public function delete(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->ticketTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        TicketModel::find($id)->delete();

        return ResponseHelper::response();
    }
}
