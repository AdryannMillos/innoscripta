<?php

namespace App\Interfaces\News;

interface NewsServiceInterface
{
    public function saveNewsFromApi($sourceKey);
}
