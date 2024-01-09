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
class Html {

    public function doctype($doctype){
        if(empty($doctype)){
            echo '<!DOCTYPE html>';
        } if($doctype === 'html_5'){
            echo '<!DOCTYPE html>';
        } if($doctype === 'html_4'){
            echo '<!DOCTYPE HTML PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">';
        } if($doctype === 'xhtml'){
            echo '<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.1//EN" "http://www.w3.org/TR/xhtml11/DTD/xhtml11.dtd">';
        } else {
            echo '<!DOCTYPE html>';
        }
    }

    public function lang($lang){
        if(!empty($lang)){
            echo '<html lang='.$lang.'>' ?? '<html lang="en">';
        } else {
            echo '<html lang="en">';
        }
    }

}