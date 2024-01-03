<?php

namespace Tests\Unit\News;

use App\Services\News\NewsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use App\Models\News;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\Mock;
use Tests\TestCase;

class NewsServiceTest extends TestCase
{
    protected $mockedHttp;

    protected $mockedNewsService;

    public function tearDown(): void
    {
        parent::tearDown();

        Mockery::close();
    }

    public function setUp(): void
    {
        parent::setUp();

        $this->mockedNewsService = Mockery::mock(NewsService::class)->makePartial();
    }


    /**
     * @test
     * @return void
     */
    public function testCallTheNewsServiceWithWrongSource()
    {
        $mockedNewsService = Mockery::mock(NewsService::class);

        $mockedNewsService->shouldReceive('saveNewsFromApi')
            ->with('asdasdas')
            ->andReturnUsing(function ($sourceKey) {
                throw new \Exception('Invalid source: ' . $sourceKey);
            });

        $this->expectException(\Exception::class);
        $this->expectExceptionMessage('Invalid source: asdasdas');

        $mockedNewsService->saveNewsFromApi('asdasdas');
    }

    public function testSaveFromNewsApi()
    {
        $article = [
            'author'      => 'John Doe',
            'title'       => 'Sample Article',
            'description' => 'This is a sample article.',
            'url'         => 'https://example.com',
            'urlToImage'  => 'https://example.com/image.jpg',
            'publishedAt' => Carbon::now()->format('Y-m-d'),
            'content'     => 'Sample content.',
            'source'      => ['name' => 'News Source'],
        ];



        Http::fake([
            'newsapi.org/v2/everything*' => Http::response([
                'articles' => [$article],
            ], 200),
        ]);

        $mockedNewsService = Mockery::mock(NewsService::class)->makePartial();

        $result = $mockedNewsService->saveNewsFromApi('NewsApi');

        $this->assertTrue($result);
    }

    public function testSaveFromNewYorkTimes()
    {
        $article = [
            'byline'         => ['original' => 'John Doe'],
            'headline'       => ['main' => 'Sample Article'],
            'abstract'       => 'This is a sample article.',
            'web_url'        => 'https://example.com',
            'multimedia'     => [
                [
                    'url' => 'https://example.com/image.jpg',
                ],
            ],
            'pub_date'       => Carbon::now()->format('Y-m-d'),
            'lead_paragraph' => 'Sample content.',
            'source'         => 'News Source',
        ];

        $year = Carbon::now()->format('Y');
        $month = Carbon::now()->format('n');

        Http::fake([
            "https://api.nytimes.com/svc/archive/v1/{$year}/{$month}.json" => Http::response([
                'response' => ['docs' => [$article]],
            ], 200),
        ]);

        $mockedNewsService = Mockery::mock(NewsService::class)->makePartial();

        $result = $mockedNewsService->saveNewsFromApi('NewYorkTimes');

        $this->assertTrue($result);
    }


    public function testSaveFromTheGuardian()
    {
        $article = [
            'fields' => [
                'byline' => 'John Doe',
                'headline' => 'Sample Article',
                'body' => 'Sample content.',
                'main' => 'This is a sample article.',
            ],
            'webTitle' => 'Sample Article',
            'webUrl' => 'https://example.com',
            'webPublicationDate' => Carbon::now()->format('Y-m-d'),
            'source' => 'The Guardian',
        ];

        Http::fake([
            'https://content.guardianapis.com/search' => Http::response([
                'response' => ['results' => [$article]],
            ], 200),
        ]);

        $mockedNewsService = Mockery::mock(NewsService::class)->makePartial();

        $result = $mockedNewsService->saveNewsFromApi('TheGuardian');

        $this->assertTrue($result);
    }
}
