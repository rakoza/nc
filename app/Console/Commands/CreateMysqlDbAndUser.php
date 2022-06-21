<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CreateMysqlDbAndUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nc:create-mysql-db-and-user {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Mysql database and user if not exists and grant privilegies';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $id = $this->argument('id');

        $tenant = Tenant::find($id);

        $this->line('Create Mysql database and user if not exists and grant privilegies');

        $username = $tenant->db_username;
        $password = $tenant->db_password;
        $database = 'tenant_' . $tenant->id;

        try {
            \DB::statement("CREATE DATABASE IF NOT EXISTS $database");
            \DB::statement("CREATE USER IF NOT EXISTS '$username' IDENTIFIED BY '$password'");
            \DB::statement("GRANT ALL ON $database.* TO $username");

            $this->info('done');

            return 0;
        } catch (\Exception $e) {
            $this->error($e->getMessage());

            if($this->inWeb()) {
                // abort further execution
                throw new \Exception("Error creating MySql database and user of the tenant. " . $e->getMessage(), 1);
            }
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
