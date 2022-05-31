<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;

class CreateContainer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nc:create-container {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create docker container for the chosen tenant';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');

        $tenant = Tenant::find($id);

        if(! $tenant) {
            $this->error('No tenant with provided id');
            return 1;
        }

        $this->line(sprintf('Start creating docker container for tenant `%s`', $tenant->name));

        $this->createLinuxUser($tenant->id);

        $uid = $this->getLinuxUserUID($tenant->id);
        $gid = $this->getLinuxUserGID($tenant->id);

        $tenant->user_uid = $uid;
        $tenant->user_gid = $gid;
        $tenant->save();

        $this->createStorageFolder($tenant->id);
        $this->copyDockerComposeFile($tenant->id);
        $this->createEnvFile($tenant);
        $this->createMysqlDatabaseUserAndGrantPrivilegies($tenant);
        $this->createDockerContainer($tenant);

        return 0;
    }

    /**
     * Create linux user
     *
     * @param  [type] $id
     * @return [type]
     */
    protected function createLinuxUser($id)
    {
        $newUser = 'client' . $id;

        $this->line(sprintf('1. create linux user `%s`', $newUser));
        exec('sudo useradd --no-create-home ' . $newUser, $output, $result);

        if($result > 0) {
            $this->warn('error: ' . $result);

            throw new \Exception("error", "Error creating new user `$newUser`");

        } else {
            $this->info('done');
        }
    }

    /**
     * Get the user UID
     *
     * @param  [type] $id
     * @return [type]
     */
    protected function getLinuxUserUID($id)
    {
        exec('id -u client' . $id, $output, $result);

        if($result > 0) {
            $this->error('error: ' . $result);

            throw new \Exception("Error getting linux user UID", 1);
        }

        return $output[0];
    }

    /**
     * Get the user GID
     *
     * @param  [type] $id
     * @return [type]
     */
    protected function getLinuxUserGID($id)
    {
        exec('id -g client' . $id, $output, $result);

        if($result > 0) {
            $this->error('error: ' . $result);

            throw new \Exception("Error getting linux user GID", 1);
        }

        return $output[0];
    }

    /**
     * Create folder for the tenant
     *
     * @param  [type] $id
     * @return [type]
     */
    protected function createStorageFolder($id)
    {
        $path = config('tenants.path');
        $user = 'client' . $id;
        $storagePath = sprintf('%s/%s/storage', $path, $user);

        $this->line(sprintf("2. create folder `$storagePath`"));

        exec("mkdir -p $storagePath", $output, $result);
        exec("sudo chown $user:$user $storagePath", $output, $result);

        if($result > 0) {
            $this->warn('error: ' . $result);
        } else {
            $this->info('done');
        }
    }

    /**
     * Create docker-composer.yml file in target folder
     *
     * @param  [type] $id
     * @return [type]
     */
    protected function copyDockerComposeFile($id)
    {
        $path = config('tenants.path');
        $user = 'client' . $id;
        $tenantPath = sprintf('%s/%s', $path, $user);
        $templatePath = sprintf('%s/container/docker-compose.yml', $path);

        $this->line('3. create docker-compose.yml');

        exec("cp $templatePath $tenantPath", $output, $result);

        if($result > 0) {
            $this->warn('error: ' . $result);
        } else {
            $this->info('done');
        }
    }

    /**
     * Create docker-composer.yml file in target folder
     *
     * @param  Tenant $tenant
     * @return [type]
     */
    protected function createEnvFile(Tenant $tenant)
    {
        $path = config('tenants.path');
        $user = 'client' . $tenant->id;
        $tenantPath = sprintf('%s/%s', $path, $user);
        $envPath = sprintf('%s/container/.env.example', $path);

        $this->line('4. create .env');

        $search = [
            '{UID}',
            '{GID}',
            '{TZ}',
            '{SRC}',
            '{DOMAIN}',
            '{DB_HOST}',
            '{DB_DATABASE}',
            '{DB_USERNAME}',
            '{DB_PASSWORD}',
            '{REDIS_HOST}',
        ];

        $replace = [
            $tenant->user_uid,
            $tenant->user_gid,
            $tenant->timezone,
            $tenant->src,
            $tenant->domain,
            $tenant->db_host,
            'tenant_' . $tenant->id,
            $tenant->db_username,
            $tenant->db_password,
            $tenant->redis_host,
        ];

        $originEnv = file_get_contents($envPath);
        $targetEnv = str_replace($search, $replace, $originEnv);

        $result = file_put_contents($tenantPath . '/.env', $targetEnv);

        if($result === false) {
            $this->error('error occurred');
        } else {
            $this->info('done');
        }
    }

    /**
     * Create mysql user
     *
     * @param  Tenant $tenant [description]
     * @return [type]         [description]
     */
    protected function createMysqlDatabaseUserAndGrantPrivilegies(Tenant $tenant)
    {
        $this->line('5. setup mysql');

        $username = $tenant->db_username;
        $password = $tenant->db_password;
        $database = 'tenant_' . $tenant->id;

        try {
            \DB::statement("CREATE DATABASE IF NOT EXISTS $database");
            \DB::statement("CREATE USER IF NOT EXISTS '$username' IDENTIFIED BY '$password'");
            \DB::statement("GRANT ALL ON $database.* TO $username");

            $this->info('done');

        } catch (\Exception $e) {
            $this->error($e->getMessage());
        }
    }

    /**
     * Create docker container
     *
     * @param  Tenant $tenant [description]
     * @return [type]         [description]
     */
    protected function createDockerContainer(Tenant $tenant)
    {
        $this->line('6. create docker container');

        $path = config('tenants.path');
        $user = 'client' . $tenant->id;
        $tenantPath = sprintf('%s/%s', $path, $user);

        exec("cd $tenantPath && docker-compose up --no-start", $output, $result);

        if($result === false) {
            $this->error('error occurred');
        } else {
            $this->info('done');
        }
    }
}
