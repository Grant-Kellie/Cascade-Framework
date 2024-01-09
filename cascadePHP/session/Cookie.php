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

namespace CascadePHP\Session;
class Cookie {




    /**
     * Cookie name
     * @var String
     */
    private $name;

    /**
     * Cookie value
     * @var String
     */
    private $value;

    /**
     * Cookie expirey time
     * @var DateTime
    */
    private $expire;

    /**
     * Cookie assigned route
     * @var String
     */
    private $path;

    /**
     * Cookie assigned (sub).domain
     * @var String
     */
    private $domain;

    /**
     * Cookie transmitted though Http(s)
     * @var Bool
     */
    private $secure = true;

    /**
     * Cookie accessible through Http(s)
     * @var Bool
     */
    private $httponly = true;


    public function create(){
        return setcookie($this->name, $this->getValue(), $this->getTime(), $this->getPath(), $this->getDomain(), $this->getSecure(), true);
    }




}