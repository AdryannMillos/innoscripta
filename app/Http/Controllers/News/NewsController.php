<?php

namespace App\Http\Controllers\News;

use App\Http\Controllers\Controller;
use App\Interfaces\News\NewsServiceInterface;
use Carbon\Carbon;
use Illuminate\Http\Request;

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
            $this->newsService->saveNewsFromApi('NewsApi');

            $this->newsService->saveNewsFromApi('NewYorkTimes');

            $this->newsService->saveNewsFromApi('TheGuardian');

            return response()->json(['message' => 'News saved!'], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }

    public function index(Request $request)
    {
        try {
            $pageSize = (int) $request->query('pageSize');
            $page =  (int) $request->query('page');
            $search = $request->query('search');
            $author = $request->query('author');
            $source = $request->query('source');
            $fromDate = $request->query('fromDate');
            $toDate = $request->query('toDate');

            $news = $this->newsService->getNews($search, $author, $source, $fromDate, $toDate, $pageSize, $page);

            return response()->json([$news], 200);
        } catch (\Exception $e) {
            return response()->json(['message' => $e->getMessage()], 500);
        }
    }
}
