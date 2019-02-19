<?php

namespace App\Services;

use Log;
use Cache;
use DOMXPath;
use DOMDocument;
use Carbon\Carbon;
use GuzzleHttp\Client;
use App\Traits\ProvidesConvenienceMethods;
use Illuminate\Database\Eloquent\Collection;

class FreenomService
{
    use ProvidesConvenienceMethods;

    private $freenomAuth = null;
    private $client = null;
    private $params = [];
    private $config = [];
    private $gateway = [
        'login'  => 'https://my.freenom.com/dologin.php',
        'list'   => 'https://my.freenom.com/clientarea.php?action=domains',
        'domain' => 'https://my.freenom.com/domains.php'
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
            $this->gateway['list'],
            $parse
        );

        $body = $response->getBody();

        if (preg_match('/Next Page/', $body)) {
            $token = $this->getToken($body);
            $response = $this->client->request(
                'POST',
                $this->gateway['list'],
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

    public function renew($domains)
    {
        if (!($domains instanceof Collection)) {
            abort('502', '数据错误');
        } else {
            if ($domains->isEmpty()) {
                abort('501', 'domains 数据为空');
            }
        }

        $this->login();

        $parse = [
            'headers' => array_merge(
                $this->baseHeaders,
                [
                    'Cookie'  => 'WHMCSZH5eHTGhfvzP=' . $this->getFreenomAuth(),
                ]
            ),
            'allow_redirects' => true,
        ];

        foreach ($domains as $domain) {
            $response = $this->client->request(
                'get',
                $this->gateway['domain'] . '?a=renewdomain&domain=' . $domain->domain_id,
                $parse
            );

            $token = $this->getToken($response->getBody());

            $response = $this->client->request(
                'post',
                $this->gateway['domain'] . '?submitrenewals=true',
                array_merge($parse, [
                    'form_params' => [
                        'paymentmethod' => 'credit',
                        'renewalid'     => $domain->domain_id,
                        'renewalperiod' => [
                            $domain->domain_id => $$domain->renew . 'M',
                        ],
                        'token' => $token
                    ]
                ])
            );

            $domain->expires_date = Carbon::parse($domain->expires_date)->addMonths($domain->renew);
            $domain->save();
        }
    }

    public function sync()
    {
        // TODO:log
        $data = $this->list();
        $this->user()->domains()->delete();
        $result = $this->user()->domains()->createMany($data);
        activity('freenom_sync')->causedBy($this->user())->log(':causer.name 同步freenom域名');
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
        $domainIds = $xpath->query('//*/table[contains(@class, "table-striped")]/tbody/tr//a[contains(@class, "pullRight")]');

        foreach ($dom as $key => $item) {
            preg_match('/id=(\d+)/', $domainIds[$key]->attributes->getNamedItem('href')->nodeValue, $matchDomainId);

            $domains[$key] = [
                'domain_id' => array_last($matchDomainId)
            ];

            foreach ($item->childNodes as $index => $childItem) {
                if ($childItem->nodeType == 1 && $index <= 9) {
                    // TODO: bug
                    $keyName = array_get($this->baseKey, strval(($index - 1) / 2));
                    $domains[$key][$keyName] = strtolower(trim($childItem->nodeValue));

                    if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $childItem->nodeValue)) {
                        $domains[$key][$keyName] = $this->formatDate($domains[$key][$keyName]);
                    }
                }
            }
        }

        Log::info([
            'code'    => 200,
            'message' => 'sync success',
            'data'    => $domains
        ]);

        return $domains;
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

    public function formatDate(String $date, String $format = 'Y-m-d')
    {
        $date = explode('/', $date);

        return Carbon::create($date[2], $date[1], $date[0])->format($format);
    }

    public function getToken($body)
    {
        preg_match('/<input[A-Za-z "=]+value=\"([\dA-Fa-f]+)\"[^>]+>/', $body, $matchesInput);

        return array_last($matchesInput);
    }
}
