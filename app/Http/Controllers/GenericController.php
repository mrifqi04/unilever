<?php

namespace App\Http\Controllers;

/**
 * Description of GenericController
 *
 * @author nuansa.ramadhan
 */

use Illuminate\Support\Facades\Session;

class GenericController extends Controller {

    //put your code here
    public function __construct() {
        $this->middleware('auth');
        $this->middleware('acl');
    }

    protected function flashMessage($level, $title, $message) {
        Session::flash('message.level', $level);
        Session::flash('message.title', $title);
        Session::flash('message.data', $message);
    }

    protected function isSuperAdmin() {
        if (strtolower(\Illuminate\Support\Facades\Auth::user()->roles[0]->name) == "Super Admin") {
            return true;
        }
        return false;
    }

    protected function getUserRole() {
        return \Illuminate\Support\Facades\Auth::user()->roles[0];
    }

}
