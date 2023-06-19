<?php

namespace App\Console\Commands;

use App\Services\NewsService;
use Illuminate\Console\Command;

class FetchNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fetch:news {--init}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This will fetch the news from external api and feed this system';

    /**
     * Execute the console command.
     */
    public function handle(NewsService $fetchExternalNews)
    {
        $fetchExternalNews->fetchArticles($this->option("init"));
    }
}
