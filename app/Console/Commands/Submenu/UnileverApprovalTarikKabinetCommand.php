<?php

namespace App\Console\Commands\Submenu;

use App\Models\UserManagement\Menu;
use App\Models\UserManagement\Permission;
use App\Models\UserManagement\SubMenu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class UnileverApprovalTarikKabinetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submenu:unilever-approval-tarik-kabinet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Submenu Unilever - Approval Tarik Kabinet';

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
                'name'    => 'unilever.pull-cabinet.index',
                'caption' => 'List Approval Tarik Kabinet'
            ],
            [
                'name'    => 'unilever.pull-cabinet.show',
                'caption' => 'Detail Approval Tarik Kabinet'
            ],
            [
                'name'    => 'unilever.pull-cabinet.approve',
                'caption' => 'Approve Tarik Kabinet'
            ],
            [
                'name'    => 'unilever.pull-cabinet.reject',
                'caption' => 'Reject Tarik Kabinet'
            ],
            [
                'name'    => 'unilever.pull-cabinet.change-delivery-date',
                'caption' => 'Change Delivery Date Tarik Kabinet'
            ],
            [
                'name'    => 'unilever.pull-cabinet.export-outlet',
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
        
        $permission = Permission::where('name', 'unilever.pull-cabinet.index')->first();
        $menuParent = Menu::where('name', 'Unilever')->first();

        $existSubMenu = SubMenu::where('name', 'Approval Tarik Kabinet')->first();
        if (!$existSubMenu) {
            $newSubMenu = new SubMenu;
            $newSubMenu->name = 'Approval Tarik Kabinet';
            $newSubMenu->menu_id = $menuParent->id;
            $newSubMenu->permission_id = $permission->id;
            $newSubMenu->order_no = 1;
            $newSubMenu->save();
        }
        $this->info('Submenu has created.');
    }
}
