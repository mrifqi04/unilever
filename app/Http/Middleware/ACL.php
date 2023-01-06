<?php

namespace App\Http\Middleware;

/**
 * Description of ACL
 *
 * @author nuansa.ramadhan
 */
use Illuminate\Support\Facades\Auth;
use App\Exceptions\UnauthorizedException;
use Closure;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Redirect;

class ACL {

    public function handle($request, Closure $next) {
        if (Auth::guest()) {
            throw UnauthorizedException::notLoggedIn();
        }
        if($request->route()->action['as'] == 'home.dashboard'){
            return $next($request);
        }
        if(Auth::user()->getPermissionByName($request->route()->action['as'])){
            return $next($request);
        }
        Session::flash('message.level', "error");
        Session::flash('message.title', "PERMISSION");
        Session::flash('message.data', "You dont have access to this page");
        return Redirect::to(route('home.index'));
    }

}
