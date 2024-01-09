<?php
namespace CascadePHP\Routing\Validation;
use CascadePHP\Security\CSRF;
use CascadePHP\Exceptions\ErrorResponse;
class validateRequest {
    
    /**
     * Validates the query values found against the set limit.
     * If not set will assume all are required. 
     * @param Array $query | contains the query values found in filtering process
     * @param Array $endpoint | contains endpoint settings
     * @throws ErrorResponse | Error 404 : Not found
     */
    public function queryCountIsValid($query, $endpoint){   
        if(!empty($endpoint->attributes)){        
            if(!empty($endpoint->method != 'get' && $endpoint->attribute_match) && count((array) $query) < $endpoint->attribute_match){
                (New ErrorResponse)->status_code(404);
            } else if(empty($endpoint->attribute_match) && count((array) $query) < count((array) ($endpoint->attributes))){
                (New ErrorResponse)->status_code(404);
            }
        }
    }


    /**
     * If the endpoint requires a CSRF token, an attemt to
     * Authenticate the CSRF token will be made by sending
     * and authenticating the HTTP request csrf and user session token.
     * @param Array $endpoint | contains the query values found in filtering process
     * @param Array $request | contains compiled response data
     * @return Bool 
     * @throws ErrorResponse | Error 401 : Unauthorized
     */
    public function csrfTokenIsAuthentic($endpoint, $request = null){
        $csrf = New CSRF;
        if($endpoint->csrf_required === true){
            if(!empty($_SESSION['cascade']['csrf_token']) && !empty($request->auth->csrf)){
                $csrf->authentication($request->auth->csrf, $_SESSION['cascade']['csrf_token']);        
            } if (!empty($_SESSION['cascade']['http']['csrf']) && !empty($_SESSION['cascade']['csrf_token'])){
                $csrf->authentication($_SESSION['cascade']['http']['csrf'], $_SESSION['cascade']['csrf_token']);
            } else {
                (new ErrorResponse)->status_code(401);
            }
        }
    }


    /**
     * Validates the path in which the endpoint has
     * set as a valid option for the current request
     * made.
     * @param Object $http | contains http request
     * @param Object $endpoint | contains the query values found in filtering process
     * @param Object $request | contains compiled response data
     * @return String redirect
     */
    public function redirectAction(object $http, object $endpoint, object $request){
        if($endpoint->redirect === true){ 
            echo 'parameters redirect TRUE<br>';
            return $endpoint->url.$this->redirectPath($request);                          
        } else if($endpoint->redirect === false){
            echo 'no parameters FALSE<br>';
            return $endpoint->url;
        } else if(is_string($endpoint->redirect) && preg_match("~$http->domain~", $request->redirect)){
            echo 'url match<br>';
            return $endpoint->redirect;
        } else if(is_string($endpoint->redirect) && !preg_match("~$http->domain~", $request->redirect)){
            echo 'url not match<br>';
            return $request->redirect;
        }
    }
    

    /**
     * If the endpoint requires a CSRF token, an attemt to
     * Authenticate the CSRF token will be made by sending
     * the HTTP request csrf and user session token.
     * @param Array $endpoint | contains the query values found in filtering process
     * @param Array $request | contains compiled response data
     * @return String uri query | null
     */
    public function redirectPath(object $request = null){
        return (!empty($request->query) ? '/'.implode('/',(array) $request->query) : null);
    }
    
}