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
class DateTime {


    function setDate($date){
        return date('Y-m-d', strtotime($date));
    }

    function currentTime(){
        return time();
    }



    function SetTimeZone($timezone = 'UTC'){
        return date_default_timezone_set("$timezone");
    }
    
}
