<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Traits\HttpResponses;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use HttpResponses;

    /** Returns the response with articles */
    public function index(Request $request)
    {

        $articles = Article::inRandomOrder();

        // Search category if available in query parameter
        if (!empty($request->get("category"))) {
            $articles = $articles->where("category", $request->get("category"));
        }

        // Search for keyword if available in query parameter
        if (!empty($request->get("searchKey"))) {
            $articles = $articles->where("title", 'like', '%' . $request->get("searchKey") . '%')
                ->orWhere("description", 'like', '%' . $request->get("searchKey") . '%')
                ->orWhere("category", $request->get("searchKey"));
        }

        return $this->success($articles->limit(50)->get());
    }
}
