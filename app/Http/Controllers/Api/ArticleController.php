<?php

namespace App\Http\Controllers\Api;

use App\Contracts\Service\ArticleContact;
use App\Http\Controllers\Controller;
use App\Http\Requests\SearchArticleRequest;
use App\Models\Article;
use App\Traits\HttpResponses;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ArticleController extends Controller
{
    use HttpResponses;

    /**
     * @var ArticleContact
     */
    private $articleService;

    public function __construct(ArticleContact $articleService)
    {
        $this->articleService = $articleService;
    }

    /** 
     * Search articles based on the query parameter
     * Returns Random 50 articles if no query found
     * 
     * @param SearchArticleRequest Request with or witout query parameter
     * @return $articles Articles as reponse
     */
    public function search(SearchArticleRequest $request)
    {
        $query = $request->Validated();
        $articles = $this->articleService->searchArticles($query);

        return $this->success($articles);
    }
}
