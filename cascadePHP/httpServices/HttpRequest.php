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
use CascadePHP\Utilities\ConvertDataType;

class HttpRequest {    

    /**
     * @var String
     */
    private $method;

    /**
     * @var String
     */
    private $http;

    /**
     * @var String
     */
    private $url;

    /**
     * @var String
     */
    private $host;

    /**
     * @var String
     */
    private $domain;

    /**
     * @var String
     */
    private $subdomain;

    /**
     * @var String
     */
    private $uriQuery;

    /**
     * @var Array
     */
    private $uriParams;

    /**
     * @var String
     */
    private $cleanUrl;

    /**
     * @var String
     */
    private $path;
    
    /**
     * @var Array
     */
    private $request;

    /**
     * //@var Array
     */
    private $requestParameters;

    private $parameters;
    private $convert;    



    public function __set(string $name, mixed $value): void {

    }

    public function __construct(){
        $this->convert = New ConvertDataType;
    }

    /**
     * Gets and Sets HTTP request into components
     * @return Object Configured Url
     */
    public function message(){
        $this->httpConfig();

        $data = array(
            'method' => $this->method,
            'http' => $this->http,            
            'host' => $this->host, 
            'subdomain' => $this->subdomain ?? null,            
            'domain' => $this->domain,
            'url' => $this->url,
            'cleanUrl' => $this->cleanUrl,
            'path' => $this->path,
            'parameters' => $this->parameters,
            'query' => (!empty($this->uriQuery) ? '?'.$this->uriQuery : null),                         
        );

        $data = array_filter($data);
        return $this->convert->toObject($data);
    }

    /**
     * Sets the configuration data for an
     * inbound request.
     */
    public function httpConfig(){        
        $this->http = $this->http();
        $this->url = $this->url($this->http);
        $this->host = $this->host($this->url);        
        $this->domain = $this->domain($this->http,$this->host);
        $this->subdomain = $this->subdomain($this->host,$this->domain);
        $this->uriQuery = $this->uriQuery($this->url);
        $this->uriParams = $this->uriParams($this->uriQuery);
        $this->requestParameters = $this->requestParameters();

   

        $this->parameters = array_merge($this->uriParams,$this->requestParameters);
        $this->method = $this->method();
        $this->path = $this->path($this->url);        
        $this->cleanUrl = $this->cleanUrl($this->url);
    }
    
    /** 
	* Discovers the reuqest HTTP method sent by client
    * Used for validation with server side request
    * @return String request method | get
	*/
	public function method(){
		return $this->requestMethod($this->parameters) ?? strtolower($_SERVER['REQUEST_METHOD']);
    }    

    /**
     * Obtains http method sent by user request, located
     * within the parameter data
     * @param Array $request
     * @return Array $match
     */
    public function requestMethod($request){      
        $options = array(
            '_method',   
            '_http_method',
        );
        
        $match = array_intersect_key($request, array_flip($options));
        if($match){
            return array_shift($match);
        }
    }

    /**
     * Gets a validated Http('s) state basced on the active url 
     * @return String Http / Https
     */
	public function http(){
		return ($_SERVER['HTTPS'] == true ? 'https://' :  'http://');
	}

    /**
     * Get the domain and subdomain in use
     * @param String $url
     * @return String host with subdomain attached
     */
	public function host(string $url){
		return parse_url($url)['host']; 
	}

    /**
     * Removes the subdomain from the url
     * @param String $http
     * @param String $host
     * @return String host without subdomain or http
     */
	public function domain(string $http,string $host){
        preg_match('/(?P<domain>[a-z0-9][a-z0-9\-]{1,63}\.[a-z\.]{2,6})$/', $http.$host, $domain);
		return $domain[0];
	}

    /**
     * Gets the subdomain of the active URL
     * @param String $host
     * @param String $domain
     * @return String Active subdomain
     */
    public function subdomain(string $host, string $domain){
        if($host != $domain) preg_match('/[^.]*./', $host, $subdomain);
        return $subdomain[0] ?? null;
    }

    /**
     * Gets the parameter data from the url if exists
     * or returns null
     * @param String $url | active url
     * @return String parameter data
     */
	public function uriQuery(string $url){
		return parse_url($url)['query'] ?? null;
    }
    
    /**
     * Gets the parameter data as a key => value pair
     * @param String $uri | parameter query string
     * @return Array $parameters
     */
    public function uriParams($uri){  
        if(!empty($url)){   
            parse_str($uri, $data);
            $parameters = $data;            
        }

        return $parameters ?? array();
    }

    /**
     * returns the path of the active url
     * @param String $url
     * @var String: url path
     */
    public function path(string $url){
        return parse_url($url)['path'];
    }

	/**
     * Sets the full url from the active url components in circulating routes
	 * dynamic generation per route for discovery
     * @param String $http
     * @var String: url
     */
	public function url(string $http){
		return $http.$_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI'];
    }	

	/**
     * Search Engine Friendly Url / Clean Url
	 * removes any query strings attached to url
     * @param String
     * @var String $url
     */
    public function cleanUrl($url){
        $url = preg_replace('/\?.*/', '', $url);
        return rtrim($url,'/');
    }

	/**
     * Sets the full url from the active url components in circulating routes
	 * dynamic generation per route for discovery
     * @return Http_parameters 
     */
    public function requestParameters(){
        $request = file_get_contents('php://input');    
        $input = array();
        parse_str($request, $input);
        return (!empty($input) ? $input : array());    
    }
}