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

namespace CascadePHP\View;
use CascadePHP\Http\Client;
use CascadePHP\Exceptions\ErrorResponse;
class Body {

    public function render($render){
        $csrf = $render->token->csrf ?? null;
        $api_key = $render->token->api_key ?? null;

        if(is_array($render->template)){
            foreach($render->template as $id => $view){                
                if(is_string($view) && file_exists($view)){
                    include_once $view;
                } else {
                    (new ErrorResponse)->custom_error('File could not be found.',404);      
                }
            }
        } else {
            if(is_string($render->template) && file_exists($render->template)){
                include_once $render->template;
            } else {
                (new ErrorResponse)->custom_error('File could not be found.',404);      
            }
        }
 
    }

}