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
use CascadePHP\Session\Session;
use CascadePHP\Routing\Middleware;
use CascadePHP\HttpServices\HttpHeaders;
use CascadePHP\HttpServices\HttpResponse;
use CascadePHP\Exceptions\ErrorResponse;
use CascadePHP\Utilities\ConvertDataType;
use CascadePHP\Routing\Filter\FilterRequest;
use CascadePHP\Routing\Validation\ValidateRequest;

class Endpoint {

    /**
     * @todo
     * Perform HTTP request and loads the request endpoint.
     * Statless is used for API calls.
     * @param Object $htt | http request
     * @param Object $endpoint | contains endpoint settings
     * @throws ErrorResponse | Error 503 : Unavailable (Development Backlog)
     */
    public function stateless(object $http, object $endpoint){
        $convert = New ConvertDataType;
        $filterRequest = New FilterRequest;
        $validateRequest = New ValidateRequest;

        // (new ErrorResponse)->status_code(503); 
        http_response_code(503);
        
        // Condenses key components for systems and response
        $request = $filterRequest->packageRequestData($http, $endpoint);

        // Validate API KEY
        $validateRequest->apiKeyIsAuthentic($endpoint, $request);

        // Response
        $this->endpointMessage($http, $endpoint, $request);   
        exit();      
    }


    /**
     * Perform HTTP request and either redirects to
     * or loads the request endpoint.
     * Stateful holds the active session state of the agent.
     * @param Object $htt | http request
     * @param Object $endpoint | contains endpoint settings
     */
    public function stateful(object $http, object $endpoint){        
        $convert = New ConvertDataType;
        $session = New Session;
        $filterRequest = New FilterRequest;
        $validateRequest = New ValidateRequest;
        
        // Session management
        $session->session();
        $session->validateSession(); 

        // Condenses key components for systems and response
        $request = $filterRequest->packageRequestData($http, $endpoint);

        // Validate query count        
        $validateRequest->queryCountIsValid($request->query ?? ($_SESSION['cascade']['http']['query'] ?? null), $endpoint);        

        // Verify HTTP & Session state
        $this->locateEndpoint($http, $endpoint, $request);     
        exit();
    }


    /**
     * Locates the stateful endpoint for redirection 
     * or loading and calls the required methods.
     * @param Object $htt | http request
     * @param Object $endpoint | contains endpoint settings
     * @param Object $request | contains compiled response data
     */
    public function locateEndpoint(object $http, object $endpoint, object $request){
        $validateRequest = New ValidateRequest;        
        $session_redirect = $_SESSION['cascade']['http']['redirect'] ?? null;

        if(!isset($endpoint->redirect) || $endpoint->redirect === null){
            $validateRequest->csrfTokenIsAuthentic($endpoint, $request); 
            $this->endpointMessage($http, $endpoint, $request);

        } else if(!empty($_SESSION['cascade']['http']['query']) && ($session_redirect === urldecode($http->url))){
            $request->query = $_SESSION['cascade']['http']['query'];
            $request->body = $request->body ?? $_SESSION['cascade']['http']['body'] ?? null;
            $request->redirect = $session_redirect;
            $this->routeState($http, $request); 
            $validateRequest->csrfTokenIsAuthentic($endpoint, $request);                  
            $this->endpointMessage($http, $endpoint, $request);

        } else {
            $request->redirect = $validateRequest->redirectAction($http, $endpoint, $request);
            $this->routeState($http, $request); 

            $validateRequest->csrfTokenIsAuthentic($endpoint, $request); 
            header('location:'. $request->redirect);
        }                 
    }

    /**
     * Sends the necessary HTTP headers. 
     * Loads the request endpoint middleware and methods.
     * @param Object $htt | http request
     * @param Object $endpoint | contains endpoint settings
     * @param Object $request | contains compiled response data
     * @return 
     */
    public function endpointMessage(object $http, object $endpoint, object $request = null){

        // echo'<pre>';
        // print_r($endpoint);
        // exit;

        (New HttpHeaders())->compile($http, $endpoint);
        if(!empty($endpoint->middleware))(New Middleware())->response($endpoint->middleware, $request);      
        (New HttpResponse())->message($endpoint->controller, $request); 

        
    }


    /**
     * @todo move to HTTP Session folder
     * Sets the session HTTP state
     * @param Object $htt | http request
     * @param Object $endpoint | contains endpoint settings
     */
    public function routeState(object $http, object $request){
        date_default_timezone_set("UTC"); 
        $_SESSION['cascade']['http'] = [
            'method' => $http->method,
            'endpoint' => $http->url,
            'query' => $request->query ?? null,
            'body' => $request->body ?? null,
            'format' => $request->format ?? null,
            'redirect' => $request->redirect ?? null,
            'csrf' => $request->auth->csrf ?? null,
            'api_key' => $request->auth->api_key ?? null
        ];
    }

}