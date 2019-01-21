<?php

namespace App\Services;

use Cache;
use DOMDocument;
use GuzzleHttp\Client;

class FreenomService
{
    private $freenomAuth = null;
    private $client = null;
    private $params = [];
    private $config = [];
    private $gateway = [
        'login' => 'https://my.freenom.com/dologin.php',
        'renew' => 'https://my.freenom.com/domains.php?a=renewals',
        'list'  => 'https://my.freenom.com/clientarea.php?action=domains'
    ];
    private $baseHeaders = [
        'Content-Type' => 'application/x-www-form-urlencoded',
        'User-Agent'   => 'Mozilla/5.0 (Macintosh; Intel Mac OS X 10_14_0) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/71.0.3578.98 Safari/537.36',
        'Accept'       => 'text/html,application/xhtml+xml,application/xml;q=0.9,image/webp,image/apng,*/*;q=0.8'
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

        $response = $this->client->request(
            'GET',
            $this->gateway['list'],
            [
                'headers' => array_merge(
                    $this->baseHeaders,
                    [
                        'Cookie'  => 'WHMCSZH5eHTGhfvzP=' . $this->getFreenomAuth(),
                        'Referer' => 'https://my.freenom.com/clientarea.php?action=domains'
                    ]
                ),
                'allow_redirects' => false,
            ]
        );

        $domians = $this->getDomainData($response->getBody());
        dd($domians);
    }

    public function getDomainData(String $body = '')
    {
        if (empty($body)) {
            abort(404, '没有找到domain');
        }
        $doc = new DOMDocument;
        @$doc->loadHTML(mb_convert_encoding($body, 'HTML-ENTITIES', 'UTF-8'));
        $table = $doc->getElementsByTagName('table');

        foreach ($table as $key => $value) {
            dd($value);
        }
        dd(1);

        $pattern = '/<table class="table table-striped table-bordered">(<tr>([\s\S]+)<\/tr>)*<\/table>/';

        preg_match_all($pattern, $body, $matches);
        dd($matches);
    }

    public function getFreenomAuth()
    {
        return $this->freenomAuth = Cache::get('freenom_auth');
    }

    public function getClient()
    {
        return $this->client = new Client([
            'timeout' => 10.0
        ]);
    }
}
