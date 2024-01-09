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

namespace CascadePHP\Security\Firewall;

use CascadePHP\Environment;
use CascadePHP\Security\Firewall;

class ActivityMonitor extends Firewall {

    public function logSessionActivity(object $report = null){
        $config = $this->environment->system_logs;
        $file_directory = $config->firewall.'firewall-'.date('Y-m-d').'.json';

        if(file_exists($file_directory) && filesize($file_directory) > 0){
            $file = file_get_contents($file_directory);
            $data = json_decode($file, true);
                   
            $list_ids = array_column($data, 'id'); // Get All Ids stored in array            
            $auto_increment_id = max($list_ids) + 1; // Set unique id
            
            $data[] = [
                'id' => $auto_increment_id,
                'ip_address' => $this->logIP(),
                'time_stamp' => date('Y-m-d H:i:s', time()),                
                'http_user_agent' => $this->logUserAgent(),
                'inbound_request' => $this->httpRequest(),
                'title' => $report->title ?? null, 
                'description' => $report->description ?? null, 
            ];
            
            file_put_contents($file_directory,json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
        } else {
            $data[] = [
                'id' => 0,
                'ip_address' => $this->logIP(),
                'time_stamp' => date('Y-m-d H:i:s', time()),
                'http_user_agent' => $this->logUserAgent(),
                'inbound_request' => $this->httpRequest(),
                'title' => $report->title ?? null, 
                'description' => $report->description ?? null, 
            ];

            file_put_contents($file_directory, json_encode($data, JSON_PRETTY_PRINT), LOCK_EX);
        }
    }


}



