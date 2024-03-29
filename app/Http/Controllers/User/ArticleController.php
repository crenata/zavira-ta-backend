<?php

namespace App\Http\Controllers\User;

use App\Constants\ArticleTypeConstant;
use App\Helpers\ResponseHelper;
use App\Http\Controllers\Controller;
use App\Models\ArticleModel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ArticleController extends Controller {
    protected string $articleTable;

    public function __construct() {
        $this->articleTable = (new ArticleModel())->getTable();
    }

    public function getHome(Request $request) {
        $articles = ArticleModel::where("type", ArticleTypeConstant::HOME)->orderByDesc("id")->get();

        return ResponseHelper::response($articles);
    }
    public function getProgram(Request $request) {
        $articles = ArticleModel::where("type", ArticleTypeConstant::PROGRAM)->orderByDesc("id")->paginate();

        return ResponseHelper::response($articles);
    }
    public function getInformation(Request $request) {
        $article = ArticleModel::where("type", ArticleTypeConstant::INFORMATION)->orderByDesc("id")->first();

        return ResponseHelper::response($article);
    }
    public function getSop(Request $request) {
        $articles = ArticleModel::where("type", ArticleTypeConstant::SOP)->orderByDesc("id")->paginate();

        return ResponseHelper::response($articles);
    }

    public function getDetail(Request $request, $id) {
        $validator = Validator::make([
            "id" => $id
        ], [
            "id" => "required|numeric|exists:$this->articleTable,id"
        ]);
        if ($validator->fails()) return ResponseHelper::response(null, $validator->errors()->first(), 400);

        $article = ArticleModel::find($id);

        return ResponseHelper::response($article);
    }
}
