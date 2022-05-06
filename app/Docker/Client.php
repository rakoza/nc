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

    /**
     * Start docker container
     *
     * @param  string $id
     * @return int
     * 204 no error
     * 304 container already started
     * 404 no such container
     * 500 server error
     */
    public function startContainer(string $id)
    {
        $response = $this->post("/containers/{$id}/start");

        return $response->getStatusCode();
    }

    /**
     * Stop docker container
     *
     * @param  string $id
     * @return int
     * 204 no error
     * 304 container already stopped
     * 404 no such container
     * 500 server error
     */
    public function stopContainer(string $id)
    {
        $response = $this->post("/containers/{$id}/stop");

        return $response->getStatusCode();
    }

    /**
     * Remove docker container
     *
     * @param  string $id
     * @return int
     * 204 no error
     * 400 bad parameter
     * 404 no such container
     * 409 conflict
     * 500 server error
     */
    public function removeContainer(string $id)
    {
        $response = $this->delete("/containers/{$id}");

        return $response->getStatusCode();
    }

}
