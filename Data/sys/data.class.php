<?php

/**
 * SebbleData class
 * outline for data objects
 */

class Data {

    // Authenticated User
    var $user;
    
    // options
    static $types;
    static $configs;
    static $controllers;
    
    function __construct() {
    
        // authenticate user
        $this->user = new User;
        
        // connect to DB?
        if ((int)method_exists($this, '_connect')) {
            call_user_func(array($this,'_connect'));
        }
    }
    
    function _check_build() {
    
        // an important method to be utilised as desired
    }
    
    static function Controller($controller) {
    
        $class = 'Controller_'.$controller;
        $X = new $class;
    }
}

?>
