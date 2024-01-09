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

namespace CascadePHP\FileServices\Mime;
use CascadePHP\Http\Message;
use CascadePHP\Exceptions\ErrorResponse;

class MimeType {

	/**
	 * returns the settings for the request mimetype or throws an error
	 * settings for compatible mimetypes are modifiable for developer
	 * requeirements.
	 */
	public function format($status, $type){		
		$mimetypes = __DIR__.'/config/mimetypes.json';

		if(file_exists($mimetypes)){
			$file = file_get_contents($mimetypes);		 
			$data = json_decode($file,true);
			$mime = $data[0];
			if (in_array($type, array_keys($mime))) {
				return (object) array(			
					'status' => $status ?: 302,	
					'headers' => [
						"Content-Type:" . $mime[$type],
						"Accept:" . $mime[$type],
					],			
				);	
			}
		}else {
			(new ErrorResponse)->error(424);
		}
	}
}