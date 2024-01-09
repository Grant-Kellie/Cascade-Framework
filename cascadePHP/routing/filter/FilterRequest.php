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

namespace CascadePHP\Routing\Filter;
use CascadePHP\Exceptions\ErrorResponse;
use CascadePHP\Utilities\ConvertDataType;

class FilterRequest { 
    
    /**
     * Request made to filter cleanUrl style URL
     * data against the current endpoint if matched.
     * @param Object $http - inbound http request data
     * @param Object $endpoint - current route details
     * @return Object query | null
     * @return Object body | null
     */
    public function cleanUrlRequest(object $http, object $endpoint){
        if(!empty($endpoint->attributes)){
            if(preg_match("~$endpoint->url~",$http->cleanUrl)){                
                $query = $this->cleanUrlQuery($http,$endpoint) ?: $this->query($http,$endpoint);
                $body = $this->body($http,$endpoint) ?? null;
                return (New ConvertDataType())->toObject(
                    ['query' => $query ?? null, 'body' => $body ?? null]
                );
            }
        }
    }


    /**
     * Filter endpoint attributes against clean url / parameters.
     * If matching returns as query, otherwise returns as body.
     * Else If no data is passed returns null.
     * - Anything within braces { } is assumed clean/cleanUrl parameter data. 
     * @example Url: cascadePHP.com/article/{cleanUrl_data}
     * @example Url: cascadePHP.com/articles
     * @param Object $http - inbound http request data
     * @param Object $endpoint - current route details
     * @return Array $query
     */
    public function cleanUrlQuery(object $http,object $endpoint){
        $http->url = preg_replace('/\?.*$/','',$http->url); 
        $http_url = array_values(array_filter(explode('/',$http->url)));
        $endpoint_url = array_values(array_filter(explode('/',$endpoint->url)));
        $attributes = (array) $endpoint->attributes;
        $parameters = (array) array_values(array_diff($http_url, $endpoint_url));

        $id = 0; $query = [];
        foreach ($attributes as $attribute => $regex){
            if(!empty($parameters)){
                foreach ($parameters  as $key => $parameter){              
                    if($id === $key && @preg_match("$regex", $parameter)){                            
                        $query += [$attribute => $parameter];
                    } else if($id === $key && @!preg_match("$regex", $parameter)){
                        echo $attribute . ' : ' . $regex . ' : ' . $key . ' ' .$parameter;
                        (New ErrorResponse)->status_code(404);
                    }      
                } $id++; 
            }
        }
        return $query;
    }


    /**
     * Returns non-query related data, can be used
     * in addition to request parameter data
     * @param Object $http - inbound http request data
     * @param Object $endpoint - current route details
     * @return Object $data
     */
    public function query(Object $http, Object $endpoint){
        if(!empty($http->parameters) && !empty($endpoint->attributes)){
            $parameters = (array) $http->parameters ?? null;
            $attributes = (array) $endpoint->attributes;
            $query = array_intersect_key($parameters,$attributes);
            $regex = array_intersect_key($attributes,$parameters); 

            $data = array();
            foreach ($query as $key => $value){
                if(in_array($key,array_keys($regex))){
                    if(@preg_match("$regex[$key]", $value)){
                        $data += [
                            $key => $value
                        ];
                    }              
                }
            }  
              
            $order = array_keys($attributes); // Gets the order of data required            
            $sorted = array_merge(array_flip($order), $data);  // Sorts the data            
            $data = array_intersect_key($sorted,$data); // Gets the values that match, in order

            return (New ConvertDataType())->toObject($data);
        }
    }


    /**
     * Returns non-query related data, can be used
     * in addition to request parameter data
     * @param Object $http - inbound http request data
     * @param Object $endpoint - current route details
     * @return Object $data
     */
    public function body(object $http, object $endpoint){
        if(!empty($http->parameters)){
            $reserved = $this->reservedWords();
            $reserved = array_flip($reserved['parameters']);
            $parameters = (array) $http->parameters;
            $attributes = (array) $endpoint->attributes ?? null;        
        
            $data = array();
            foreach ($parameters as $key => $value){
                if(in_array($key, array_flip($reserved))){
                    unset($parameters[$key]);
                }
                else if(!in_array($key,array_keys($attributes))){
                    $data += [
                        $key => $value
                    ];                               
                }
            }         
            return (New ConvertDataType())->toObject($data);
        }
    }

    
    /**
     * Returns the request HTTP method that was request
     * by client.
     * @param Object $request
     * @return Object Http Method
     */
    public function method(object $request){
        return $request->parameters->_method ?? 
        $request->parameters->_http_method ?? 
        $http->method;
    }


    /**
     * returns a requested token
     * @param Object $http
     * @return Array $token
     */
    public function token(object $http){
        $parameters = (array) ($http->parameters ?? null);
        $options = array(
            '_csrf',   
            '_api_key',
        );

        $tokens = array();
        foreach ($parameters as $key => $value) {
            if(in_array($key,$options)){
                $tokens += [
                    ltrim($key,'_') => $value
                ];            
            }
        }       

        return $tokens;
    }


    /**
     * returns a requested file format
     * @param Object $htt | http request
     * @return String $format
     */
    public function format(object $http){
        $parameters = (array) $http->parameters;
        $options = array(
            '_format',
        );

        foreach ($parameters as $key => $value) {
            if(in_array($key,$options)){
                return $value;           
            }
        }       
    }


    /**
     * returns filtered request data and contains 
     * components for building endpoint response.
     * @param Object $htt | http request
     * @param Object $endpoint | contains endpoint settings
     * @return String packaged request
     */
    public function packageRequestData(object $http, object $endpoint){
        $session_redirect = $_SESSION['cascade']['http']['redirect'] ?? null;
        $cleanUrl = ($session_redirect != urldecode($http->url) ? $this->cleanUrlRequest($http, $endpoint) : null);
        $query = $cleanUrl->query ?? $this->query($http, $endpoint);
        $body = $cleanUrl->body ?? $this->body($http, $endpoint);

        return (New ConvertDataType())->toObject([  
            'query' => $query ?? null,  
            'body' => $body ?? null,               
            'parameter' => (!empty($http->query) ? $http->query : null),           
            'auth' => (!empty($http->parameters) ? $this->token($http) : null),           
            'format' => (!empty($http->parameters) ? $this->format($http) : null),
            'redirect' => $endpoint->redirect ?? $endpoint->url ?: null,
        ]);
    }


    /**
     * List of reserved Query keys that are used
     * for citicial parts of the system or application.
     * @return Array 
     */
    public function reservedWords(){
        return [
            'parameters' => [
                '_method',   
                '_http_method',
                '_csrf',   
                '_api_key',
                '_format',
            ],
        ];
    }

}
