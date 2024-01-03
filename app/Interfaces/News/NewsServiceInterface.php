<?php

namespace App\Interfaces\News;

interface NewsServiceInterface
{
    public function saveNewsFromApi(string $sourceKey);
}
