<?php

namespace App\Console\Commands\Submenu;

use App\Models\UserManagement\Menu;
use App\Models\UserManagement\Permission;
use App\Models\UserManagement\SubMenu;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Ramsey\Uuid\Uuid;

class AuditorTarikKabinetCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'submenu:auditor-tarik-kabinet';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Add Submenu Auditor - Tarik Kabinet';

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
                'name'    => 'auditor.pull-cabinet.index',
                'caption' => 'Auditor Tarik Kabinet List Data'
            ],
            [
                'name'    => 'auditor.pull-cabinet.show',
                'caption' => 'Auditor Tarik Kabinet Detail Data'
            ],
            [
                'name'    => 'auditor.pull-cabinet.edit',
                'caption' => 'Auditor Tarik Kabinet Edit Form'
            ],
            [
                'name'    => 'auditor.pull-cabinet.update',
                'caption' => 'Auditor Tarik Kabinet Update'
            ],
            [
                'name'    => 'auditor.pull-cabinet.destroy',
                'caption' => 'Auditor Tarik Kabinet Delete'
            ],
            [
                'name'    => 'auditor.pull-cabinet.get-juragan',
                'caption' => 'Auditor Tarik Kabinet Get Juragan'
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
        
        $permission = Permission::where('name', 'auditor.pull-cabinet.index')->first();
        $menuParent = Menu::where('name', 'Auditor')->first();

        $existSubMenu = SubMenu::where('name', 'Auditor Tarik Kabinet')->first();
        if (!$existSubMenu) {
            $newSubMenu = new SubMenu;
            $newSubMenu->name = 'Auditor Tarik Kabinet';
            $newSubMenu->menu_id = $menuParent->id;
            $newSubMenu->permission_id = $permission->id;
            $newSubMenu->order_no = 1;
            $newSubMenu->save();
        }
        $this->info('Submenu has created.');
    }
}
