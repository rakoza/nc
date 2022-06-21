<?php

namespace App\Console\Commands;

use App\Models\Tenant;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\App;

class CreateLinuxUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'nc:create-linux-user {id}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create Linux user';

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

        if ($this->ifLinuxUserNotExists()) {
            $this->createLinuxUser($tenant->id);
        } else {
            $this->line(sprintf('Linux user `%s` already exists', $this->linuxUser));
        }

        $uid = $this->getLinuxUserUID();
        $gid = $this->getLinuxUserGID();

        $tenant->user_uid = $uid;
        $tenant->user_gid = $gid;
        $tenant->save();

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
     * Get true if the command is running from the web interface
     *
     * @return boolean
     */
    protected function inWeb() {
        return !App::runningInConsole();
    }

}
