<?php

namespace App\Console\Commands\Menu;

use App\Models\UserManagement\Menu;
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
    protected $signature = 'menu:new {--name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add new menu';

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
            $isExist = Menu::where('name', 'ilike', $name)->first();
            if (!$isExist) {
                $data = new Menu();
                $data->name = $name;
                $data->order_no = 1;
                $data->save();
    
                $this->info('New menu has created.');
            }
            else {
                $this->error("Menu '$name' is already exist!");
            }

        }
        else {
            $this->error('Option --menu is required!');
        }
    }
}
