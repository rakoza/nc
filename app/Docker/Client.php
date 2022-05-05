<?php

namespace App\Docker;

use GuzzleHttp\Client as GuzzleClient;
use Illuminate\Http\Request;

class Client extends GuzzleClient
{

    /**
     * [__construct description]
     * @param array $config [description]
     */
    public function __construct()
    {
        $options = [
            'base_uri' => 'http://localhost/v1.41/',
            'curl' => [
                CURLOPT_UNIX_SOCKET_PATH => '/var/run/docker.sock'
            ]
        ];

        parent::__construct($options);
    }

    /**
     * Get docker containers
     *
     * @return [type] [description]
     */
    public function getAllContainers()
    {
        $response = $this->get('/containers/json?all=true');

        return $response->getBody();
    }

}
