<?php

namespace Ellllllen\TVDBApi;

use Ellllllen\ApiWrapper\Connect;
use Illuminate\Contracts\Cache\Repository;


class Authenticate
{
    private $connect;
    private $cache;
    private $token;

    const CACHE_TOKEN_NAME = "tv_db_api_token";

    public function __construct(Connect $connect, Repository $cache)
    {
        $this->connect = $connect;
        $this->cache = $cache;
    }

    public function getToken()
    {
        if ($this->cache->has(static::CACHE_TOKEN_NAME)) {
            $this->token = $this->cache->get(static::CACHE_TOKEN_NAME);
        }

        if (!$this->token) {
            $this->token = $this->createNewToken();
            $this->cache->forever(static::CACHE_TOKEN_NAME, $this->token);
        }

        return $this->token;
    }

    private function createNewToken()
    {
        $parameters = [
            "json" => [
                "apikey" => env("TVDB_API_APIKEY"),
                "userkey" => env("TVDB_API_USERKEY"),
                "username" => env("TVDB_API_USERNAME"),
            ]
        ];

        $response = $this->connect->doRequest("POST", $parameters, "/login", true);

        return json_decode($response)->token;
    }
}