<?php

namespace App\Services;

use Cache;
use Carbon\Carbon;
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
                'allow_redirects' => true,
            ]
        );

        return $this->getDomainData($response->getBody());
    }

    public function sync()
    {
    }

    public function getDomainData(String $body = '')
    {
        if (empty($body)) {
            abort(404, '没有找到domain');
        }

        $pattern = '/<td class="second"><a href="http:\/\/([\S]+)\/" [^>]+>[a-zA-Z\.\n\t \d_]+<i[^>]+><\/i><\/a>[\s]*<\/td>[\s]*<td class="third">([\d\/]+)<\/td>\s*<td class="fourth">([\d\/]+)<\/td>/m';
        preg_match_all($pattern, $body, $matches);

        if (empty(array_first($matches))) {
            abort(404, '没有找到domain');
        }

        $domains = [];
        for ($i = 0; $i < count($matches[0]); $i++) {
            $domains[$i] = [
                'domain'        => $matches[1][$i],
                'register_time' => $this->formatDate($matches[2][$i]),
                'expires_time'  => $this->formatDate($matches[3][$i])
            ];
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

        return Carbon::create($date[2], $date[0], $date[1])->format($format);
    }
}
