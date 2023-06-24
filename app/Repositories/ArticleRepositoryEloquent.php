<?php

namespace App\Repositories;

use App\Contracts\Repositories\ArticleRepository;
use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Contracts\Database\Eloquent\Builder;
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
        // Make a collection to use the chunk method
        $articles = collect($articles);

        // It will chunk the dataset in smaller collections containing 500 values each. 
        // Play with the value to get best result
        $chunks = $articles->chunk(50);

        foreach ($chunks as $chunk) {
            /** 
             * Also possible with batch inserting like bellow line, but we loose meiliserch indexing
             * $this->model->insert($chunk->toArray());
             * 
             * Although it is possible to batch indexing to meiliserch, but will more complicated
             */

            foreach ($chunk->toArray() as $article) {
                $this->model->create($article);
            }
        }
    }

    /**
     * Perform search operation on article model
     * 
     * @param $query Array of query data
     * @return Collection of articles
     */
    public function searchArticles($query)
    {
        $articles = $this->model;

        // Search for keyword if available in query
        if (!empty($query["searchKey"])) {
            $articles = $articles->search($query["searchKey"]);
        }

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

        return $articles->get()->shuffle();
    }

    public function searchArticlesByPreference($query)
    {
        $articles = $this->model->inRandomOrder();

        $isFirstQuery = true;
        foreach ($query as $key => $queryField) {
            if ($queryField) {
                foreach ($queryField as $queryFieldValue) {
                    if ($isFirstQuery) {
                        $articles = $articles->where($key, $queryFieldValue);
                        $isFirstQuery = false;
                    } else {
                        $articles = $articles->orWhere($key, $queryFieldValue);
                    }
                }
            }
        }

        return $articles->limit(70)->get();
    }
}
