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
     * Create domain (or subdomain)
     *
     * @return \Illuminate\Http\Response
     */
    public function createDomain(Tenant $tenant)
    {
        $domain = $tenant->domain;
        $appDomain = '.' . config('digitalocean.app_domain');

        $isSubdomain = str_contains($domain, $appDomain);
        $subDomain = str_replace($appDomain, '', $tenant->domain);

        if($isSubdomain) {
            return $this->client->addCnameRecord($appDomain, $subDomain);
        }

        $this->client->addDomain($domain); // ovo ce kreirati samo NS rekorde
        return $this->client->addARecord($domain, '@'); // ovo kreira A rekord
    }

    /**
     * Get docker service status
     *
     * @return \Illuminate\Http\Response
     */
    public function getDomainDetails(Tenant $tenant)
    {
        $domain = $tenant->domain;
        $appDomain = config('digitalocean.app_domain');

        $isSubdomain = str_contains($domain, $appDomain);

        try {

            if($isSubdomain) {
                $body = $this->client->getCnameRecord($appDomain, $domain);
            } else {
                $body = $this->client->getDomain($domain);
            }

            return [
                'status' => 'ok',
                'record' => json_decode($body),
            ];

        } catch (\GuzzleHttp\Exception\ClientException $e) {
            $response = $e->getResponse();

            $statusCode = $response->getStatusCode();
            $body = $response->getBody();
            $error = json_decode($body);

            return [
                'status' => 'error',
                'error_id' => $error->id,
                'error_message' => $error->message,
            ];
        }
    }

}
