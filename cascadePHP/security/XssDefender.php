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

namespace CascadePHP\Security;
class XssDefender{
    
    /**    
     * filters and removes data associated with XSS atacks
     * or data that may be deemed harmful to the application.
     * Should only be used on data being output
     * @var array: $data
     * @return: sanatize inbound special characters
     */
    public function recursiveHtmlSpecialChars($data){
        array_walk_recursive($data, function (&$value){ 
            $value = htmlspecialchars($value, ENT_COMPAT | ENT_HTML401, 'UTF-8', false);
        });
        return $data;
    }


    public function recursiveHtmlEntities($data){
        array_walk_recursive($data, function (&$value){ 
            $value = htmlentities($value, ENT_COMPAT | ENT_HTML401, 'UTF-8', false);
        });
        return $data;
    }


}