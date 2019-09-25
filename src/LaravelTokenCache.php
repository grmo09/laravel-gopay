<?php

namespace grmo09\LaravelGoPay;

use Cache;
use GoPay\Token\TokenCache;
use GoPay\Token\AccessToken;

/**
 * Class LaravelTokenCache
 * @package grmo09\LaravelGoPay
 */
class LaravelTokenCache implements TokenCache
{
    /**
     * @param $client
     * @param AccessToken $token
     */
    public function setAccessToken($client, AccessToken $token)
    {
        Cache::put('gopay_token_' . $client, serialize($token), config('gopay.timeout'));
    }

    /**
     * @param $client
     * @return mixed|null
     */
    public function getAccessToken($client)
    {
        $token = Cache::get('gopay_token_' . $client);

        if (!is_null($token)) {
            return unserialize($token);
        }

        return null;
    }
}