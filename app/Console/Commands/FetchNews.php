<?php

namespace App\Console\Commands;

use App\Interfaces\News\NewsServiceInterface;
use Illuminate\Console\Command;

class FetchNews extends Command
{
    protected $signature = 'fetch:news';
    protected $description = 'Fetch news from APIs';

    public function handle(NewsServiceInterface $newsService)
    {
        try {
            $newsService->saveNewsFromApi('NewsApi');
            $newsService->saveNewsFromApi('NewYorkTimes');
            $newsService->saveNewsFromApi('TheGuardian');
            $this->info('News fetched successfully!');
        } catch (\Exception $e) {
            $this->error('Error fetching news: ' . $e->getMessage());
        }
    }
}
