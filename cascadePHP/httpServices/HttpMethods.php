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
use CascadePHP\Exceptions\ErrorResponse;

class HttpMethods {

    public function exists($method, $headers){     
        if(method_exists(__CLASS__,"$method") && !empty($headers)){
            return (object) $this->$method($headers);
        } else {
            (New ErrorResponse)->error(404); 
        }       
    }

    /** 
	* @method: Get
	* @return: reads existing records
	*/
    public function get($header){	
		return array(
			'method' => strtoupper(__FUNCTION__),						
            'status' => 200,
            'headers' => [
                'Access-Control-Allow-Origin:'. $header->origin,
                'Access-Control-Allow-Headers: access',
                'Access-Control-Allow-Methods: GET',
                'Access-Control-Allow-Credentials: true',
                $header->security_policy ?? null,
                $header->referrer ?? null,
                $header->max_age ?? null,
            ]
        );
	}


    /** 
	* @method: Head
	* @return: Response has only HTTP header fields and no payload is sent in response.
	*/
    public function head($header){		
		return array(
			'method' => strtoupper(__FUNCTION__),				
            'status' => 204,
            'headers' => [
                'Access-Control-Allow-Origin:'. $header->origin,
                'Access-Control-Allow-Headers: access',
                'Access-Control-Allow-Methods: HEAD',
                'Access-Control-Allow-Credentials: true',
                $header->security_policy ?? null,
                $header->referrer ?? null,
            ]
        );
    }
    
    
    /** 
	* @method: Options
	* @return: A list of valid request methods associated with the resource using Allow header.
	*/
    public function options($header){		
		return array(
			'method' => strtoupper(__FUNCTION__),						
            'status' => 204,
            'headers' => [
                'Access-Control-Allow-Origin:'. $header->origin,
                'Access-Control-Allow-Headers: access',
                'Access-Control-Allow-Methods: OPTIONS',
                'Access-Control-Allow-Credentials: true',
                $header->security_policy ?? null,
                $header->referrer ?? null,
            ]
        );
    }


    /** 
	* @method: Trace
	* @return: A representation of the request message as received by the end server.
	*/
    public function trace($header){		
		return array(
			'method' => strtoupper(__FUNCTION__),						
            'status' => 200,
            'headers' => [
                'Access-Control-Allow-Origin:'. $header->origin,
                'Access-Control-Allow-Headers: access',
                'Access-Control-Allow-Methods: TRACE',
                'Access-Control-Allow-Credentials: true',
                $header->security_policy ?? null,
                $header->referrer ?? null,
            ]
        );
    }
    

	/** 
	* @method: Post
	* @return: creates new reords
	*/	
	public function post($header){
		return array(
			'method' => strtoupper(__FUNCTION__),				
            'status' => 201,
            'headers' => [
                'Access-Control-Allow-Origin:'. $header->origin,
                'Access-Control-Allow-Headers: access',
                'Access-Control-Allow-Methods: POST',
                'Access-Control-Allow-Credentials: true',
                $header->security_policy ?? null,
                $header->referrer ?? null,
            ]
        );
	}


	/** 
	* @method: Put
	* @return: creates new reords
	*/	
	public function put($header){
		return array(
			'method' => strtoupper(__FUNCTION__),			
            'status' => 200,
            'headers' => [
                'Access-Control-Allow-Origin:'. $header->origin,
                'Access-Control-Allow-Headers: access',
                'Access-Control-Allow-Methods: PUT',
                'Access-Control-Allow-Credentials: true',
                $header->security_policy ?? null,
                $header->referrer ?? null,
            ]
        );
	}

    
	/** 
	* @method: Patch
	* @return: creates new reords
	*/	
	public function patch($header){
		return array(
			'method' => strtoupper(__FUNCTION__),						
            'status' => 200,
            'headers' => [
                'Access-Control-Allow-Origin:'. $header->origin,
                'Access-Control-Allow-Headers: access',
                'Access-Control-Allow-Methods: PATCH',
                'Access-Control-Allow-Credentials: true',
                $header->security_policy ?? null,
                $header->referrer ?? null,
            ]

        );
	}


	/** 
	* @method: Delete
	* @return: creates new reords
	*/	
	public function delete($header){		
		return array(
			'method' => strtoupper(__FUNCTION__),						
            'status' => 200,
            'headers' => [
                'Access-Control-Allow-Origin:'. $header->origin,
                'Access-Control-Allow-Headers: access',
                'Access-Control-Allow-Methods: DELETE',
                'Access-Control-Allow-Credentials: true',
                $header->security_policy ?? null,
                $header->referrer ?? null,
            ]
        );
    }   
}


