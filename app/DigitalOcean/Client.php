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
     * Retrieve an CNAME dns record
     *
     * @return string
     */
    public function getCnameRecord($domain, $subDomain)
    {
        $response = $this
            ->client
            ->get("domains/$domain/records", [
                'query' => [
                    'name' => $subDomain,
                    'type' => 'CNAME',
                ]
            ]);

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
     * Add CNAME record, subdomain
     *
     * @return [type] [description]
     */
    public function addCnameRecord($domain, $subDomain)
    {
        $response = $this
            ->client
            ->post("domains/$domain/records", [
                'json' => [
                    'type' => 'CNAME',
                    'name' => $subDomain,
                    'data' => $domain . '.',
                ],
            ]);

        return $response->getStatusCode();
    }

    /**
     * Add A record, for domain or subdomain
     *
     * @return [type] [description]
     */
    public function addARecord($domain, $subDomain)
    {
        $response = $this
            ->client
            ->post("domains/$domain/records", [
                'json' => [
                    'type' => 'A',
                    'name' => $subDomain,
                    'data' => config('digitalocean.public_ip_address'),
                ],
            ]);

        return $response->getStatusCode();
    }

}
