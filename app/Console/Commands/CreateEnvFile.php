<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CreateEnvFile extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nc:create-env-file {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update .env file';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');

        $tenant = Tenant::find($id);

        $path = config('tenants.path');
        $user = 'client' . $tenant->id;

        $destinationFolder = $path . DIRECTORY_SEPARATOR . $user;
        $envSource = $destinationFolder . DIRECTORY_SEPARATOR . '.env.example';
        $envDest = $destinationFolder . DIRECTORY_SEPARATOR . '.env';

        $this->line('Copy .env.example to .env and update it with database data');

        if(is_file($envDest)) {
            $this->warn("File `$envDest` already exists");
            return 0;
        }

        $search = [
            '{USER_UID}',
            '{USER_GID}',
            '{TZ}',
            '{APP_SRC}',
            '{APP_DOMAIN}',
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

        $originEnv = file_get_contents($envSource);
        $targetEnv = str_replace($search, $replace, $originEnv);

        $result = file_put_contents($envDest, $targetEnv);

        if($result === false) {
            $this->error('error occurred');

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error creating .env file of the tenant", 1);
            }
        } else {
            $this->info('done');
            return 0;
        }
    }

    /**
     * Get true if the command is running from the web interface
     *
     * @return boolean
     */
    protected function inWeb() {
        return !App::runningInConsole();
    }
}
