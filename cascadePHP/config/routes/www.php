<?php
$app->subdomain('www.', function() use ($app){    
    $app->route([
        'method' => 'get', 
        'route' => '/',         
        'controller' => 'App\Example\Main::index',
        'format' => 'html',   
    ]); 

    $app->route([
        'method' => 'get', 
        'route' => '/render',         
        'controller' => 'App\Example\Main::renderDataAndTemplateExample',
        'format' => 'html',     
    ]);


    // Example of grouped routes / nested routes 
    $app->group('/nested_one', function() use ($app){
        
        $app->route([ // optional route
            'method' => 'get', 
            'route' => '/route_n1',         
            'controller' => 'App\Example\Main::n1',
            'format' => 'html',   
        ]); 

        $app->group('/nested_two', function() use ($app){            
            $app->route([ // optional route
                'method' => 'get', 
                'route' => '/route_n2',         
                'controller' => 'App\Example\Main::n2',
                'format' => 'html',   
            ]);     
        });        
    }); 
      
});