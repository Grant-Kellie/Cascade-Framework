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

namespace CascadePHP\Security;

use CascadePHP\Environment;
use CascadePHP\HttpServices\HttpRequest;

use CascadePHP\Utilities\ConvertDataType;


class Firewall {

    public function __construct(){
        $this->environment = (New Environment)->settings(); 
    }


    public function httpRequest(){
        $request = (New HttpRequest)->message();        
        return array(
            'http_method' => $request->method,
            'url' => $request->rest_url,
        );
    }

    public function logIP(){
        return $_SERVER['REMOTE_ADDR'];        
    }
    
    public function logUserAgent(){
        return $_SERVER['HTTP_USER_AGENT'];
    }

}