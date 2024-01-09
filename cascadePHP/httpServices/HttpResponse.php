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

namespace CascadePHP\HttpServices;
use CascadePHP\Exceptions\ErrorResponse;
class HttpResponse {
    /**
     * Load the controller and send data if applicable
     * for HTTP response / output.
     */
    public function message($controller, $response){ 
        $class_method = explode('::',$controller); 
        $class = $class_method[0];
        $method = $class_method[1];

        if(method_exists($class,$method)){
            $class = new $class();
            $class->$method($response);     
        } else {
            (new ErrorResponse)->status_code(404);
        }

        exit();
    }


    /**
     * respond with the redirect state and commit redirect response.
     */
    public function redirect($location){              
        http_response_code(302 ?? 301);
        header("location:". $location);
        exit();
    }
}
