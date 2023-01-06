<?php

namespace App\Console\Commands\Submenu;

use Illuminate\Console\Command;
use App\Models\UserManagement\SubMenu;

class ChangeByNameCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submenu:change-by-name {--old-name=} {--new-name=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Change Submenu by Name';

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
        $oldName = $this->option('old-name');
        $newName = $this->option('new-name');

        if ($oldName && $newName) {
            $submenu = SubMenu::where('name', $oldName)->first();
            if ($submenu) {
                $submenu->name = $newName;
                $submenu->save();
                $this->info('Submenu has changed.');
            }
            else {
                $this->error('"'.$oldName.'" is not found!');
            }
        }
        else {
            $this->error('Options --old-name & --new-name is required!');
        }
    }
}
