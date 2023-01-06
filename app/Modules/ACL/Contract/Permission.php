<?php

namespace App\Modules\ACL\Contract;

/**
 *
 * @author nuansa.ramadhan
 */
interface Permission {

    /**
     * A permission can be applied to roles.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function roles();

    /**
     * A permission can be applied to users.
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsToMany
     */
    public function users();

    /**
     * Find a permission by its name.
     *
     * @param string $name
     *
     * @return Permission
     */
    public static function findByName($name);
}
