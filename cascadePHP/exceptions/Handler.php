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
class Handler {

    // /**
    //  * register and include exception.
    //  */
    // public static function registerExceptionClass($class){    
    //     spl_autoload_register('Cascade\Handler::registerExceptionClass', true, true);
    //     return ($_SERVER['DIRECTORY_ROOT'].file_exists($class.'.php') ? include_once $class.'.php' : null);      
    // }

    /**
     * Loads the exception response class, method and message
     */
    public function exception($class, $method, $message = null){
        $response = $this->registerExceptionClass($class);        
        ($response == 1 ? (new $class)->$method($message) : $this->criticalException($response));
        exit();
    }
  
    /**
     * Informs that the exception file could
     * not be discovered.
     */
    public function criticalException($response){
        print_r($response);
    }
    
}

