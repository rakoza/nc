<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

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
     * Name of the linux user
     *
     * @var string
     */
    private $linuxUser;

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

        $this->linuxUser = 'client' . $tenant->id;

        $this->line(sprintf('Start creating docker container for tenant `%s`', $tenant->name));

        if ($this->ifLinuxUserNotExists()) {
            $this->createLinuxUser($tenant->id);
        }

        $uid = $this->getLinuxUserUID();
        $gid = $this->getLinuxUserGID();

        $tenant->user_uid = $uid;
        $tenant->user_gid = $gid;
        $tenant->save();

        $this->createStorageFolder();
        $this->copyDockerComposeFile();
        $this->createEnvFile($tenant);
        $this->createMysqlDatabaseUserAndGrantPrivilegies($tenant);
        $this->createDockerContainer($tenant);

        return 0;
    }

    /**
     * Get true if the command is running from the web interface
     *
     * @return boolean
     */
    protected function inWeb() {
        return !App::runningInConsole();
    }

    /**
     * Create linux user
     *
     * @param  [type] $id
     * @return [type]
     */
    protected function createLinuxUser($id)
    {
        $newUser = $this->linuxUser;

        $this->line(sprintf('1. create linux user `%s`', $newUser));

        exec('sudo useradd --no-create-home ' . $newUser, $output, $result);

        if($result > 0) {
            $this->warn('error: ' . $result);

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error creating new user `$newUser`", 1);
            }

        } else {
            $this->info('done');
        }
    }

    /**
     * Get true if linux user with the name `$this->linuxUser` not exists
     *
     * @return [type] [description
     */
    protected function ifLinuxUserNotExists()
    {
        exec('id -u ' . $this->linuxUser, $output, $result);

        // ($result > 0) ako takav korisnik ne postoji
        return $result > 0;
    }

    /**
     * Get the user UID
     *
     * @return [type]
     */
    protected function getLinuxUserUID()
    {
        exec('id -u ' . $this->linuxUser, $output, $result);

        if($result > 0) {
            $this->error('error: ' . $result);

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error getting linux user UID", 1);
            }
        }

        return $output[0];
    }

    /**
     * Get the user GID
     *
     * @return [type]
     */
    protected function getLinuxUserGID()
    {
        exec('id -g ' . $this->linuxUser, $output, $result);

        if($result > 0) {
            $this->error('error: ' . $result);

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error getting linux user GID", 1);
            }
        }

        return $output[0];
    }

    /**
     * Create folder for the tenant
     *
     * @return [type]
     */
    protected function createStorageFolder()
    {
        $path = config('tenants.path');
        $user = $this->linuxUser;
        $storagePath = sprintf('%s/%s/storage', $path, $user);

        $this->line(sprintf("2. create folder `$storagePath`"));

        exec("mkdir -p $storagePath", $output, $result);
        exec("sudo chown $user:$user $storagePath", $output, $result);

        if($result > 0) {
            $this->warn('error: ' . $result);

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error creating storage folder `$storagePath`", 1);
            }
        } else {
            $this->info('done');
        }
    }

    /**
     * Create docker-composer.yml file in target folder
     *
     * @return [type]
     */
    protected function copyDockerComposeFile()
    {
        $path = config('tenants.path');
        $user = $this->linuxUser;
        $tenantPath = sprintf('%s/%s', $path, $user);
        $templatePath = sprintf('%s/container/docker-compose.yml', $path);

        $this->line('3. create docker-compose.yml');

        exec("cp $templatePath $tenantPath", $output, $result);

        if($result > 0) {
            $this->warn('error: ' . $result);

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error coping `$templatePath` file to the tenant folder", 1);
            }
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
        $user = $this->linuxUser;
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
            '{WORD_TO_PDF_SERVER}',
            '{WORD_TO_PDF_SERVER_PORT}',
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
            $tenant->word_to_pdf_worker ?? 'host.docker.internal',
            $tenant->word_to_pdf_worker_port ?? '5555',
        ];

        $originEnv = file_get_contents($envPath);
        $targetEnv = str_replace($search, $replace, $originEnv);

        $result = file_put_contents($tenantPath . '/.env', $targetEnv);

        if($result === false) {
            $this->error('error occurred');

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error creating .env file of the tenant", 1);
            }
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

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error creating MySql database and user of the tenant. " . $e->getMessage(), 1);
            }
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
        $user = $this->linuxUser;
        $tenantPath = sprintf('%s/%s', $path, $user);

        exec("cd $tenantPath && docker-compose up --no-start", $output, $result);

        if($result === false) {
            $this->error('error occurred');

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error creating docker container, final step.", 1);
            }
        } else {
            $this->info('done');
        }
    }
}
