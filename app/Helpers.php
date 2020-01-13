<?php

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

if (!function_exists('fetch')) {
    /**
     * Fetch Remote Data
     *
     * @param string $url
     * @param boolean $isJson
     * @param array $data
     * @param array $headers
     * @return array
     */
    function fetch($url, $data = [], $rawHeaders = [], $method = 'GET', $isJson = true, $returnHeader = false, $timeout = 5)
    {
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
        curl_setopt($ch, CURLOPT_HEADER, $returnHeader ? 1 : 0);
        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

        if (!empty($rawHeaders)) {
            $headers = [];
            foreach ($rawHeaders as $k => $v) {
                $headers[] = $k . ': ' . $v;
            }
            curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        }
        if (!empty($data)) {
            curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);

            if (is_array($data)) {
                if (isset($rawHeaders['Content-Type']) && $rawHeaders['Content-Type'] == 'application/json') {
                    $postData = json_encode($data);
                } else {
                    $postData = http_build_query($data);
                }
            } else {
                $postData = $data;
            }

            curl_setopt($ch, CURLOPT_POSTFIELDS, $postData);
        }

        $result = curl_exec($ch);
        $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $time = curl_getinfo($ch, CURLINFO_TOTAL_TIME);
        curl_close($ch);

        if (!empty($result)) {
            return $isJson ? json_decode($result, true) : $result;
        }

        app('log')->info('[fetch][fail][' . $code . ']' . $url . (isset($postData) ? '?' . str_replace(PHP_EOL, '', $postData) : ''));

        return $isJson ? [] : '';
    }
}

if (!function_exists('auth_user')) {
    /**
     * return current logged-in user
     *
     * @return \App\Essential\Models\User|null
     */
    function auth_user()
    {
        return Auth::user();
    }
}

if (!function_exists('signTime')) {
    /**
     * return current logged-in user
     *
     * @return \App\Essential\Models\User|null
     */
    function signTime($cache_name = '', $expire = 3600)
    {
        $cache_instance = app('cache');

        if (!$cache_instance->has($cache_name)) {
            $random_number = mt_rand(30, 45);

            if (Str::startsWith($cache_name, 'lootboy_')) {
                $random_number = mt_rand(46, 59);
            }

            $cache_instance->put($cache_name, now()->secondsUntilEndOfDay(), $random_number);
        }

        return $cache_instance->get($cache_name);
    }
}
