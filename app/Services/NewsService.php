<?php

namespace App\Services;

use App\Models\Article;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class NewsService
{
    /** News and article categoris to be fetched */
    private $categories = [
        "General",
        "Business",
        "Entertainment",
        "Health",
        "Science",
        "Sports",
        "Technology"
    ];
    /**
     * Fetch news from external api and store in our database
     * 
     * @param $isInitialFetch True if running first system boot up, default false
     */
    public function fetchArticles($isInitialFetch = false)
    {
        /** Holds the articles from different data sources  */
        $articles = [];

        /** Fetch articles from The NewYork Times */
        $date = Carbon::now()->format("Ymd");
        $facetFq = 'fq=news_desk%3A(%22' . implode("%22%2C%20%22", $this->categories) . '%22)';  // Search query for new york time api
        try {
            $response = Http::get("https://api.nytimes.com/svc/search/v2/articlesearch.json?begin_date=$date&end_date=$date&facet=true&facet_fields=news_desk&facet_filter=true&" . $facetFq . '&api-key=' . config("newsapi.nytimes.api_key"));
            $response = $response->collect()->get("response");
            $articles = $this->formatNewYorkTimesArticle($response["docs"]);
        } catch (\Throwable $th) {
            Log::error($th);
        }



        /** Fetch articles from The Gurdian */
        $date = Carbon::now()->format("Y-m-d");
        try {
            foreach ($this->categories as $category) {
                $response = Http::get("https://content.guardianapis.com/search?from-date=$date&to-date=$date&section=" . strtolower($category) . "&show-fields=headline,byline,thumbnail&show-references=author&show-elements=image&api-key=" . config("newsapi.thegurdian.api_key"));
                $response = $response->collect()->get("response")["results"];
                if (isset($response[0])) {
                    $articles = array_merge($articles, $this->formatTheGurdianArticle($response));
                }
            }
        } catch (\Throwable $th) {
            Log::error($th);
        }


        /** Fetch articles from News Api */
        try {
            foreach ($this->categories as $category) {
                $from = $isInitialFetch ? Carbon::now()->subDays(3)->format("Y-m-d") : $date;
                $response = Http::get("https://newsapi.org/v2/everything?q=" . strtolower($category) . "&from=$from&to=$date&apiKey=" . config("newsapi.newsapi.api_key"));
                $response = $response->collect()->get('articles');
                if (isset($response[0])) {
                    $articles = array_merge($articles, $this->formatNewsApiArticle($response, $category));
                }
            }
        } catch (\Throwable $th) {
            Log::error($th);
        }

        $articles = collect($articles); // Make a collection to use the chunk method

        // It will chunk the dataset in smaller collections containing 500 values each. 
        // Play with the value to get best result
        $chunks = $articles->chunk(30);

        foreach ($chunks as $chunk) {
            Article::insert($chunk->toArray());
        }
    }

    /** 
     * Reformat the articles from the newyork times provider 
     * 
     * @param $articles Unformated array of articles from the new york times
     * @return $articles Formated array of articles 
     */
    public function formatNewYorkTimesArticle($articles)
    {
        return array_map(function ($article) {
            $image = array_filter($article["multimedia"], function ($multimedia) {
                return @$multimedia['width'] > 500 && $multimedia['width'] < 700;
            });
            $selectedImage = array_values($image);
            $image_url = isset($selectedImage[0]) ? "https://www.nytimes.com/" . $selectedImage[0]['url'] : "https://www.nytimes.com/images/2018/02/06/sports/06drape-web1/merlin_133302105_c3e586b3-6eff-4b50-adfb-71b88c91091f-popup.jpg";

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

    /** 
     * Reformat the articles from the gurdian provider 
     * 
     * @param $articles Unformated array of articles from the gurdian
     * @return $articles Formated array of articles 
     */
    public function formatTheGurdianArticle($articles)
    {
        return array_map(function ($article) {
            return [
                "source" => "The Gurdian",
                "category" => $article["sectionName"],
                "author" => $article["fields"]["byline"],
                "title" => $article["webTitle"],
                "description" => $article["webTitle"],
                "url" => $article["webUrl"],
                "image_url" => $article["fields"]["thumbnail"],
                "published_at" => Carbon::parse($article["webPublicationDate"])->format('Y/m/d'),
                "created_at" => Carbon::now()->format('Y/m/d H:i:s'),
                "updated_at" => Carbon::now()->format('Y/m/d H:i:s'),
            ];
        }, $articles);
    }

    /** 
     * Reformat the articles from the News api 
     * 
     * @param $articles Unformated array of articles from the  News api
     * @return $articles Formated array of articles 
     */
    public function formatNewsApiArticle($articles, $category)
    {
        return array_map(function ($article) use ($category) {
            return [
                "source" => $article["source"]["name"],
                "category" => $category,
                "author" => $article["author"] ?? "Anonymous",
                "title" => $article["title"],
                "description" => $article["description"],
                "url" => $article["url"],
                "image_url" => $article["urlToImage"] ?? "https://i.insider.com/648de17251ea980019d6c024?width=1200&format=jpeg",
                "published_at" => Carbon::parse($article["publishedAt"])->format('Y/m/d'),
                "created_at" => Carbon::now()->format('Y/m/d H:i:s'),
                "updated_at" => Carbon::now()->format('Y/m/d H:i:s'),
            ];
        }, $articles);
    }
}
