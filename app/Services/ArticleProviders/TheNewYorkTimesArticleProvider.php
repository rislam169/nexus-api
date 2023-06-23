<?php

namespace App\Services\ArticleProviders;

use App\Interfaces\ArticleProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheNewYorkTimesArticleProvider implements ArticleProviderInterface
{
    /** Fetch articles from The NewYork Times */
    public function importArticle($isInitialFetch)
    {
        $date = Carbon::now()->format("Ymd");
        $from = $isInitialFetch ? Carbon::now()->subDays(3)->format("Y-m-d") : $date;
        $facetFq = 'fq=news_desk%3A(%22' . implode("%22%2C%20%22", config("article.categories")) . '%22)';  // Search query for new york time api
        try {
            $response = Http::get("https://api.nytimes.com/svc/search/v2/articlesearch.json?begin_date=$from&end_date=$date&facet=true&facet_fields=news_desk&facet_filter=true&" . $facetFq . '&api-key=' . config("newsapi.nytimes.api_key"));
            $response = $response->collect()->get("response");

            return $this->reformat($response["docs"]);
        } catch (\Throwable $th) {
            Log::error($th);
            return [];
        }
    }

    /** 
     * Reformat the articles from the newyork times provider 
     * 
     * @param $articles Unformated array of articles from the new york times
     * @return $articles Formated array of articles 
     */
    public function reformat($articles)
    {
        return array_map(function ($article) {
            $image = array_filter($article["multimedia"], function ($multimedia) {
                return @$multimedia['width'] > 500 && $multimedia['width'] < 700;
            });
            $selectedImage = array_values($image);
            $image_url = isset($selectedImage[0]) ? "https://www.nytimes.com/" . $selectedImage[0]['url'] : $this->defaultImage;

            return [
                "source" => $article["source"] ?? "Anonymous",
                "category" => $article["news_desk"],
                "author" => str_replace("By ", "", $article["byline"]["original"]),
                "title" => $article["headline"]["main"],
                "description" => $article["abstract"],
                "url" => $article["web_url"],
                "image_url" => $image_url,
                "published_at" => Carbon::parse($article["pub_date"])->format('Y/m/d'),
                "created_at" => Carbon::now()->format('Y/m/d H:i:s'),
                "updated_at" => Carbon::now()->format('Y/m/d H:i:s'),
            ];
        }, $articles);
    }
}
