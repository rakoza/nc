<?php

namespace App\Http\Controllers;

use App\Docker\Client as DockerClient;
use App\Models\Tenant;
use Illuminate\Http\Request;

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
     * Get all docker containers
     *
     * @return \Illuminate\Http\Response
     */
    public function getContainers()
    {
        $containers = $this->client->getAllContainers();

        // return collect($containers)->map(fn($item) => $item->Names[0]);
        return collect(json_decode($containers))->map(function($item) {
            return [
                'id' => $item->Id,
                'name' => trim($item->Names[0], '/'),
                'image' => $item->Image,
                'state' => $item->State,
                'status' => $item->Status,
            ];
        });
    }

    /**
     * Get container basic info
     *
     * @param  Tenant       $tenant [description]
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

}
