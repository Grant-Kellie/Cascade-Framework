<?php
$app->subdomain('admin.', function() use ($app){    
    $app->route([
        'method' => 'get', 
        'route' => '/',         
        'controller' => 'App\Admin\ControlPanel::index',
        'middleware' => [
            'App\Middleware\User\Authorization::admin',
        ],
        'format' => 'html',   
    ]); 


    $app->group('/users', function() use ($app){

        $app->route([
            'method' => 'get', 
            'route' => '/',         
            'controller' => 'App\Admin\ControlPanel::users',
            'middleware' => [
                'App\Middleware\User\Authorization::admin',
            ],
            'format' => 'html',   
        ]); 

        $app->route([
            'method' => 'get', 
            'route' => '/create',      
            'attributes' => [ // Data values used to validate the route, limits input to regular expression requirements
                'forename' => '/^[a-zA-Z_-]+$/',
                'surname' => '/^[a-zA-Z_-]+$/',
                'age' => '/^[0-9]+$/', // $this->regex->numeric()
                'email' => '/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/',
            ],     
            'controller' => 'App\Admin\ControlPanel::create_user',
            'middleware' => [
                'App\Admin\Middleware\Authorization::admin',            ],
            'format' => 'html', 
            'redirect' => false,
            'csrf_required' => false,
        ]); 

        //users
        $app->route([
            'method' => 'delete', 
            'route' => '/delete',     
            'attributes' => [ 
                'email' => '/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/',
            ],          
            'controller' => 'App\Admin\ControlPanel::delete_user',
            'middleware' => [
                'App\Admin\Middleware\Authorization::admin',
            ],
            'format' => 'html',   
            'redirect' => false,
            'csrf_required' => true,
        ]); 
    
    }); 

}); 