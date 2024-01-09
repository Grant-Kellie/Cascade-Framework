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

namespace CascadePHP\Database;
use PDO;
use CascadePHP\Config\Settings\Database;
use CascadePHP\Exceptions\ErrorResponse;

class DBLoader{

    public function run($database){          
        $settings = (new Database())->settings();
        if($database === key($settings->database)){
            $db = (object) $settings->database[$database];
           
            $driver = $db->driver;
            $host = $db->host;
            $charset = $db->charset ?? 'utf8';
            $port = $db->port ?? null;
            $user = $db->user;
            $pass = $db->password;

            $options = [
                \PDO::ATTR_ERRMODE            => \PDO::ERRMODE_EXCEPTION,
                \PDO::ATTR_DEFAULT_FETCH_MODE => \PDO::FETCH_ASSOC,
                \PDO::ATTR_EMULATE_PREPARES   => false,
            ];

            $dsn = "mysql:host=$host;dbname=$database;charset=$charset;port=$port";
            
            try {
                return $pdo = new \PDO($dsn, $user, $pass, $options);
            } catch (\PDOException $e) {
                throw new \PDOException($e->getMessage(), (int)$e->getCode());
            }
        } else {
            (new ErrorResponse)->custom_error('Database could not be found.', 404);
        }
    }

}