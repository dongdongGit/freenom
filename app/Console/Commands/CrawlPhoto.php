<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use GuzzleHttp\Client;
use App\Services\ImageService;

class CrawlPhoto extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'crawl:photo';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '爬取图片';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        set_time_limit(0);

        $client = new Client([
            'timeout' => 5.0
        ]);

        $url = 'http://hyjf.5188cms.com/static/wechat/uploads/';

        $response = $client->request(
            'GET',
            $url,
            [
                'headers' => [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                    'User-Agent'   => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
                    'Accept'       => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'
                ],
                'allow_redirects' => true,
            ]
        );

        $body = $response->getBody();

        $doc = new \DOMDocument();
        $page = mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8');
        @$doc->loadHTML($page);
        $xpath = new \DOMXPath($doc);
        $dom = $xpath->query('//*/td/a');
        $imgs = [];

        foreach ($dom as $key => $item) {
            if ($key > 11302) {
                $href = $item->attributes->getNamedItem('href')->nodeValue;

                if (preg_match('/\d+(\.jpg|\.png|\.jpeg)/', $href)) {
                    try {
                        $image_service = new ImageService($url . $href);
                        $image_service->save();
                    } catch (\Exception $e) {
                    }
                }
            }
        }
    }
}
