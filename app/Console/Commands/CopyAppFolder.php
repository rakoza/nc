<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CopyAppFolder extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nc:copy-app-folder {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Copy app folder';

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

        $sourceFolder = $path . DIRECTORY_SEPARATOR . 'apps' . DIRECTORY_SEPARATOR . $tenant->src;
        $destinationFolder = $path . DIRECTORY_SEPARATOR . $user;

        $foldersToChangeOwner = implode(' ', [
            $destinationFolder . DIRECTORY_SEPARATOR . 'storage/app',
            $destinationFolder . DIRECTORY_SEPARATOR . 'storage/framework',
            $destinationFolder . DIRECTORY_SEPARATOR . 'storage/logs',
            $destinationFolder . DIRECTORY_SEPARATOR . 'bootstrap/cache',
        ]);

        $this->line("Copy app to destination folder `$destinationFolder`");

        // check if destination folder already exists
        if(is_dir($destinationFolder)) {
            $this->warn("Directory `$destinationFolder` already exists");
            return 0;
        }

        exec("cp -R $sourceFolder $destinationFolder");
        exec("sudo chown -R $user:$user $foldersToChangeOwner", $output, $result);

        if($result > 0) {
            $this->warn('error: ' . $result);

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error creating storage folder `$destinationFolder`", 1);
            }
        } else {
            $this->info('done');
        }

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
}
