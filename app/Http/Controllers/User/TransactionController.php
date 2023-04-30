<?php

namespace App\Http\Controllers\User;

use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\TransactionModel;
use Illuminate\Http\Request;

class TransactionController extends Controller {
    public function get(Request $request) {
        $data = TransactionModel::with("latestHistory", "histories")
            ->orderByDesc("id")
            ->paginate();

        return ResponseHelper::response($data);
    }
}
