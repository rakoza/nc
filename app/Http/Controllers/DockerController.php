<?php

namespace App\Http\Controllers;

use App\Docker\Client as DockerClient;
use App\Models\Tenant;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Artisan;

class DockerController extends Controller
{

    /**
     * Init docker client
     */
    public function __construct()
    {
        $this->client = new DockerClient();
    }

    /**
     * Get docker service status
     *
     * @return \Illuminate\Http\Response
     */
    public function getStatus()
    {
        try {
            return $this->client->getStatus();
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get Nginx container status
     *
     * @return \Illuminate\Http\Response
     */
    public function getNginxContainer()
    {
        try {
            $containers = $this->client->getAllContainers();

            return collect(json_decode($containers))
                ->where('Image', 'nginxproxy/nginx-proxy:alpine')
                ->map(function($item) {
                    return [
                        'id' => $item->Id,
                        'state' => $item->State,
                        'status' => $item->Status,
                    ];
                })
                ->firstOrFail();

        } catch (\Illuminate\Support\ItemNotFoundException $e) {
            return [
                'state' => 'unknown',
                'status' => 'Nginx container does not exist.',
            ];
        } catch (\Exception $e) {
            return [
                'state' => 'unknown',
                'status' => $e->getMessage(),
            ];
        }
    }

    /**
     * Get all docker containers
     *
     * @return \Illuminate\Http\Response
     */
    public function getContainers()
    {
        $containers = $this->client->getAllContainers();

        // return collect($containers)->map(fn($item) => $item->Names[0]);
        return collect(json_decode($containers))
            ->where('Image', '!=', 'nginxproxy/nginx-proxy:alpine')
            ->map(function($item) {
                return [
                    'id' => $item->Id,
                    'name' => trim($item->Names[0], '/'),
                    'image' => $item->Image,
                    'state' => $item->State,
                    'status' => $item->Status,
                ];
            })
            ->values();
    }

    /**
     * Get container basic info
     *
     * @param  Tenant $tenant
     * @return \Illuminate\Http\Response
     */
    public function getContainer(Tenant $tenant)
    {
        $containers = $this->getContainers();

        $containerName = sprintf("client%d_app_1", $tenant->id);

        $container = collect($containers)->firstWhere('name', $containerName);

        return $container;
    }

    /**
     * Create container
     *
     * @param  Tenant $tenant
     * @return \Illuminate\Http\Response
     */
    public function createContainer(Tenant $tenant)
    {
        Artisan::call('nc:create-container', [
            'id' => $tenant->id
        ]);
    }

    /**
     * Start container
     *
     * @param  int $containerId
     * @return \Illuminate\Http\Response
     */
    public function startContainer(string $containerId)
    {
        return $this->client->startContainer($containerId);
    }

    /**
     * Stop container
     *
     * @param  int $containerId
     * @return \Illuminate\Http\Response
     */
    public function stopContainer(string $containerId)
    {
        return $this->client->stopContainer($containerId);
    }

    /**
     * Remove container
     *
     * @param  int $containerId
     * @return \Illuminate\Http\Response
     */
    public function removeContainer(string $containerId)
    {
        return $this->client->removeContainer($containerId);
    }

}
