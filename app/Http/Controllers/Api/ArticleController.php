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
    public function index()
    {
        $articles = Article::inRandomOrder()->limit(50)->get();
        return $this->success($articles);
    }
}
