<?php

namespace App\Interfaces\News;

interface NewsServiceInterface
{
    public function saveNewsFromApi(string $sourceKey);

    public function getNews(string $search = null, string $author = null, string $source = null, string $fromDate = null, string $toDate = null, int $pageSize = 10, int $page = 1);
}
