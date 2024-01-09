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

use FilesystemIterator;
use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;

use CascadePHP\Environment;
use CascadePHP\HttpServices\HttpRequest;
use CascadePHP\Exceptions\ErrorResponse;
use CascadePHP\Utilities\ConvertDataType;
use CascadePHP\FileServices\RegEx\Expression;

/**
 * Router handles the inbound url against the preset
 * routes and apis set within the config.
 * 
 * Given the vaidation is passed for the url paramaters
 * or clean route, headers are formed and middleware can be run
 * before the end path is loaded. 
 * 
 * @author Grant Kellie | contact@cascadephp.com
 * @version 1.0 | 2020-12-19 | 16:33 UTC
 */

class Router {

    /**
     * The value stored before the host if applicable
     * @var Object
     */
    public $subdomain;

    /**
     * Associated groups by nesting depth
     * @var Object
     */
    public $groups = []; 
    /**
     * used to keep track of the group paths depth
     * @var Object
     * @example https://cascadePHP/{group_one}/{group_two}/resource
     */
    public $depth = 0; // Contains the current depth of the active group.

    /**
     * Holds the HTTP request data 
     * @var Object
     */
    public $http;

    /**
     * Holds the route | api path data 
     * @var Object
     */
    public $endpoint;

    
    private $endpointData;



    public function __set(string $name, mixed $value): void {

    }


    /**
     * Initial process of the router with the purpose of
     * assisting with the desocvery of routes and apis that 
     * Loads any discovered routes and apis found
     * @return Array api | route
     */
    public function invoke(){          
        $app = New Router;  
        $this->regex = New Expression;
        $environment = (New Environment)->settings();     
        $filesDirectory = $environment->router->routes; 
        if (is_dir($filesDirectory)){

            /**
             * 
             * 
             * 
             * 
             * @todo: Should be own class called from utilities directory
             * 
             * 
             * 
             * 
             */
            $directoryIterate = new RecursiveDirectoryIterator($filesDirectory, RecursiveDirectoryIterator::SKIP_DOTS);
            $iterate = new RecursiveIteratorIterator($directoryIterate); 
            $iterate->rewind();
            
            while($iterate->valid()) {
                if ($iterate->getExtension() === 'php'){  
                    if (is_file($iterate->key())){ 
                        if(file_exists($iterate->key())){
                            include_once $iterate->key();
                        }                                     
                    }
                } $iterate->next();                                        
            }  
        }

        $this->responseFail();
    }

    /**
     * Traversed the subdomains givin within the routes and apis
     * aiding with the construction of an endpoint path.
     * The subdomain method considers google chromes
     * use against www. and m. subdomains
     * 
     * @return String $this->subdomain
     */
    public function subdomain(string $subdomain, object $closure){ 
        ($subdomain === 'www.' ? $this->subdomain = null : $this->subdomain = $subdomain);   
        return $this->is_callable($closure);        
    }
    
    /**
     * Gets the group path name to form a clean and managable 
     * set of relative routes.
     * If valid will invoke the group path
     * @return Array $this->groups
     */
    public function group(string $group, object $closure){
        $this->depth++;
        $this->groups += [
            $this->depth => $group
        ];
        $this->is_callable($closure);
        unset($this->groups[$this->depth]);
        $this->depth--; 
        return $this;
    }
    

    /**
     * @deprecated ?
     */
    // public function url(){
    //     $http = (object) $this->http;
    //     $endpoint = $this->endpoint;
    //     return $http->http.$http->subdomain.$http->domain;
    // }

    
    /**
     * @deprecated ?
     */
    // public function path(){
    //     $http = (object) $this->http;
    //     $endpoint = $this->endpoint;
    //     return ($endpoint->route ?? null);
    // }
      
    /**
     * Handles the process of filtering or validting
     * current or potential routes being accessed.
     * @param Array $data
     */
    public function route(array $data){  
        $this->configureData($data); 
        $this->configureHttp();      
        $this->configureEndpoint('route');          
        $this->locateEndpoint();    
    }


    /**
     * Validates current or potential APIs being accessed
     * @param Array $data
     */
    public function api(array $data){
        $this->configureData($data);      
        $this->configureHttp();        
        $this->configureEndpoint('api');       
        $this->locateEndpoint();          
    }

    /**
     * returns bundled of route / api as
     * referencable object.
     * @return Object $this->endpointData
     */
    public function configureData(array $data){   
        $this->endpointData = (New ConvertDataType())->toObject($data);
    }

    /**
     * configures the HTTP request into object
     * referencable data
     * @return Object $this->http
     */
    public function configureHttp(){
        $httpRequest = (New HttpRequest)->message();
        $this->http = array(           
            'http' => $httpRequest->http,
            'subdomain' => $subdomain ?? null,
            'domain' => $httpRequest->domain,
            'cleanUrl' => $httpRequest->cleanUrl,                  
        );
        $httpRequest = (array) $httpRequest;
        $httpPrepare = array_merge($this->http, $httpRequest);
        $this->http = (New ConvertDataType())->toObject($httpPrepare);
    }


    /**
     * Configures information supplied by the route / api
     * to accuratly represent the actions required to be intpreted.
     * @return Object $this->endpoint
     * @throws ErrorResponse 404 : Not found
    */
    public function configureEndpoint(string $type){  
        $http = $this->http;
        $endpoint = $this->endpointData;

        if(empty($endpoint->controller)){
            (new ErrorResponse)->status_code(404);
            
        } if(empty($endpoint->subdomain)){
            $endpoint->subdomain = $this->subdomain;

        } if(preg_match('~(?=[\/]).~',$endpoint->controller)){
            $endpoint->controller = preg_replace('~(?=[\/]).~','\\', $endpoint->controller);

        } if($endpoint->route === '/'){
            $endpoint->route = null;

        } if(empty($endpoint->groups)){
            $endpoint->groups = implode('',$this->groups) ?? null;

        } if(empty($endpoint->type)){
            $endpoint->type = $type; 

        } if(empty($endpoint->url)){
            $endpoint->url = $http->http.$http->subdomain.$http->domain.$endpoint->groups.$endpoint->route;

        } if (empty($endpoint->csrf_required)){
            $endpoint->csrf_required = 0;
        }

        return $this->endpoint = $endpoint;
    }

    /**
     * filters the endpoint path by parameterless
     * or clean URL
     * @return Stateless | Http Request and API path
     * @return Stateful | Http Request and Route path
     */
    public function locateEndpoint(){   
        $http = $this->http;
        $endpoint = $this->endpoint;  
          
        if($http->method === $endpoint->method && $http->subdomain === $endpoint->subdomain){
            if(($http->cleanUrl === $endpoint->url || $this->cleanUrl($http, $endpoint))){
                
                if($endpoint->type === 'route'){
                    (New Endpoint)->stateful($this->http, $this->endpoint); 
                } if($endpoint->type === 'api'){
                    (New Endpoint)->stateless($this->http, $this->endpoint);  
                } else {                
                    (new ErrorResponse)->status_code(404);
                }
                exit();           
            } 
        }
    }

    /**
     * Filter and assembe inbound http clean url url data
     * and count the attributes and parameters are valid
     * GET: www.example.com/document/about-clean-urls
     * @var Array $http | http request data
     * @var Array $endpoint | Current route
     */
    public function cleanUrl($http, $endpoint){
        $httpUrl = array_values(array_filter(explode('/',$http->url)));
        $endpointUrl = array_values(array_filter(explode('/',$endpoint->url)));

        if(!empty($endpoint->attributes) && preg_match("~$endpoint->url~",$http->cleanUrl) && $http->subdomain === $endpoint->subdomain){
            return (count($httpUrl) <= (count($endpointUrl) + count((array) $endpoint->attributes)) ? true : false);
        } else {
            return false;
        }
    }

    /**
     * Return the required route closure details to load the application
     * This can be relivant to subdomains, route nesting, groups and page loading controllers
     * @var Array: $closure
     * @return Invoke callback
     * @throws ErrorResponse 404 : Not found
     */
    public function is_callable($closure){    
        if (is_callable($closure)){                     
            call_user_func($closure);           
        } else {
            (new ErrorResponse)->status_code(404);
        }
    }


    /**
     * If a Route / API after searching all possible paths is not discoverable.
     * @throws ErrorResponse 404 : Not found
     */
    public function responseFail(){  
        (http_response_code(404) ? (new ErrorResponse)->status_code(404) : false); 
    }
}