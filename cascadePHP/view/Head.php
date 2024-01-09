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
use CascadePHP\Environment;
use CascadePHP\HttpServices\HttpRequest;
class Head {

    public $http_host;

    public function load($settings = null){
        $httpRequest = (New HttpRequest)->message();
        $environment = (New Environment)->settings();  
        $default = $environment->default;  
        $this->http_host = $httpRequest->http.$httpRequest->host;  
        
        (!empty($default->favicon) ? $this->favicon($default->favicon) : null);
        (!empty($default->title) ? $this->title($default->title, ($settings->title ?? null)) : null); 
        (!empty($settings->description) ? $this->description($settings->description) : null);
        (!empty($default->metatag) ? $this->metatag($default->metatag) : null);    
        (!empty($settings->metatag) ? $this->metatag($settings->metatag) : null);       
        (!empty($settings->openGraph) ? $this->openGraph($settings->openGraph) : null);
        (!empty($default->css) ? $this->css($default->css) : null);   
        (!empty($default->scripts) ? $this->js($default->scripts) : null);         
        (!empty($default->style) ? $this->style($default->style) : null); 
        
    }


    public function title(string $website, $page){
        echo '<title>'. $website . $page . '</title>';
    }

    public function description($paragraph = null){
        echo '<meta name="description" content="'.$paragraph.'">';
    }

    public function metatag(array $metatag){
        if(!empty($metatag)){
            foreach ($metatag as $tag) {
                echo $tag;
            }
        }
    }

    public function css(array $css){
        if(!empty($css)){
            foreach ($css as $file) {
                echo '<link rel="stylesheet" type="text/css" href="'.$this->http_host.'/'.$file.'"/>';
            }
        }
    }

    public function js(array $js){
        if(!empty($js)){
            foreach ($js as $file => $config){
                echo '<script type="text/javascript" src="'.$this->http_host.'/'.$file.'" '.$config.'></script>';
            }
        }
    }

    public function openGraph(array $openGraph){
        if(!empty($openGraph)){
            foreach ($openGraph as $type => $og) {
                echo '<meta property="og:'.$type.'" content="'.$og.'"/>';
            }
        }
    }

    public function favIcon(array $favicon){
        if(!empty($favicon)){
            foreach ($favicon as $id => $icon) {
                echo '<link rel="shortcut icon" href="'. $this->http_host.'/'.$icon.'"/>';
            }
        } else if(empty($favicon)) {
            echo '<link rel="shortcut icon" href="'. $this->http_host.'/public/templates/html_core/fav.ico"/>';
        } else {
            echo '<link rel="icon" href="data:;base64,iVBORw0KGgo=">';
        }
    }

    public function style($style){
        if(!empty($style)){
            echo '<style>';
            foreach ($style as $tag => $css) {
                echo "$tag{ $css }";
            }
            echo '</style>';
        }
    }

}