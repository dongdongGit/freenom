<?php

namespace App\Services;

use Cache;
use DOMXPath;
use DOMDocument;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Traits\ProvidesConvenienceMethods;

class FreenomService
{
    use ProvidesConvenienceMethods;

    private $freenomAuth = null;
    private $client = null;
    private $params = [];
    private $config = [];
    private $gateway = [
        'login'  => 'https://my.freenom.com/dologin.php',
        'renew'  => 'https://my.freenom.com/domains.php?a=renewals',
        'domain' => 'https://my.freenom.com/clientarea.php?action=domains',
    ];
    private $baseHeaders = [
        'Content-Type' => 'application/x-www-form-urlencoded',
        'User-Agent'   => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
        'Accept'       => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'
    ];

    private $baseKey = [
        'domain',
        'register_date',
        'expires_date',
        'status',
        'type'
    ];

    public function __construct()
    {
        $this->config = config('freenom');
    }

    public function isLogin()
    {
        if (!array_get($this->config, 'username')
             || !array_get($this->config, 'password')
        ) {
            // TODO: exception
            abort(403, '随便写的');
        }

        if (empty($this->client)) {
            $this->client = $this->getClient();
        }

        if (empty($this->getFreenomAuth()) && empty(Cache::get('freenom_auth'))) {
            return false;
        }

        return true;
    }

    public function login()
    {
        if ($this->isLogin()) {
            return $this;
        }

        $response = $this->client->request(
            'POST',
            $this->gateway['login'],
            [
                'allow_redirects' => false,
                'headers'         => array_merge($this->baseHeaders, [
                    'Referer' => 'https://my.freenom.com/clientarea.php?incorrect=true'
                ]),
                'form_params' => array_only($this->config, ['username', 'password'])
            ]
        );

        $responseCookie = array_get($response->getHeaders(), 'Set-Cookie', []);

        foreach ($responseCookie as $key => &$value) {
            if (!preg_match('/^WHMCSZH5eHTGhfvzP=[\S\s]+$/', $value)) {
                unset($responseCookie[$key]);
            }
        }

        $auth = array_last(explode('=', array_first(explode(';', array_last($responseCookie)))));
        Cache::put('freenom_auth', $auth, 10);
        $this->freenomAuth = $auth;

        return $this;
    }

    public function list()
    {
        $this->login();

        $parse = [
            'headers' => array_merge(
                $this->baseHeaders,
                [
                    'Cookie'  => 'WHMCSZH5eHTGhfvzP=' . $this->getFreenomAuth(),
                    'Referer' => 'https://my.freenom.com/clientarea.php?action=domains'
                ]
            ),
            'allow_redirects' => true,
        ];

        $response = $this->client->request(
            'GET',
            $this->gateway['domain'],
            $parse
        );

        $body = $response->getBody();

        if (preg_match('/Next Page/', $body)) {
            preg_match('/<input[A-Za-z "=]+value=\"([\dA-Fa-f]+)\"[^>]+>/', $body, $matchesInput);
            $token = array_last($matchesInput);

            $response = $this->client->request(
                'POST',
                $this->gateway['domain'],
                array_merge($parse, [
                    'form_params' => [
                        'itemlimit' => 'all',
                        'token'     => $token
                    ]
                ])
            );
        }

        return $this->getDomainData($response->getBody());
    }

    public function sync()
    {
        // TODO:log
        $data = $this->list();
        $this->user()->domains()->delete();
        $this->user()->domains()->createMany($data);
    }

    public function getDomainData(String $body = '')
    {
        if (empty($body)) {
            abort(404, '没有找到domain');
        }

        $domains = [];
        $doc = new DOMDocument();
        $page = mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8');
        @$doc->loadHTML($page);
        $xpath = new DOMXPath($doc);
        $dom = $xpath->query('//*/table[contains(@class, "table-striped")]/tbody/tr');
        $domainIds = $xpath->query('//*/table[contains(@class, "table-striped")]/tbody/tr/*/a[contains(@class, "pullRight")]');

        foreach ($dom as $key => $item) {
            $domains[$key] = [];

            foreach ($item->childNodes as $index => $childItem) {
                if ($childItem->nodeType == 1 && $index <= 9) {
                    $keyName = array_get($this->baseKey, strval(($index - 1) / 2));
                    $domains[$key][$keyName] = strtolower(trim($childItem->nodeValue));

                    if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $childItem->nodeValue)) {
                        $domains[$key][$keyName] = $this->formatDate($domains[$key][$keyName]);
                    }
                }
            }
        }

        return $domains;
    }

    public function getFreenomAuth()
    {
        return $this->freenomAuth = Cache::get('freenom_auth');
    }

    public function getClient()
    {
        return $this->client = new Client([
            'timeout' => 5.0
        ]);
    }

    public function formatDate(String $date, String $format = 'Y-m-d')
    {
        $date = explode('/', $date);

        return Carbon::create($date[2], $date[1], $date[0])->format($format);
    }
}
