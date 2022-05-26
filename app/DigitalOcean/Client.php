<?php

namespace App\DigitalOcean;

use GuzzleHttp\Client as GuzzleClient;

class Client
{

    /**
     * @var GuzzleClient
     */
    private $client;

    /**
     * Inicijalizujemo http client sa zadatim podesavanjima
     */
    public function __construct()
    {
        $token = config('digitalocean.token');

        $options = [
            'base_uri' => 'https://api.digitalocean.com/v2/',
            'headers' => [
                'Authorization' => 'Bearer ' . $token,
                'Accept'        => 'application/json',
            ]
        ];

        $this->client = new GuzzleClient($options);
    }

    /**
     * Retrieve an existing domain
     *
     * @return string
     */
    public function getDomain($domain)
    {
        $response = $this
            ->client
            ->get("domains/$domain");

        return $response->getBody();
    }

    /**
     * Add personal domain
     *
     * @return [type] [description]
     */
    public function addDomain($domain)
    {
        $response = $this
            ->client
            ->post("domains", [
                'json' => [
                    'name' => $domain,
                    'data' => config('digitalocean.public_ip_address'),
                ],
            ]);

        return $response->getStatusCode();
    }

    /**
     * Add subdomain of our app domain
     *
     * @return [type] [description]
     */
    public function addSubDomain($sub)
    {
        $domain = config('digitalocean.app_domain');

        $response = $this
            ->client
            ->post("domains/$domain/records", [
                'json' => [
                    'type' => 'CNAME',
                    'name' => $sub,
                    'data' => $domain . '.',
                ],
            ]);

        return $response->getStatusCode();
    }

}
