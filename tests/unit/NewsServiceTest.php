<?php

namespace Tests\Unit;

use App\Services\News\NewsService;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Carbon;
use App\Models\News;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Support\Facades\Facade;
use Mockery;
use Mockery\Mock;
use PHPUnit\Framework\TestCase;

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


    // /**
    //  * @test
    //  * @return void
    //  */
    // public function testCallTheNewsServiceWithWrongSource()
    // {
    //     $mockedNewsService = Mockery::mock(NewsService::class);

    //     $mockedNewsService->shouldReceive('saveNewsFromApi')
    //         ->with('asdasdas')
    //         ->andReturnUsing(function ($sourceKey) {
    //             throw new \Exception('Invalid source: ' . $sourceKey);
    //         });

    //     $this->expectException(\Exception::class);
    //     $this->expectExceptionMessage('Invalid source: asdasdas');

    //     $mockedNewsService->saveNewsFromApi('asdasdas');
    // }

    public function testSaveFromNewsApi()
    {
        // Sample article data
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

        // Set the API key
        putenv('NEWS_API_KEY=your_api_key');

        // Call the method you want to test
        $result = $mockedNewsService->saveNewsFromApi('NewsApi');

        // Assert the result
        $this->assertTrue($result);
    }


}
