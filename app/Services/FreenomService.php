<?php

namespace App\Services;

use App\Exceptions\Freenom\InvalidConfigException;
use DOMXPath;
use DOMDocument;
use Carbon\Carbon;
use App\Models\User;
use GuzzleHttp\Client;
use Illuminate\Support\Arr;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\Crypt;

class FreenomService
{
    private $baseUrl = 'https://my.freenom.com/';

    private $isLogin = false;

    private $freenomAuth = null;

    private $client = null;

    private $params = [];

    private $originalDomain;

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
        if (
            !Arr::get($this->config, 'username')
            || !Arr::get($this->config, 'password')
        ) {
            throw new InvalidConfigException('Failed to get config. username or password not found.', 500);
        }

        $this->config['password'] = Crypt::decryptString($this->config['password']);

        if (empty($this->client)) {
            $this->client = $this->getClient();
        }

        if (empty($this->getFreenomAuth()) && empty(app('cache')->get('freenom_auth'))) {
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
                'form_params' => Arr::only($this->config, ['username', 'password'])
            ]
        );

        if (!$this->isLoginSuccessful($response)) {
            info('登录失败', [
                $this->config
            ]);

            abort(403, '登录失败');
        }

        $responseCookie = Arr::get($response->getHeaders(), 'Set-Cookie', []);

        foreach ($responseCookie as $key => &$value) {
            if (!preg_match('/^WHMCSZH5eHTGhfvzP=[\S\s]+$/', $value)) {
                unset($responseCookie[$key]);
            }
        }

        $auth = Arr::last(explode('=', Arr::first(explode(';', Arr::last($responseCookie)))));
        app('cache')->put('freenom_auth', $auth, 600);
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

        return $this->getDomainData((string) $body);
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

        $domains = $this->filterRenewDomain($domains);

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
                            $domain->domain_id => $domain->renew . 'M',
                        ],
                        'token' => $token
                    ]
                ])
            );

            $domain->expires_date = Carbon::parse($domain->expires_date)->addMonths($domain->renew);
            $domain->save();
        }
    }

    public function sync(User $user)
    {
        $data = $this->list();
        $data = $this->processSyncDomain($data, $user);

        if (!empty($data['update'])) {
            $old_domains = $this->getOriginalDomains()->keyBy('domain_id');

            foreach ($data['update'] as $domain) {
                $old_domains[$domain['domain_id']]->update($domain);
            }
        }

        if (!empty($data['delete'])) {
            $user->domains()->whereIn('id', $data['delete'])->delete();
        }

        if (!empty($data['create'])) {
            $user->domains()->createMany($data['create']);
        }

        activity('freenom_sync')->causedBy($user)->log(':causer.name 同步freenom域名');

        app('cache')->forget('user_index_' . $user->id);
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
                'domain_id' => Arr::last($matchDomainId)
            ];

            foreach ($item->childNodes as $index => $childItem) {
                if ($childItem->nodeType == 1 && $index <= 9) {
                    $keyName = Arr::get($this->baseKey, strval(floor($index / 2)));

                    if (!empty($keyName)) {
                        $domains[$key][$keyName] = strtolower(trim($childItem->nodeValue));

                        if (preg_match('/\d{2}\/\d{2}\/\d{4}/', $childItem->nodeValue)) {
                            $domains[$key][$keyName] = $this->formatDate($domains[$key][$keyName]);
                        }
                    }
                }
            }
        }

        info([
            'code'    => 200,
            'message' => 'sync success',
            'data'    => $domains
        ]);

        return $domains;
    }

    public function getFreenomAuth()
    {
        return $this->freenomAuth = app('cache')->get('freenom_auth');
    }

    public function getClient()
    {
        return $this->client = new Client([
            'timeout' => $this->config['timeout']
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

        return Arr::last($matchesInput);
    }

    protected function isLoginSuccessful($response)
    {
        $doc = new DOMDocument();
        $page = mb_convert_encoding($response->getBody(), 'HTML-ENTITIES', 'UTF-8');
        @$doc->loadHTML($page);
        $xpath = new DOMXPath($doc);
        $dom = $xpath->query('/html/body/div[1]/section[1]/div/div/ul/li[7]/a[2]');

        if (count($dom) > 0) {
            return false;
        }

        return true;
    }

    /**
     * 处理返回的域名数据，本地不存在则添加、存在则更新、本地存在，返回的数据不存在则删除
     *
     * @param [type] $data
     * @param [type] $user
     * @return void
     */
    public function processSyncDomain($data, $user)
    {
        $domain_list = [
            'create' => [],
            'update' => [],
            'delete' => []
        ];

        $originalDomains = $this->setOriginalDomains($user);

        if (!empty($data)) {
            $domain_list['create'] = $data;

            if ($originalDomains->isNotEmpty()) {
                $originalDomainsKey = $originalDomains->keyBy('domain_id');
                $collect_data = collect($data)->keyBy('domain_id');
                $intersect_data = $collect_data->intersectByKeys($originalDomainsKey);

                $domain_list = [
                    'create' => array_values($collect_data->diffKeys($intersect_data)->all()),
                    'update' => array_values($intersect_data->all()),
                    'delete' => $originalDomainsKey->diffKeys($intersect_data)->pluck('id')->all()
                ];
            }
        }

        return $domain_list;
    }

    public function filterRenewDomain($domains)
    {
        return $domains->filter(function ($domain, $index) {
            if (Carbon::parse($domain->expires_date)->lt(now()->addDay(14))) {
                return $domain;
            }
        });
    }

    public function setOriginalDomains($user)
    {
        return $this->originalDomain = $user->domains()->get();
    }

    public function getOriginalDomains()
    {
        return $this->originalDomain;
    }

    private function req(...$params)
    {
        $response = $this->client->request(
            'POST',
            $this->gateway['login'],
            [
                'allow_redirects' => false,
                'headers'         => array_merge($this->baseHeaders, [
                    'Referer' => 'https://my.freenom.com/clientarea.php?incorrect=true'
                ]),
                'form_params' => Arr::only($this->config, ['username', 'password'])
            ]
        );

        return $response;
    }
}
