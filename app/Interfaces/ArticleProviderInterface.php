<?php

namespace App\Interfaces;

interface ArticleProviderInterface
{
    public function importArticle($isInitialFetch);

    public function reformat($articles);
}
