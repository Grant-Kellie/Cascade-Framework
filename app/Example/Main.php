<?php 
namespace App\Example;
use Cascade;
use CascadePHP\Security\CSRF;
use CascadePHP\Database\DBLoader;

class Main extends Cascade {

    public function index($request){
        $data = [
            'title' => ' | Landing page',
            'template' => [
                'app/Example/assets/templates/index.php',
            ],   
        ];

        if(is_object($request)){    
            $this->render($data);            
        } else {
            echo 'no data request';            
        }


        echo '
            <br><br>
            <h3>www.cascadephp.com Routes</h3>
            <a href="/">Home</a><br>
            <a href="nested_one/route_n1">cascadephp.com/nested_one/route_n1</a><br>
            <a href="nested_one/nested_two/route_n2">cascadephp.com/nested_one/nested_two/route_n2</a><br>

            <br><br>

            <h3>admin.cascadephp.com Routes</h3>
            <a href="https://admin.cascadephp.com">Admin Panel</a><br>
            <a href="https://admin.cascadephp.com/users">Users</a><br>
            <a href="https://admin.cascadephp.com/user/create">Create User</a><br>
            <a href="https://admin.cascadephp.com/user/delete">Delete User</a><br>          
        ';


        exit;
    }
   

    public function renderDataAndTemplateExample($request){
        $data = [
            'title' => ' | Main Search',
            'template' => [
                'app/Example/assets/templates/search.php',
            ],   
            'token' => [
                'csrf' =>  (New CSRF())->token() ?? null,
                'api_key' => $request->auth->api_key ?? null,
            ]   
        ];

        if(is_object($request)){    
            $this->render($data);            
        } else {
            echo 'no data request';            
        }

        exit();
    }


    public function n1(){
        echo __FUNCTION__;
        exit;
    }



}