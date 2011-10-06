<?php

session_start();

class SebbleDMS_User extends SebbleDMS_User_Storage {

    // singleton
    static $instance;
    
    // user details
    var $details;
    var $filters;
    
    var $loggedIn = false;
    var $domain;
    
    // options
    static $timeout = 30;
    
    static function singleton() {
    
        if (!isset(self::$instance)) {
            self::$instance = new SebbleDMS_User(true);
        }
        return self::$instance;
    }
    
    function __construct($session = false) {
    
        if ($session)
            $this->_authenticate();
    }
    
    function _authenticate() {
        
        // attempt to recover existing session
        if (isset($_SESSION['username']) &&
                $_SESSION['activity'] > time() - SebbleDMS_User::$timeout*60) {
            
            return $this->_refresh($_SESSION['username'], true);
        } else {
            $this->_logout();
            return false;
        }
    }
    
    function _login($u, $p, $session=true) {
    
        if ($this->checkPassword($u, $p)) {
        
            if ($session) {
                $_SESSION['username'] = $u;
                $_SESSION['logintime'] = time();
                $_SESSION['activity'] = $_SESSION['logintime'];
                $_SESSION['domain'] = $session;
            }
            
            return $this->_refresh($u, $session);
        } else {
            ## Wrong u/n or p/w
            $this->_logout();
            return false;
        }
    }
    
    function _refresh($u, $session=true) {
    
        if ($c = $this->fetchUser($u)) {
            if ($session) {
                $_SESSION['activity'] = time();
                $this->domain = $_SESSION['domain'];
            }
            $this->details = $c;
            unset($this->details['password']);
            $this->filters = $c['filters'];
            $this->loggedIn = true;
            return true;
        }
        return false;
    }
    
    function _logout() {
    
        unset($_SESSION['username']);
        unset($_SESSION['activity']);
        unset($_SESSION['logintime']);
        unset($_SESSION['domain']);
        $this->details = array();
        $this->filters = array();
        
        $this->loggedIn = false;
        return true;
    }
    
    // Data Functions
    function hasFilter($object, $filter) {
    
        if (!isset($this->filters[$object])) {
            return false;
        }
        if ($this->filters[$object] == $filter) return true;
        else if (is_array($this->filters[$object])) {
            if (in_array($filter, $this->filters[$object])) return true;
        }
        return false;
    }
}
