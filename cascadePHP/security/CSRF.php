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
use CascadePHP\Config\Settings\Security;
use CascadePHP\Session\Session;
use CascadePHP\Exceptions\ErrorResponse;

class CSRF {

    /**
     * retrive the user token
     * @uses $_SESSION
     * 
     */
    public function token(){
        if($_SESSION){
            return $_SESSION['cascade']['csrf_token'];
        }
    }

    /**
     * Generates a csrf token based on a pre-defined 
     * token complexity of 32 or developer defined.
     * @uses tokenComplexity
     * @return String high entropy token
     * @link https://www.php.net/manual/en/function.random-bytes.php 
     */
    public function generateToken(){      
        return bin2hex(random_bytes($this->tokenComplexity()));
    }

    /**
     * Authentication the inbound request token with the user token
     * @param String $http_token | http request token
     * @param String $session_token | user session_cookie csrf
     * @throws ErrorResponse 403
     */
    public function authentication(string $http_token = null, string $session_token = null){
        if($http_token != $session_token || empty($http_token) || empty($session_token)){
            (new ErrorResponse)->status_code(403);
        } if($http_token === $session_token){
            return true;
        }
    }
    
    /**
     * request CSRF token by complexity
     * Token complexity at a minimum should be 32
     * @return Int defined | 32
     */
    public function tokenComplexity(){
        $config = (New Security)->settings();         
        if(!empty($config->security->csrf->complexity) && $config->security->csrf->complexity >= 32){
            return $config->security->csrf->complexity;
        } else {
            return 32;
        }
    }

}