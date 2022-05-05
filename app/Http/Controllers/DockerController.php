<?php

namespace App\Http\Controllers;

use App\Docker\Client as DockerClient;
use App\Models\Tenant;
use Illuminate\Http\Request;

class DockerController extends Controller
{
    /**
     * Get all docker containers
     *
     * @return \Illuminate\Http\Response
     */
    public function getContainers(DockerClient $client)
    {
        $containers = $client->getAllContainers();

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
     * @param  DockerClient $client [description]
     * @return [type]               [description]
     */
    public function getContainer(Tenant $tenant, DockerClient $client)
    {
        $containers = $this->getContainers($client);
        $containerName = sprintf("client%d_app_1", $tenant->id);

        $container = collect($containers)->firstWhere('name', $containerName);

        return $container;
    }

}
