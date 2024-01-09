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

namespace CascadePHP\Config\Settings;
class Database {

    private $database = [
        'database_1' => [
            'driver' => 'mysql',
            'host' => null,
            'user' => null,
            'password' => null,
            'charset' => null,
            'collation' => null,
            'prefix' => null,
            'server_version' => null,
        ],

        'database_2' => [
            'driver' => 'postgresql',
            'host' => null,
            'user' => null,
            'password' => null,
            'charset' => null,
            'collation' => null,
            'prefix' => null,
            'server_version' => null,
        ],
    ];

    /**
     * get all class variables within Environment.php
     */
    public function settings(){       
        return (object) get_class_vars(__CLASS__);       
    }   
}