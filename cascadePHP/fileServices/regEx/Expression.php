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

namespace CascadePHP\FileServices\RegEx;
use CascadePHP\Exceptions\ErrorResponse;

class Expression {

    public function any(){
        return '/[^\n]+/';
    }

    /**
     * returns lowercase - uppercase alphabet
     * expressions
     */
    public function a_z(){
        return '/^[a-zA-Z]+$/';
    }
    

    public function void_space(){
        return '/^[a-zA-Z\s\S+]+$/m';
    }


    /**
     * returns upper alphabet
     * expressions
     */
    public function a_z_upper(){
        return '/^[A-Z]+$/';
    }

    /**
     * returns lowercase alphabet
     * expressions
     */
    public function a_z_lower(){
        return '/^[a-z]+$/';
    }

    /**
     * returns numeric
     * expressions
     */
    public function numeric(){
        return '/^[0-9]+$/';
    }

    /**
     * returns full alphabet with numeric values
     * expressions
     */
    public function alphaNumeric(){
        print_r(__FUNCTION__);
    }

    /**
     * returns email expression
     */
    public function email(){
        return '/^([a-zA-Z0-9_\-\.]+)@([a-zA-Z0-9_\-\.]+)\.([a-zA-Z]{2,5})$/';
    }

}