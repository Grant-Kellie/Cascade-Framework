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

namespace CascadePHP\Config\Settings\Globals;
use CascadePHP\Exceptions\ErrorResponse;
class EnvConfig {

    /**
     * Globally load .ENV files.
     * File locations are stored in _env array
     */
    public function load($settings){
        if(!empty($settings['_env'])){
            foreach ($settings['_env'] as $id => $file){
                $file = __DIR__.'/'.$file;
                if (!is_readable($file)){
                    (new ErrorResponse)->custom_error('Unable to load Environment Settings',404);                  
                } 
                $envFile = file($file, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
                foreach ($envFile as $line){
                    (strpos(trim($line), '#') === 0 ? true : false);
                    list($name, $value) = explode('=', $line, 2);
                    $name = trim($name);
                    $value = trim($value);
        
                    if (!array_key_exists($name, $_SERVER) && !array_key_exists($name, $_ENV)) {
                        putenv(sprintf('%s=%s', $name, $value));
                        $_ENV[$name] = $value;
                        $_SERVER[$name] = $value;
                    }
                }                
            }  
        } 
    }
}