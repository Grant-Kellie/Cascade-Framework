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

namespace CascadePHP\Routing;
use CascadePHP\Exceptions\ErrorResponse;

/**
 * Used to load/use code before the controller is called.
 * @return Invoke $middleware
 * @throws ErrorResponse 404 : Not found
 */
class Middleware{
    public function response($class_method, $args = null){     
        if(!empty($class_method)){               
            foreach ($class_method as $middleware) {
                if (is_callable($middleware)){
                    $middleware = explode('::',$middleware);
                    $class = (New $middleware[0]);
                    $method = $middleware[1];
                    $class->$method($args);                                  
                } else {
                    (new ErrorResponse)->status_code(404);
                }
            }
        }        
    }
}