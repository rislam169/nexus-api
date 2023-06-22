<?php

namespace App\Console\Commands;

use App\Contracts\Service\ArticleContact;
use Illuminate\Console\Command;

class ImportArticles extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'import:articles {--init}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will import the articles from external api and feed this system';

    /**
     * Execute the console command.
     */
    public function handle(ArticleContact $articleService)
    {
        $articleService->importArticles($this->option("init"));
    }
}
