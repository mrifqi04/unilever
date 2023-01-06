<?php

namespace App\Models\UserManagement;

/**
 * Description of Permission
 *
 * @author nuansa.ramadhan
 */

use Illuminate\Database\Eloquent\Model;
use App\Modules\ACL\Contract\Permission as PermissionContract;

class Permission extends Model implements PermissionContract
{

    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'public.permissions';
    public $guarded = ['id'];
    public $incrementing = false;
    protected $keyType = 'string';
    protected $dateFormat = 'Y-m-d H:i:s.u O';

    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    public function roles()
    {
        return $this->belongsToMany(
            'App\Models\UserManagement\Role', 'public.role_has_permissions'
        );
    }

    public function users()
    {
        return $this->belongsToMany(
            'App\Models\UserManagement\User', 'public.user_has_permissions'
        );
    }

    public static function findByName($name)
    {
        $permission = static::getPermissions()->where('name', $name)->first();

        if (!$permission) {
            throw new Exception("Permission doesnt exist");
        }

        return $permission;
    }

    public function submenus()
    {

        return $this->hasMany(SubMenu::class);
    }

}
