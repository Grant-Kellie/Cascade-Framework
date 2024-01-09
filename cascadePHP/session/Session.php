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

namespace CascadePHP\Session;

use CascadePHP\Environment;
use CascadePHP\Security\CSRF;
use CascadePHP\Security\Firewall;
use CascadePHP\Security\Firewall\ActivityMonitor;

use CascadePHP\HttpServices\HttpRequest;
use CascadePHP\Utilities\ConvertDataType;
use CascadePHP\Exceptions\ErrorResponse;


/**
 * 
 * 
 * @todo: Should be able to create simple session requests from developer
 * @deprecated: currently only allows for user session data tracking and validation
 * 
 * 
 * 
 */


class Session {

    /**
     * Holds the set value for a violation report
     * for use with the Firewall
     * @var Array
     */
    public $report = [];

    /**
     * Contains the Environment file settings
     * @var Array
     */
    public $environment = [];

    /**
     * Contains the inboud Http request settings
     * @var Array
     */
    public $settings = [];



    private $convert; 
    private $httpRequest;

    /**
     * General functions required for base operation
     * of Router Class
     */
    public function __construct(){
        $this->convert = New ConvertDataType;        
        $this->httpRequest = (New HttpRequest)->message();
        $this->environment = (New Environment)->settings(); 
    }

    /**
     * Base session settings
     */
    public function config(){
        date_default_timezone_set("UTC"); 

        $name = $this->environment->cookie->name ?? null;
        $timeout = $this->environment->cookie->timeout ?? null;   
        $renewal = $this->environment->cookie->renewal ?? null;        
        $path = $this->environment->cookie->path ?? null;
        $domain = $this->environment->cookie->domain ?? null;
        $secure = $this->environment->cookie->secure ?? null;
        $httponly = $this->environment->cookie->httponly ?? 1;
        $sameSite = $this->environment->cookie->sameSite ?? null;

        return $this->convert->toObject(array(
            'name' => (!empty($name) ? $name : null), 
            'timeout' => (!empty($timeout) ? strtotime($timeout) : 0),  
            'renewal' => (!empty($renewal) ? strtotime($renewal): 0),  
            'path' => (!empty($path) ? $path : null), 
            'domain' => (!empty($domain) ? $domain : null),   
            'secure' => (!empty($secure) ? $secure : null),
            'httponly' => (!empty($httponly) ? $httponly : 1),
            'sameSite' => (!empty($sameSite) ? $sameSite : null), 
        ));
    }

    /**
     * Create reference cookie for sessions
     */
    public function session_set_cookie(){     
        session_destroy();         
        $session = $this->config();              
        session_set_cookie_params(
            $session->timeout - time(), 
            $session->path, 
            $session->domain, 
            $session->secure, 
            $session->httponly,
        );          

        session_name($session->name); 
        session_start();   
    }

    /**
     * Destroy reference cookie for sessions
     */
    public function session_destroy_cookie(){               
        $session = $this->config(); 
        unset($_COOKIE[$session->name]);             
        setcookie(
            $session->name,
            '',
            -9999, 
            $session->path, 
            $session->domain, 
            $session->secure, 
            $session->httponly,
        );   
        return true;       
    }

    /**
     * Load sessions if session hasn't already started
     */
    public function session(){
        (headers_sent() === false && !isset($_COOKIE['cascade']) ? ini_set("session.cookie_domain", ".".$this->httpRequest->domain) : null);           
        if(!isset($_SESSION)){
            session_start();
        }  
    }

    /**
     * Validate sessions credentials match
     * Updates when required or if suspicious activiy noticed
     */
    public function validateSession(){
        $session = $this->config();         
        if(!empty($_SESSION[$session->name]) && !empty($_COOKIE[$session->name])){    
            if($_COOKIE[$session->name] != $_SESSION[$session->name]['cid']){
                $this->report = $this->convert->toObject([
                    'title' => 'Session / Cookie ID mismatch',
                    'description' => 'The session id provided by the client does not match with any server side ids\'s',
                ]);
                $this->sessionNotifyFirewall();
                throw (new ErrorResponse)->status_code(403);
            } if($_SESSION[$session->name]['ip'] != $_SERVER['REMOTE_ADDR']){
                $this->report = $this->convert->toObject([
                    'title' => 'IP Mismatch',
                    'description' => 'The IP addresses given by the client do not match.',
                ]);
                $this->sessionNotifyFirewall();
                throw (new ErrorResponse)->status_code(403);
            } if($_SESSION[$session->name]['browser'] != $_SERVER['HTTP_USER_AGENT']){
                $this->report = $this->convert->toObject([
                    'title' => 'Invalid user agent',
                    'description' => 'The server agent could not be recognised.',
                ]);
                $this->sessionNotifyFirewall();
                throw (new ErrorResponse)->status_code(403);
            }
            else if(time() >= $_SESSION[$session->name]['timeout_ms']){
                return $this->delete();
            }            
            else if(time() >= $_SESSION['cascade']['renews_ms']){
                return $this->renew();
            } 
        }
        if (empty($_SESSION['cascade']) || empty($_COOKIE['cascade'])){     
            $this->create();
        }  
    }

    /**
     * Form the user session & apply CSRF token
     */
    public function create(){              
        $session = $this->config();                  
        (!isset($_COOKIE['cascade']) ? $this->session_set_cookie() : false);           
        if(isset($_COOKIE['cascade'])){
            $_SESSION['cascade'] = [
                'cid' => $_COOKIE["cascade"],
                'csrf_token' => (new CSRF())->generateToken(),
                'ip' => $_SERVER['REMOTE_ADDR'],
                'browser' => $_SERVER['HTTP_USER_AGENT'], 
                'timestart' => date('Y-m-d H:i:s', time()),
                'renews' => ($session->renewal != 0 ? date('Y-m-d H:i:s', ($session->renewal)) : 'No time extention'),
                'timeout' => ($session->timeout != 0 ? date('Y-m-d H:i:s', ($session->timeout)) : 'Browser Session'),
                'timeout_ms' => ($session->timeout != 0 ? $session->timeout : 0),
                'renews_ms' => ($session->timeout != 0 ? $session->renewal : 0),
            ];  
        }                 
    }

    /**
     * Used for updating CSRF token on current session referenced with cookie
     */
    public function update(){    
        $session = $this->config();

        setcookie($session->name, $_COOKIE[$session->name],$session->timeout);
        $_SESSION[$session->name] = [
            'ip' => $_SERVER['REMOTE_ADDR'],
            'cid' => $_COOKIE[$session->name],
            'csrf_token' => (new CSRF())->generateToken(),            
            'browser' => $_SERVER['HTTP_USER_AGENT'], 
            'timestart' => date('Y-m-d H:i:s', time()),
            'renews' => ($session->renewal != 0 ? date('Y-m-d H:i:s', ($session->renewal)) : 'No time extention'),
            'timeout' => ($session->timeout != 0 ? date('Y-m-d H:i:s', ($session->timeout)) : 'Browser Session'),
            'timeout_ms' => ($session->timeout != 0 ? $session->timeout : 0),
            'renews_ms' => ($session->timeout != 0 ? $session->renewal : 0),
        ];      
    }

    /**
     * Refresh the current session if still active
     */
    public function renew(){    
        $session = $this->config();        

        session_regenerate_id(true);
        $_SESSION[$session->name] = [
            'ip' => $_SERVER['REMOTE_ADDR'],
            'cid' => $_COOKIE[$session->name],
            'csrf_token' => $_SESSION[$session->name]['csrf_token'],            
            'browser' => $_SERVER['HTTP_USER_AGENT'], 
            'timestart' => date('Y-m-d H:i:s', time()),
            'renews' => ($session->renewal != 0 ? date('Y-m-d H:i:s', ($session->renewal)) : 'No time extention'),
            'timeout' => ($session->timeout != 0 ? date('Y-m-d H:i:s', ($session->timeout)) : 'Browser Session'),
            'timeout_ms' => ($session->timeout != 0 ? $session->timeout : 0),
            'renews_ms' => ($session->timeout != 0 ? $session->renewal : 0),
        ];    
        
    }

    /**
     * Possible tampering to destroy current user session
     */
    public function delete(){   
        if(!isset($_SESSION)){
            session_destroy();
        }            
        $this->session_destroy_cookie();     
    }


    /**
     * Destroy session and log suspisious activity
     */
    public function sessionNotifyFirewall(){
        $this->delete();  
        $this->create();    
        $firewall = new ActivityMonitor;
        $firewall->logSessionActivity($this->report);             
    }


    /**
     * Share session token for use in forms
     */
    public static function sessionToken(){             
        if(!empty($_SESSION['cascade']) && !empty($_COOKIE['cascade'])){
            return $_SESSION['cascade']['csrf_token'] ?? null;
        } else {
            return; 
        }
    }

}