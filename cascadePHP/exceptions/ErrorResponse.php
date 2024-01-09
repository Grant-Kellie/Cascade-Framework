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

namespace CascadePHP\Exceptions;
class ErrorResponse {

    /**
     * discover status code and delcare response
     */
    public function status_code(int $code){    
        $this->error($code);
    }

	/**
	 * a basic error response that loads and 
	 * responds the error message based on available
	 * http response codes.
	 * @param Integer $code
	 * @return HTML error message
	 */
    public function error(int $code){
        $status = $this->responseCodes();

        if(in_array($status[$code], $status)){
			http_response_code($code);
			echo '<pre>';
			echo "<h2>Error $code</h2>";            
            print_r($status[$code]);            
		}
		
		echo '<br>render to style template';

		exit();
	}

	/**
	 * For displaying custom error responses that 
	 * HTTP status codes alone cannot describe in full.
	 * Could be used to display Developer Error, User Errors
	 * or both.
	 * 
	 * Currently handles multiple errors only by 
	 * single depth multi-dimentional array.
	 * @param Array $message
	 * @param Integer $status
	 * @return HTML error message
	 */
	public function custom_error(array $message = null, int $status = 500){    
		http_response_code($status);
		
		if(is_array($message)){
			foreach ($message as $title => $data){
				echo '<h2>' . $title . '</h2>';
				if(is_array($data)){
					foreach($data as $id => $output){
						echo $id . ' ' . $output . '<br>'; 
					}
				}
			}
		} else {
			echo '<h2>' . $message . '</h2>';
		}

		exit();
    }



	/**
	 * Predefined http response code error messages that
	 * can be called for further usage.
	 * @return Array http status code and error message
	 */
	public function responseCodes(){
		return array(
			// Informational
			100	=> array('Continue', ''),
			101	=> array('Switching Protocol', ''),
			102	=> array('Processing', ''),

			// Success
			200	=> array('OK', ''),
			201	=> array('Created', ''),
			202	=> array('Accepted', ''),
			203	=> array('Non-authoritative Information', ''),
			204	=> array('No Content', ''),
			205	=> array('Reset Content', ''),
			206	=> array('Partial Content', ''),
			207	=> array('Multi-Status', ''),
			208	=> array('Already Reported', ''),
			226	=> array('IM Used', ''),

			// Redirection
			300	=> array('Multiple Choices', ''),
			301	=> array('Moved Permanently', ''),
			302	=> array('Found', ''),
			303	=> array('See Other', ''),
			304	=> array('Not Modified', ''),
			//305	=> array(' Use Proxy', ''), Deprecated
			307	=> array('Temporary Redirect', ''),
			308	=> array('Permanent Redirect', ''),

			// Client Errors
			400 => array('Bad Request', 'Unable to process request sent by client.'),
			401 => array('Unauthorized', 'Invalid authentication credentials.'),
	    	403 => array('Forbidden', 'The server has refused to fulfill your request.'),
	    	404 => array('Not Found', 'The document/file requested was not found on this server.'),
	    	405 => array('Method Not Allowed', 'The method specified in the Request-Line is not allowed for the specified resource.'),
	    	408 => array('Request Timeout', 'Your browser failed to send a request in the time allowed by the server.'),
	    	409	=> array('Conflict', ''),
			410	=> array('Gone', ''),
			411	=> array('Length Required', ''),
			412	=> array('Precondition Failed', ''),
			413	=> array('Payload Too Large', ''),
			414	=> array('Request-URI Too Long', ''),
			415	=> array('Unsupported Media Type', ''),
			416	=> array('Requested Range Not Satisfiable', ''),
			417	=> array('Expectation Failed', ''),
			421	=> array('Misdirected Request', ''),
			422	=> array('Unprocessable Entity', ''),
			423	=> array('Locked', ''),
			424	=> array('Failed Dependency', ''),
			426	=> array('Upgrade Required', ''),
			428	=> array('Precondition Required', ''),
			429 => array('Too many requests', 'You have made too many attempts.'),
			431	=> array('Request Header Fields Too Large', ''),
			444	=> array('Connection Closed Without Response', ''),
			451	=> array('Unavailable For Legal Reasons', ''),
			449	=> array('Client Closed Request', ''),	    	

			// Server Errors
	    	500 => array('Internal Server Error', 'The request was unsuccessful due to an unexpected condition encountered by the server.'),
	    	501	=> array('Not Implemented', ''),
	    	502 => array('Bad Gateway', 'The server received an invalid response from the upstream server while trying to fulfill the request.'),
	    	504 => array('Gateway Timeout', 'The upstream server failed to send a request in the time allowed by the server.'),
			505	=> array('HTTP Version Not Supported', ''),
			506	=> array('Variant Also Negotiates', ''),
			507	=> array('Insufficient Storage', ''),
			508	=> array('Loop Detected', ''),
			510	=> array('Not Extended', ''),
			511	=> array('Network Authentication Required', ''),
			599	=> array('Network Connect Timeout Error', '')
        );
	}    
}

