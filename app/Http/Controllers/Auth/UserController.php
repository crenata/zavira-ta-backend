<?php

namespace App\Http\Controllers\Auth;

use App\Constants\TokenConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Mail\PasswordResetMail;
use App\Mail\VerifyMail;
use App\Models\PasswordResetModel;
use App\Models\UserModel;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;

class UserController extends Controller {
    protected string $userTable, $passwordResetTable;

    public function __construct() {
        $this->userTable = (new UserModel())->getTable();
        $this->passwordResetTable = (new PasswordResetModel())->getTable();
    }

    public function get(Request $request) {
        return ResponseHelper::response($request->user());
    }

    public function register(Request $request) {
        $validator = Validator::make($request->all(), [
            "name" => "required|string",
            "email" => "required|string|email|unique:$this->userTable,email",
            "password" => "required|string|min:8",
            "confirm_password" => "required|string|min:8|same:password"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        return DB::transaction(function () use ($request) {
            $token = Str::random(64);
            $user = UserModel::create([
                "name" => $request->name,
                "email" => $request->email,
                "token" => $token,
                "password" => Hash::make($request->password)
            ]);

            Mail::to($request->email)->send(new VerifyMail($token));

            return ResponseHelper::response([
                "user" => $user,
                "token" => $user->createToken(TokenConstant::TOKEN_NAME, [TokenConstant::AUTH_USER])->plainTextToken
            ]);
        });
    }

    public function login(Request $request) {
        $validator = Validator::make($request->all(), [
            "email" => "required|string|email|exists:$this->userTable,email",
            "password" => "required|string|min:8",
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $user = UserModel::where("email", $request->email)->first();

        if (!Hash::check($request->password, $user->password)) return ResponseHelper::response(null, "The provided credentials are incorrect.", 401);

        return ResponseHelper::response([
            "user" => $user,
            "token" => $user->createToken(TokenConstant::TOKEN_NAME, [TokenConstant::AUTH_USER])->plainTextToken
        ]);
    }

    public function verify(Request $request, $token) {
        $validator = Validator::make([
            "token" => $token
        ], [
            "token" => "required|string|min:64|max:64|exists:$this->userTable,token"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $user = UserModel::where("token", $request->token)->first();
        $user->token = null;
        $user->email_verified_at = Carbon::now();
        $user->save();

        return ResponseHelper::response($user);
    }

    public function sendForgotPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            "email" => "required|string|email|exists:$this->userTable,email"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $token = Str::random(64);
        PasswordResetModel::create([
            "email" => $request->email,
            "token" => $token
        ]);

        Mail::to($request->email)->send(new PasswordResetMail($token));

        return ResponseHelper::response();
    }

    public function changeForgotPassword(Request $request) {
        $validator = Validator::make($request->all(), [
            "token" => "required|string|exists:$this->passwordResetTable,token",
            "password" => "required|string|min:8",
            "confirm_password" => "required|string|min:8|same:password"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $reset = PasswordResetModel::where("token", $request->token)->first();
        $user = UserModel::where("email", $reset->email)->first();
        $user->password = Hash::make($request->password);
        $user->save();
        $reset->forceDelete();

        return ResponseHelper::response();
    }

    public function logout(Request $request) {
        $request->user()->currentAccessToken()->delete();

        return ResponseHelper::response();
    }
}
