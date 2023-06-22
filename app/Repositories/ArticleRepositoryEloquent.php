<?php

namespace App\Repositories;

use App\Contracts\Repositories\ArticleRepository;
use App\Models\Article;
use Carbon\Carbon;
use Prettus\Repository\Eloquent\BaseRepository;

class ArticleRepositoryEloquent extends BaseRepository implements ArticleRepository
{

    /**
     * Specify Model class name
     *
     * @return string
     */
    function model()
    {
        return Article::class;
    }

    public function insertMultiple($articles)
    {
        return $this->model->insert($articles);
    }

    /**
     * Perform search operation on article model
     * 
     * @param $query Array of query data
     * @return Collection of articles
     */
    public function searchArticles($query)
    {
        $articles = $this->model->inRandomOrder();

        // Search category if available in query
        if (!empty($query["category"])) {
            $articles = $articles->where("category", $query["category"]);
        }

        // Search source if available in query
        if (!empty($query["source"])) {
            $articles = $articles->where("source", $query["source"]);
        }

        // Search articles publish at or later of fromDate if available in query
        if (!empty($query["fromDate"])) {
            $articles = $articles->whereDate("published_at", ">=", Carbon::parse($query["fromDate"]));
        }

        // Search articles publish at or before of toDate if available in query
        if (!empty($query["toDate"])) {
            $articles = $articles->whereDate("published_at", "<=", Carbon::parse($query["toDate"]));
        }

        // Search for keyword if available in query
        if (!empty($query["searchKey"])) {
            $articles = $articles->where("title", 'like', '%' . $query["searchKey"] . '%')
                ->orWhere("description", 'like', '%' . $query["searchKey"] . '%')
                ->orWhere("category", $query["searchKey"]);
        }
        return $articles->limit(50)->get();
    }
}
