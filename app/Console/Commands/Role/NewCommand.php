<?php

namespace App\Console\Commands\Role;

use App\Models\UserManagement\Role;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class NewCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'role:new {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Role';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function handle()
    {
        $name = $this->option('name');

        if ($name) {
            $isExist = Role::where('name', 'ilike', $name)->first();
            if (!$isExist) {
                $data = new Role();
                $data->id = Uuid::uuid4()->toString();
                $data->name = $name;
                $data->save();
    
                $this->info('New role has created.');
            }
            else {
                $this->error("Role '$name' is already exist!");
            }
        }
        else {
            $this->error('Option --role is required!');
        }
    }
}
