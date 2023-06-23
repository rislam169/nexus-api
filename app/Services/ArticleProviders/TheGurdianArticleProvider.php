<?php

namespace App\Services\ArticleProviders;

use App\Interfaces\ArticleProviderInterface;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TheGurdianArticleProvider implements ArticleProviderInterface
{
    /** Fetch articles from The Gurdian */
    public function importArticle($isInitialFetch)
    {
        $date = Carbon::now()->format("Y-m-d");
        try {
            foreach (config("article.categories") as $category) {
                $response = Http::get("https://content.guardianapis.com/search?from-date=$date&to-date=$date&section=" . strtolower($category) . "&show-fields=headline,byline,thumbnail&show-references=author&show-elements=image&api-key=" . config("newsapi.thegurdian.api_key"));
                $response = $response->collect()->get("response")["results"];
                if (isset($response[0])) {
                    return $this->reformat($response);
                }
            }
        } catch (\Throwable $th) {
            Log::error($th);
            return [];
        }
    }

    /** 
     * Reformat the articles from the gurdian provider 
     * 
     * @param $articles Unformated array of articles from the gurdian
     * @return $articles Formated array of articles 
     */
    public function reformat($articles)
    {
        return array_map(function ($article) {
            return [
                "source" => "The Gurdian",
                "category" => $article["sectionName"],
                "author" => $article["fields"]["byline"],
                "title" => $article["webTitle"],
                "description" => $article["webTitle"],
                "url" => $article["webUrl"],
                "image_url" => $article["fields"]["thumbnail"] ?? config("article.defaultImage"),
                "published_at" => Carbon::parse($article["webPublicationDate"])->format('Y/m/d'),
                "created_at" => Carbon::now()->format('Y/m/d H:i:s'),
                "updated_at" => Carbon::now()->format('Y/m/d H:i:s'),
            ];
        }, $articles);
    }
}
