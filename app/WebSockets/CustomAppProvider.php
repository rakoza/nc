<?php

namespace App\WebSockets;

use BeyondCode\LaravelWebSockets\Apps\App;
use BeyondCode\LaravelWebSockets\Apps\AppProvider;
use Illuminate\Support\Collection;

class CustomAppProvider implements AppProvider
{
    /** @var Collection */
    protected $apps;

    public function __construct()
    {
        // $this->apps = collect(config('websockets.apps'));
        $this->apps = \App\Models\Tenant::where('is_active', true)->get()
            ->map(function($tenant) {
                return [
                    'id' => $tenant->id,
                    'name' => $tenant->domain,
                    'key' => $tenant->domain,
                    'secret' => md5($tenant->domain),
                    'capacity' => null,
                    'enable_client_messages' => false,
                    'enable_statistics' => true,
                ];
            });
    }

    /**  @return array[\BeyondCode\LaravelWebSockets\AppProviders\App] */
    public function all(): array
    {
        return $this->apps
            ->map(function (array $appAttributes) {
                return $this->instanciate($appAttributes);
            })
            ->toArray();
    }

    public function findById($appId): ?App
    {
        $appAttributes = $this
            ->apps
            ->firstWhere('id', $appId);

        return $this->instanciate($appAttributes);
    }

    public function findByKey(string $appKey): ?App
    {
        $appAttributes = $this
            ->apps
            ->firstWhere('key', $appKey);

        return $this->instanciate($appAttributes);
    }

    public function findBySecret(string $appSecret): ?App
    {
        $appAttributes = $this
            ->apps
            ->firstWhere('secret', $appSecret);

        return $this->instanciate($appAttributes);
    }

    protected function instanciate(?array $appAttributes): ?App
    {
        if (! $appAttributes) {
            return null;
        }

        $app = new App(
            $appAttributes['id'],
            $appAttributes['key'],
            $appAttributes['secret']
        );

        if (isset($appAttributes['name'])) {
            $app->setName($appAttributes['name']);
        }

        if (isset($appAttributes['host'])) {
            $app->setHost($appAttributes['host']);
        }

        if (isset($appAttributes['path'])) {
            $app->setPath($appAttributes['path']);
        }

        $app
            ->enableClientMessages($appAttributes['enable_client_messages'])
            ->enableStatistics($appAttributes['enable_statistics'])
            ->setCapacity($appAttributes['capacity'] ?? null);

        return $app;
    }
}
