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

use RecursiveIteratorIterator;
use RecursiveDirectoryIterator;
use FilesystemIterator;
use Cascade;
use CascadePHP\Exceptions\ErrorResponse;

/**
 * Autoload classes hierarchically
 * @author Grant Kellie | contact@cascadephp.com
 */
class Autoload_Cascade extends Cascade {


    /**
     * Holds verified autoloader directories from environment
     * @var Array
     */
    private $directories;

    /**
     * stores the values for classes discovered
     * @var Array
     */
    private $classmap = [];


    /**
     * Holds the usable class values
     * @var Array
     */
    private $classes = [];

    /**
     * Constructor
     */
    public function __construct(){
        $this->directories = $this->env('autoload');
        $this->classes = $this->loadClassmap();
    }
    

    /**
     * Traverses the directory structre of a given path set within the Environment file.
     * @return FileDirectory
     */
    public function directoryIterator($directory = null){
        $directoryIterate = new RecursiveDirectoryIterator($directory, RecursiveDirectoryIterator::SKIP_DOTS);
        return $iterate = new RecursiveIteratorIterator($directoryIterate);       
    }


    /**
     * Initialize prompts a Hierarchical searhes of pre-defined paths or loads
     * stored classes from a supplied file.
     */    
    public function invoke(){          
        if(empty($this->directories)){
            $message = ['Server environment file misconfigured' => 'Please add directories to the file Environment.php for the autoloader to discover you files and classes.'];
            (new ErrorResponse)->custom_error($message, 500);
        } else {
            (!empty($this->classes) ? spl_autoload_register('CascadePHP\Autoload_Cascade::registerClasses', true, true) : $this->map());
        }                   
    }


    /**
     * Validates the Enviromnet file has been configured.
     * Build class map then attempt to load classes required for use in an application
     * @return Autoload
     */
    public function map(){  
        if(empty($this->directories)){            
            $message = ['Server environment file misconfigured' => 'Please add directories to the file Environment.php for the autoloader to navigate you files and classes.'];
            (new ErrorResponse)->custom_error($message, 500);
        } else {           
            $this->compileClassmap();           
        }
    }


    /**
     * Reads and Sends namespaces, classes and file directory
     * to be sent to registerClasses()
     * 
     * @return File: class_map.json
     */  
    public function loadClassmap(){  
        $file = @file_get_contents(dirname(__DIR__, 1).'/config/mapping/classes.json', true);  
        if(!empty($file)) return json_decode($file);    
    }


    /**
     * This is the main autoloader method
     * Register Class and include file if readable
     * @return Class
     * @throws 
     */
    public function registerClasses($class = null){
        (empty($this->classes) ? $this->map() : true);         
        foreach ($this->directories as $directory){
            foreach ($this->classes as $mapped){      
                if($directory === $mapped->directory && $class === $mapped->namespace_class && !class_exists($mapped->namespace_class)){
                    if(file_exists($mapped->file_path)){  
                        include_once $mapped->file_path;                              
                    }
                } if(!file_exists($mapped->file_path)){
                    $this->map();                      
                } 
            }
        }  
    }


    /**
     * Discovers classes to build map of classes and files.
     * Saves the discovered classes to json file 
     * @return File class_map.json
     */  
    public function compileClassmap(){      
        foreach ($this->directories as $directory){
            if (is_dir($directory)){
                $iterate = $this->directoryIterator($directory);
                $iterate->rewind();
                
                while($iterate->valid()) {
                    if ($iterate->getExtension() === 'php'){  
                        if (is_file($iterate->key())){ 
                            $settings = $this->config($directory, $iterate);                           
                            array_push($this->classmap, $settings);                                                
                        }
                    } $iterate->next();                                        
                }  
            }
        }     
        $encoded = json_encode($this->classmap, JSON_PRETTY_PRINT);

        file_put_contents(dirname(__DIR__, 1).'/config/mapping/classes.json', $encoded);
        return spl_autoload_register('CascadePHP\Autoload_Cascade::registerClasses', true, true);        
    }

    
    /**
     * Builds the namespace of class by detecting
     * the filePath and stripping unneeded values.
     * @param String $filepath
     * @return String $namespace
     */  
    public function buildNamespace($filePath){
        $namespace = preg_replace('/.'.'php'.'/', '', $filePath);
        $namespace = ucwords($namespace, DIRECTORY_SEPARATOR);
        $namespace = preg_replace('/\W\w+\s*(\W*)$/', '$1', $namespace);
        return preg_replace('~(?=[\/]).~','\\', $namespace);
    }


    /**
     * Builds the class settings used to locate classes and files. 
     * @param String $directory | autoload directory
     * @param Function $iterate | RecursiveIteratorIterator
     * @return Array $settings
     */
    public function config($directory, $iterate){
        $settings = [
            "directory" => $directory,
            'file' => $iterate->getFilename(),          
            'file_path' => $iterate->getPath().'/'.$iterate->getFilename(),
            'file_size_bits' => $iterate->getSize() * 8,
        ];

        $class = ucwords(preg_replace('/.[^.]*$/','', $settings['file']));
        $namespace = $this->buildNamespace($settings['file_path']);

        $settings += [
            'namespace_class' => $namespace.'\\'.$class,
            'namespace' => $namespace,
            'class' => $class,
        ];
        return $settings;
    }
}

// Included prior to autoloader full load to allow for Error handling
include_once dirname(__DIR__).'/exceptions/ErrorResponse.php';