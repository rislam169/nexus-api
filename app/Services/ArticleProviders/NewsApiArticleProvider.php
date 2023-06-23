<?php

namespace App\Services\ArticleProviders;

use App\Interfaces\ArticleProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsApiArticleProvider implements ArticleProviderInterface
{
    /** Fetch articles from The NewYork Times */
    public function importArticle($isInitialFetch)
    {
        /** Fetch articles from News Api */

        try {
            $from = $isInitialFetch ? Carbon::now()->subDays(3)->format("Y-m-d") : Carbon::now()->format("Ymd");
            $to = Carbon::now()->format("Y-m-d");

            foreach (config("article.categories") as $category) {
                $response = Http::get("https://newsapi.org/v2/everything?q=" . strtolower($category) . "&from=$from&to=$to&apiKey=" . config("newsapi.newsapi.api_key"));
                $response = $response->collect()->get('articles');
                if (isset($response[0])) {
                    return $this->reformat($response, $category);
                }
            }
        } catch (\Throwable $th) {
            Log::error($th);
            return [];
        }
    }

    /** 
     * Reformat the articles from the News api 
     * 
     * @param $articles Unformated array of articles from the  News api
     * @return $articles Formated array of articles 
     */
    public function reformat($articles,  $category = "Anonymus")
    {
        return array_map(function ($article) use ($category) {
            return [
                "source" => $article["source"]["name"],
                "category" => $category,
                "author" => $article["author"] ?? "Anonymous",
                "title" => $article["title"],
                "description" => $article["description"],
                "url" => $article["url"],
                "image_url" => $article["urlToImage"] ?? config("article.defaultImage"),
                "published_at" => Carbon::parse($article["publishedAt"])->format('Y/m/d'),
                "created_at" => Carbon::now()->format('Y/m/d H:i:s'),
                "updated_at" => Carbon::now()->format('Y/m/d H:i:s'),
            ];
        }, $articles);
    }
}
