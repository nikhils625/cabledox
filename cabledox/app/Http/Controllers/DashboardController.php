<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class DashboardController extends Controller
{
    function index(){ 
        $user  = \Auth::user(); 
        
        $role_id = 0;
        if(!@$user->roles->isEmpty() && @$user->roles->count() == 1) {
            $role_id = @$user->roles[0]->id;
        }

        if($role_id == 1) {
            return $this->superAdmin();
        }
        elseif($role_id == 2) {
            return $this->admin();
        }
        elseif($role_id == 3) {
            return $this->electrician();
        }
        elseif($role_id == 4) {
            return $this->manager(); 
        }
        elseif($role_id == 5) {
            return $this->supervisor();
        }
    }
   
    function superAdmin(){
        return view('dashboard.super-admin');
    }

    function admin(){

        return view('dashboard.admin');
    }

    function electrician(){   
        return view('dashboard.electrician');
    }

    function supervisor(){
       return view('dashboard.supervisor');
    }

    function manager(){
       return view('dashboard.manager');
    }
}
