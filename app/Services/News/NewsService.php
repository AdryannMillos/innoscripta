<?php

namespace App\Services\News;

use App\Interfaces\News\NewsServiceInterface;
use App\Models\News;
use Carbon\Carbon;
use Illuminate\Support\Facades\Http;

class NewsService implements NewsServiceInterface
{
    public function saveNewsFromApi(string $sourceKey)
    {
        try {
            switch ($sourceKey) {
                case 'NewsApi':
                    $page = 1;
                    $apiKey = env('NEWS_API_KEY');

                    do {
                        $params = $this->getNewsApiParams($apiKey, $page);
                        $response = Http::get('https://newsapi.org/v2/top-headlines', $params);

                        if (isset($response->json()['articles']) && count($response->json()['articles']) > 0) {
                            $this->saveFromNewsApi($response->json()['articles']);
                            $page++;
                        }
                    } while (isset($response->json()['articles']) && count($response->json()['articles']) > 0);
                    break;

                case 'NewYorkTimes':
                    $apiKey = env('NEW_YORk_TIMES_API_KEY');
                    $params = $this->getNewYorkTimesParams($apiKey);
                    $year = Carbon::now()->format('Y');
                    $month = Carbon::now()->format('n');

                    $response = Http::get("https://api.nytimes.com/svc/archive/v1/{$year}/{$month}.json", $params);
                    if ($response->json() !== null) {
                        $data = $response->json()['response']['docs'];

                        if (isset($data) && count($data) > 0) {
                            foreach ($data as $article) {
                                if (Carbon::parse($article['pub_date'])->format('Y-m-d') === Carbon::now()->format('Y-m-d')) {
                                    $this->saveFromNewYorkTimes([$article]);
                                }
                            }
                        }
                    }
                    break;

                case 'TheGuardian':
                    $page = 1;
                    $apiKey = env('THE_GUARDIAN_API_KEY');

                    do {
                        $params = $this->getTheGuardianParams($apiKey, $page);
                        $response = Http::get('https://content.guardianapis.com/search', $params);

                        if (isset($response->json()['response']['results']) && count($response->json()['response']['results']) > 0) {
                            $this->saveFromTheGuardian($response->json()['response']['results']);
                            $page++;
                        }
                    } while (isset($response->json()['response']['results']) && count($response->json()['response']['results']) > 0);
                    break;
                default:
                    throw new \InvalidArgumentException('Unknown source: ' . $sourceKey);
            }

            return true;
        } catch (\Exception $e) {
            return throw new \Exception($e->getMessage());
        }
    }

    private function getNewsApiParams($apiKey, $page = 1)
    {
        return [
            'pageSize' => 100,
            'page' => $page,
            'language' => 'en',
            'from' =>  Carbon::now()->format('Y-m-d'),
            'sortBy' => 'popularity',
            'apiKey' => $apiKey,
        ];
    }

    private function getNewYorkTimesParams($apiKey)
    {
        return [
            'api-key' => $apiKey,
        ];
    }

    private function getTheGuardianParams($apiKey, $page = 1)
    {
        return [
            'page-size' => 50,
            'page' => $page,
            'from-date' =>  Carbon::now()->format('Y-m-d'),
            'show-fields' => 'all',
            'api-key' => $apiKey,
        ];
    }

    private function saveFromNewsApi($articles)
    {
        foreach ($articles as $article) {
            if ($article['url'] === 'https://removed.com' || !isset($article['url'])) continue;

            $this->saveArticleToDatabase(
                $article['author'],
                $article['title'],
                $article['description'],
                $article['url'],
                $article['urlToImage'],
                $article['publishedAt'] ? Carbon::parse($article['publishedAt']) : null,
                $article['content'] ?? 'No Content',
                $article['source']['name']
            );
        }
    }

    private function saveFromNewYorkTimes($articles)
    {
        foreach ($articles as $article) {
            if ($article['web_url'] === 'https://removed.com' || !isset($article['web_url'])) continue;

            $this->saveArticleToDatabase(
                $article['byline']['original'],
                $article['headline']['main'],
                $article['abstract'],
                $article['web_url'],
                isset($article['multimedia'][0]) ? $article['multimedia'][0]['url'] : null,
                Carbon::parse($article['pub_date']),
                $article['lead_paragraph'],
                $article['source']
            );
        }
    }

    private function saveFromTheGuardian($articles)
    {
        foreach ($articles as $article) {
            if ($article['webUrl'] === 'https://removed.com' || !isset($article['webUrl'])) continue;

            $this->saveArticleToDatabase(
                isset($article['fields']['byline']) ?$article['fields']['byline'] : null,
                $article['webTitle'],
                $article['fields']['headline'],
                $article['webUrl'],
                $article['fields']['main'],
                Carbon::parse($article['webPublicationDate']),
                $article['fields']['body'],
                'The Guardian'
            );
        }
    }

    private function saveArticleToDatabase($author, $title, $description, $url, $imageUrl, $publishedAt, $content, $source)
    {
        $news = News::where('url', $url)->first();
        $imageUrl = substr($imageUrl, 0, 255);
        $content = substr($content, 0, 65535);
        if (!$news) {
            News::create([
                'author' => $author,
                'title' => $title,
                'description' => $description,
                'url' => $url,
                'image_url' => $imageUrl,
                'published_at' => $publishedAt,
                'content' => $content,
                'source' => $source,
            ]);
        }
    }
}
