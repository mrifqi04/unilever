<?php

namespace App\Events;

/**
 * Description of LoginListener
 *
 * @author nuansa.ramadhan
 */
use Illuminate\Auth\Events\Login;
use App\Models\UserManagement\Menu;

class AuthenticateListener {

    /**
     * @param  Login $event
     * @return void
     */
    public function handle(Login $event) {
        $menu = Menu::with(['submenus'=>function($q)use($event){
            return $q->whereIn('permission_id', $event->user->getPermissionsViaRoles()->pluck('id'))->orderBy("order_no", "asc");
        }])->whereHas('submenus', function($query) use ($event){
            return $query->whereIn('permission_id', $event->user->getPermissionsViaRoles()->pluck('id'))->orderBy("order_no", "asc");
        })->orderBy("order_no", "asc")->get();

        session(
                [
                    'menus' => $menu,
                ]
        );
    }

}
