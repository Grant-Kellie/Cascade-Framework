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

namespace CascadePHP\Utilities;
class ConvertDataType {

    /**
     * Converts arrays or strings to objects
     * @var Array $data 
     * @return Object
     */
    function toObject($data){
        return json_decode(json_encode($data,False));
    }

    /**
     * Converts object or strings to array
     * @var Object $data 
     * @return Array
     */
    function toArray($data){
        return (array) $data;
    }
    
}
