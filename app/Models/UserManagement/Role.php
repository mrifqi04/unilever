<?php

namespace App\Models\UserManagement;

/**
 * Description of Role
 *
 * @author nuansa.ramadhan
 */
use Illuminate\Database\Eloquent\Model;
use App\Modules\ACL\Contract\Role as RoleContract;
use App\Modules\ACL\Traits\HasPermissions;

class Role extends Model implements RoleContract {

    use HasPermissions;

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'public.roles';
    public $incrementing = false;
    protected $keyType = 'string';

    /**
     * The attributes that aren't mass assignable.
     *
     * @var array
     */
    public $guarded = ['id'];

    /**
     * Create a new Eloquent model instance.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = []) {
        parent::__construct($attributes);
    }

    public function permissions() {
        return $this->belongsToMany(
                        'App\Models\UserManagement\Permission', 'public.role_has_permissions', 'role_id', 'permission_id'
        );
    }

    public function hasPermissionTo($permission) {
        if (is_string($permission)) {
            $permission = app(Permission::class)->findByName($permission);
        }

        return $this->permissions->contains('id', $permission->id);
    }

    public function users(){
        return $this->belongsToMany(
            'App\Models\UserManagement\User', 'public.user_has_roles'
        );
    }

    public static function findByName($name) {
        $role = static::where('name', $name)->first();

        if (!$role) {
            throw new Exception("Role Doesnt Exist");
        }

        return $role;
    }

    protected $dateFormat = 'Y-m-d H:i:s.u O';
}
