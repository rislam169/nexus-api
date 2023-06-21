<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Article;
use App\Traits\HttpResponses;
use Carbon\Carbon;
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

        // Search source if available in query parameter
        if (!empty($request->get("source"))) {
            $articles = $articles->where("source", $request->get("source"));
        }

        // Search articles publish at or later of fromDate
        if (!empty($request->get("fromDate"))) {
            $articles = $articles->whereDate("published_at", ">=", Carbon::parse($request->get("fromDate")));
        }

        // Search articles publish at or before of toDate
        if (!empty($request->get("toDate"))) {
            $articles = $articles->whereDate("published_at", "<=", Carbon::parse($request->get("toDate")));
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
