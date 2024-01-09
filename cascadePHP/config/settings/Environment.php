<?php
/**
 *  CascadePHP
 *  Copyright: Grant Kellie | contact@cascadephp.com
 *  
 *  Copyright & Licence
 *  For full details on the copyright and licencing of CascadePHP,
 *  please see the Licence file that comes with the source code.
 * 
 */

namespace CascadePHP;

/**
 * Globally available settings for core systems
 * @author Grant Kellie | contact@cascadephp.com
 * 
 */
class Environment {

    public $default = [
        'doctype' => 'html_5',
        'language' => 'en',
        'title' => 'CascadePHP',        
        'favicon' => [
            'public/images/favicon/default.ico',
        ],
        'metatag' => [
            '<meta charset="UTF-8">',
            '<meta name="viewport" content="width=device-width, initial-scale=1.0">',   
        ],
        'css' => [
            // 'public/scripts/css/default.css',
        ],
        'scripts' => [
            // 'public/scripts/js/test.js' => 'async',
        ],
        'style' => [
            '*' => 'margin:0; padding:0;',
            'body' => 'font-family:sans-serif;',
        ],
    ];

    /**
     * Directories available for autoloading 
     */
    public $autoload = [
        'app',
        'cascadePHP',
    ];

    public $mode = 'dev';

    public $system_logs = [
        'autoload' => 'cascadePHP/dev/logs/autoload.txt', 
        'router' => 'cascadePHP/dev/logs/router.txt', 
        'firewall' => 'cascadePHP/security/firewall/logs/',     
    ];

    public $global = [
        '.env',
    ];


    public $router = [
      'routes' => 'cascadePHP/config/routes'
    ];

    public $headers = [
        'permitted_origins' => [
            'https://cascadephp.com',  
            'https://www.cascadephp.com',  
            'https://admin.cascadephp.com',  
        ], 
        'cache_control' => 'No-Store',
        'expires' => 0,
        'referrer' => 'origin',
        'security_policy' => "img-src https://*; child-src 'none';",

    ];

    public $cookie = [
        'name' => 'cascade',
        'timeout' => '+9 hours', // session lifespan
        'renewal' => '+3 hours', // extend session lifespan after given time
        'path' => '/',  
        'domain' => '.cascadephp.com',       
        'secure' => true, 
        'httponly' => true, 
        'sameSite' => 'lax',
    ];


    /**
     * get all class variables within Environment.php
     */
    public function settings(){ 
        return json_decode(json_encode(get_class_vars(__CLASS__),False));       
    }     


}