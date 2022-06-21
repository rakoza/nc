<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateApp extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nc:create-app {id}';

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

        $r = $this->call('nc:create-linux-user', ['id' => $id]);
        if($r !== 0) {
            return $r;
        }

        $r = $this->call('nc:copy-app-folder', ['id' => $id]);
        if($r !== 0) {
            return $r;
        }

        $r = $this->call('nc:create-env-file', ['id' => $id]);
        if($r !== 0) {
            return $r;
        }

        $r = $this->call('nc:create-mysql-db-and-user', ['id' => $id]);
        if($r !== 0) {
            return $r;
        }

        $r = $this->call('nc:create-docker-container', ['id' => $id]);
        if($r !== 0) {
            return $r;
        }

        return 0;
    }
}
