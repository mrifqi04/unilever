<?php

namespace App\Console\Commands\Submenu;

use App\Models\UserManagement\Menu;
use App\Models\UserManagement\Permission;
use App\Models\UserManagement\SubMenu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class CallCenterTarikKabinetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submenu:call-center-tarik-kabinet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Submenu Call Center - Tarik Kabinet';

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
        // create permission
        $permissions = [
            [
                'name'    => 'callcenter.pull-cabinet.index',
                'caption' => 'List Tarik Kabinet'
            ],
            [
                'name'    => 'callcenter.pull-cabinet.show',
                'caption' => 'Detail Tarik Kabinet'
            ],
            [
                'name'    => 'callcenter.pull-cabinet.update',
                'caption' => 'Edit Tarik Kabinet'
            ],
            [
                'name'    => 'callcenter.pull-cabinet.approve',
                'caption' => 'Approve Tarik Kabinet'
            ],
            [
                'name'    => 'callcenter.pull-cabinet.cancel',
                'caption' => 'Cancel Tarik Kabinet'
            ],
            [
                'name'    => 'callcenter.pull-cabinet.postpone',
                'caption' => 'Tunda Tarik Kabinet'
            ],
            [
                'name'    => 'callcenter.pull-cabinet.export-outlet',
                'caption' => 'Export Tarik Kabinet'
            ],
        ];

        foreach ($permissions as $key => $value) {
            $existPermission = Permission::where('name', $value['name'])->first();

            if (! $existPermission) {
                $newPermission = new Permission;
                $newPermission->id      = Uuid::uuid4()->toString();
                $newPermission->name    = $value['name'];
                $newPermission->caption = $value['caption'];
                $newPermission->save();
            }
        }
        
        $permission = Permission::where('name', 'callcenter.pull-cabinet.index')->first();
        $menuParent = Menu::where('name', 'Call Center')->first();

        $existSubMenu = SubMenu::where('name', 'Tarik Kabinet')->first();
        if (!$existSubMenu) {
            $newSubMenu = new SubMenu;
            $newSubMenu->name = 'Tarik Kabinet';
            $newSubMenu->menu_id = $menuParent->id;
            $newSubMenu->permission_id = $permission->id;
            $newSubMenu->order_no = 1;
            $newSubMenu->save();
        }
        $this->info('Submenu has created.');
    }
}
