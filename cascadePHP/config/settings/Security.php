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
class Security {

    private $security = [
        'keys' => [
            'private' => '/cascade/config/security/private.php',
            'public' => '/cascade/config/security/public.php',
        ],

        'algorithm' => [
            'default_encryption_algo' => [
                'libSodium',
            ],
            'default_password_algo' => [
                'argon2', 
                'blowfish',
                'sha256',    
            ],
        ],

        'csrf' => [
            'complexity' => 64,
        ],
    ];

    /**
     * get all class variables within Environment.php
     */
    public function settings(){       
        return get_class_vars(__CLASS__);       
    }   

}