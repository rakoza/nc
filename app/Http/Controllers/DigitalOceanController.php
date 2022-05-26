<?php

namespace App\Http\Controllers;

use App\DigitalOcean\Client as Client;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DigitalOceanController extends Controller
{

    /**
     * Init DO api client
     */
    public function __construct()
    {
        $this->client = new Client();
    }

    /**
     * Get docker service status
     *
     * @return \Illuminate\Http\Response
     */
    public function getDomain(string $domain)
    {
        try {

            $body = $this->client->getDomain($domain);

            return json_decode($body);

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            return [
                'status' => 'ok',
                'error' => $e->getMessage()
            ];
        }
    }

}
