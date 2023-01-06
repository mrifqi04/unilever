<?php

namespace App\Console\Commands\Permission;

use Illuminate\Console\Command;
use App\Models\UserManagement\Permission;
use App\Models\UserManagement\Role;
use App\Models\UserManagement\SubMenu;

class SuperadminCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'permission:superadmin';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'New Permission for Superadmin\'s Role';

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
            'unilever.pull-cabinet.index',
            'unilever.pull-cabinet.show',
            'unilever.pull-cabinet.approve',
            'unilever.pull-cabinet.reject',
            'unilever.pull-cabinet.change-delivery-date',
            'unilever.pull-cabinet.export-outlet',
            'auditor.pull-cabinet.index',
            'auditor.pull-cabinet.show',
            'auditor.pull-cabinet.edit',
            'auditor.pull-cabinet.update',
            'auditor.pull-cabinet.destroy',
            'auditor.pull-cabinet.get-juragan',
        ];

        $newPermissionIds = Permission::whereIn('name', $newPermissions)->get()->pluck('id');

        $role = Role::where('name', 'ilike', 'super admin')->first();
        $updatedPermissions = $role->permissions->pluck('id')->merge($newPermissionIds)->unique();
        $role->permissions()->sync($updatedPermissions);
       
        $this->info('Role Permission has updated.');
    }
}
