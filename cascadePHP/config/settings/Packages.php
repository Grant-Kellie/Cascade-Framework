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

namespace CascadePHP\Config\Settings;
class Packages {
    public function include(){
        $this->composer();
    }

    // Allows for composer to work along side CascadePHP without native autoloader overlapping PSR-4
    public function composer(){
        $composer = $_SERVER['DOCUMENT_ROOT'].'/vendor/autoload.php';
        (file_exists($composer) ? require $composer : null); 
    }

}