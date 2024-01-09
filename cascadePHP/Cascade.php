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

use CascadePHP\Environment;
use CascadePHP\Utilities\Utilities;


/**
 * Should be contained within their own toolbox
 */
use CascadePHP\View\Html;
use CascadePHP\View\Head;
use CascadePHP\View\Body;
use CascadePHP\Http\Client;
use CascadePHP\Database\QueryBuilder;
use CascadePHP\Utilities\ConvertDataType;



/**
 * @deprecated Cascade is God class
 * @todo develop independant class::functions
 */
class Cascade {

    /**
     * @var Object
     * @return Array
     * Contains a set of accessible classes that contain general
     * purpose, portable functions.
     */
    public function utilities($request = null){
        $options = array(
            'ConvertDataType' => 'CascadePHP\Utilities\ConvertDataType',
            'DateTime' => 'CascadePHP\Utilities\DateTime',
        );

        if(in_array($request, array_keys($options))){
            return New $options[$request];
        } else {
            return null;
        }  
        
    }




    /**
     * @var Object
     * @return Array
     * Access the Environment PHP file settings
     */
    public function env($request = null){
        $env = new Environment(); 
        if(empty($request)){
            return $env;
        } else {
            return $env->$request;
        }  
    }    

    /**
     * Renders data and HTML to page
     */
    public function render($data){
        $convert = New ConvertDataType;
        $environment = (New Environment)->settings(); 
        $html = new Html;

        $html->doctype($environment->default->doctype);
        $html->lang($environment->default->language);

        $data = $convert->toObject($data);  
   
        (new Body())->render($data);              
    }
 
}