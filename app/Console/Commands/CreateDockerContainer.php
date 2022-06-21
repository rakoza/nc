<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CreateDockerContainer extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nc:create-docker-container {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create docker container';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->line('Create docker container');

        $id = $this->argument('id');

        $tenant = Tenant::find($id);

        $path = config('tenants.path');
        $user = 'client' . $tenant->id;

        $destinationFolder = $path . DIRECTORY_SEPARATOR . $user;

        exec("cd $destinationFolder && docker-compose up --no-start", $output, $result);

        if($result === false) {
            $this->error('error occurred');

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error creating docker container, final step.", 1);
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
