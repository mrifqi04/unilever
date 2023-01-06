<?php

namespace App\Console\Commands\Permission;

use Illuminate\Console\Command;
use App\Models\UserManagement\Permission;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\SubMenu;

class AdminCallCenterCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:admin-call-center';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'New Permission for Admin Call Center\'s Role';

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
        $newPermissions = [
            'callcenter.pull-cabinet.index',
            'callcenter.pull-cabinet.show',
            'callcenter.pull-cabinet.update',
            'callcenter.pull-cabinet.approve',
            'callcenter.pull-cabinet.cancel',
            'callcenter.pull-cabinet.postpone',
            'callcenter.pull-cabinet.export-outlet',
        ];

        $newPermissionIds = Permission::whereIn('name', $newPermissions)->get()->pluck('id');

        $role = Role::where('name', 'ilike', 'admin call center')->first();
        $updatedPermissions = $role->permissions->pluck('id')->merge($newPermissionIds)->unique();
        $role->permissions()->sync($updatedPermissions);
       
        $this->info('Role Permission has updated.');
    }
}
