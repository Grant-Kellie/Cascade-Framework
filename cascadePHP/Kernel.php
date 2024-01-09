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

namespace CascadePHP;
use Cascade;
use CascadePHP\Routing\Router;
use CascadePHP\Config\Settings\Packages;
use CascadePHP\Config\Settings\Globals\EnvConfig;

include_once 'cascadePHP/Cascade.php';
include_once 'cascadePHP/config/settings/Environment.php';
include_once 'cascadePHP/autoload/Autoload_Cascade.php';

class Kernel extends Cascade {
    public function Initialize(){         
        $environment = $this->env();
        $autoload = new Autoload_Cascade;

    
        error_reporting(E_ALL);
        ($environment->mode === 'live' ? $autoload->invoke() : $autoload->map());

        // (new EnvConfig())->load($env);
        if(class_exists('Packages')){
            (new Packages())->include();
        }

        

        echo '<h3>Memory Usage</h3>';
        echo 'Allocated: ' . $this->convert(memory_get_usage(true)) . '<br>';
        echo 'Peak Usage: ' . $this->convert(memory_get_peak_usage(false)) . '<br>';
        
        (new Router())->invoke();       
    }


    public function convert($size){
        $unit = array('b','kb','mb','gb','tb','pb');
        return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
    }
}

(new Kernel())->Initialize();