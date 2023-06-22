<?php

namespace App\Contracts\Repositories;

use Prettus\Repository\Contracts\RepositoryInterface;

interface ArticleRepository extends RepositoryInterface
{
    public function insertMultiple($articles);

    public function searchArticles($query);

    public function searchArticlesByPreference($query);
}
