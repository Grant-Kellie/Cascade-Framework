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

use CascadePHP\Environment;
use CascadePHP\HttpServices\HttpMethods;
use CascadePHP\Exceptions\ErrorResponse;

use CascadePHP\FileServices\Mime\MimeType;
use CascadePHP\Utilities\ConvertDataType;

class HttpHeaders {

    public $convert;
    public $httpMethod;
    public $mimeType;
    public $environment;
    public $origin ;
    public $referrer;
    public $max_age;
    public $security_policy;

    /**
     * General functions required for base operation
     * of Router Class
     */
    public function __construct(){
        $this->convert = New ConvertDataType;  
        $this->httpMethod = New HttpMethods;    
        $this->mimeType = New MimeType;
        $this->environment = (New Environment)->settings(); 
    }

    /**
     * Form HTTP request header and data for response payload
     */
    public function compile($http, $endpoint){  
        $environment = $this->environment;
        
        // Http Method
        $headers = $this->includeHeaders($http, $environment);	
        $httpMethod = $this->httpMethod->exists($http->method, $headers);

        $method = $httpMethod->method;
        $status = $httpMethod->status;        
        $headers = $httpMethod->headers;
        
        // Mime Type (File Format)
        $format = $this->mimeType->format($status, $endpoint->format ?? 'html');
        $format = $format->headers;

        // Payload
        $compiled = (object) [
            'method' => $method,
            'status' => $status,
            'headers' => array_merge(array_filter($headers), $format),
        ];

        $this->responseHeaders($compiled);
    }

    /**
     * Send the HTTP headers for loading
     */
    public function responseHeaders($sent){ 
        foreach ($sent->headers as $header){            
            header($header);
        }
    }


    /** 
	* Sets the configuration from the environment to HTTP headers
	*/
    public function includeHeaders($http, $settings){
        if (!empty($settings->headers)){

            // $this->authentication($settings);
            // $this->caching($settings);
            // $this->clientHints($settings);
            // $this->conditionals($settings);
            // $this->connectionManagement($settings);
            // $this->contentNegoation($settings);
            // $this->controls($settings);
            // $this->cookies($settings);
            // $this->CORS($settings);
            // $this->DNT($settings);

            $this->security($http, $settings);
            return $this;            
        }
    }

    public function security($http, $settings){
        $domain = $http->http.$http->host;
        $permitted_origins = $settings->headers->permitted_origins;    
        $error_message = ['Configuration Error' => [
            'The server Origin has not been configured for ' . $domain,
        ]];
        (in_array($domain, $permitted_origins) ? $this->origin = $domain : (new ErrorResponse)->custom_error($error_message,403)); // null
        (!empty($settings->headers->referrer) ? $this->referrer = 'referrer-policy:'. $settings->headers->referrer : null);
        (!empty($settings->headers->cache_control) ? $this->max_age = 'cache-control:'.$settings->headers->cache_control : 'no-store');
        (!empty($settings->headers->security_policy) ? $this->security_policy = 'content-security-policy:'.$settings->headers->security_policy : 0);
        return $this;
    }







}