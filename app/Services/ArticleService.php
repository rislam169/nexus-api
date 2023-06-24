<?php

namespace App\Services;

use App\Contracts\Repositories\ArticleRepository;
use App\Contracts\Service\ArticleContact;
use App\Services\ArticleProviders\NewsApiArticleProvider;
use App\Services\ArticleProviders\TheGurdianArticleProvider;
use App\Services\ArticleProviders\TheNewYorkTimesArticleProvider;

class ArticleService implements ArticleContact
{
    /**
     * @var ArticleRepository
     */
    private $articleRepository;

    /**
     * @var UserSettingService
     */
    private $userSettingService;

    public function __construct(ArticleRepository $articleRepository, UserSettingService $userSettingService)
    {
        $this->articleRepository = $articleRepository;
        $this->userSettingService = $userSettingService;
    }

    /**
     * Fetch news from external api and store in our database
     * 
     * @param $isInitialFetch True if running first system boot up, default false
     */
    public function importArticles($isInitialFetch = false)
    {
        /** Holds the articles from different data sources  */
        $articles = [];

        /** Get articles from The NewYork Times */
        $theNewYorkTimesProvider = new TheNewYorkTimesArticleProvider();
        $theNewYorkTimesArticles = $theNewYorkTimesProvider->importArticle($isInitialFetch);
        $articles = array_merge($articles, $theNewYorkTimesArticles);

        /** Fetch articles from The Gurdian */
        $theNewYorkTimesProvider = new TheGurdianArticleProvider();
        $theNewYorkTimesArticles = $theNewYorkTimesProvider->importArticle($isInitialFetch);
        $articles = array_merge($articles, $theNewYorkTimesArticles);

        /** Fetch articles from News Api */
        $newsApiProvider = new NewsApiArticleProvider();
        $newsApiArticles = $newsApiProvider->importArticle($isInitialFetch);
        $articles = array_merge($articles, $newsApiArticles);

        /** Insert articles to database */
        $this->articleRepository->insertMultiple($articles);
    }

    /**
     * Collect articles from repository after searching
     * 
     * @param $query Array of query data
     * @param $userId Id of the logged in user
     * @return Collection of articles
     */
    public function searchArticles($query, $userId = null)
    {
        if (empty($query) && !empty($userId)) {
            $userSetting = $this->userSettingService->getSettingByUserId($userId, ["source", "category", "author"])->toArray();
            return $this->articleRepository->searchArticlesByPreference($userSetting);
        }
        return $this->articleRepository->searchArticles($query);
    }
}
