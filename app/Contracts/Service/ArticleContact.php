<?php

namespace App\Contracts\Service;

interface ArticleContact
{
    public function importArticles($isInitialFetch = false);

    public function searchArticles($query, $user = null);
}
