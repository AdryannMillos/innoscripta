<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Interfaces\News\NewsServiceInterface;

class NewsController extends Controller
{
    protected $newsService;

    public function __construct(NewsServiceInterface $newsService)
    {
        $this->newsService = $newsService;
    }

    public function store()
    {
        try {
            // $this->newsService->saveNewsFromApi('NewsApi');

            // $this->newsService->saveNewsFromApi('NewYorkTimes');

            $this->newsService->saveNewsFromApi('TheGuardian');

            return response()->json(['message' => 'News saved!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
