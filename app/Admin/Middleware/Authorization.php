<?php 
// Follows folder path as conevention App\Admin\Middleware
namespace App\Admin\Middleware;
class Authorization {

    public function admin($request){
        echo 'Admin Middleware Controller';
    }



}