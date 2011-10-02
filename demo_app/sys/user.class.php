<?php

session_start();

class SebbleDMS_User extends SebbleDMS_User_Data {

    // singleton
    static $instance;
    
    // user details
    var $details;
    var $options;
    var $permissions;
    
    var $loggedIn = false;
    
    // options
    static $timeout = 30;
    
    static function singleton() {
    
        if (!isset(self::$instance)) {
            self::$instance = new User(true);
        }
        return self::$instance;
    }
    
    function __construct($session = false) {
    
        if ($session)
            $this->_authenticate();
    }
    
    function _authenticate() {
        
        // attempt to recover existing session
        if (isset($_SESSION['username'])&&$_SESSION['activity']>time()-DMS_User::$timeout*60) {
            if ($this->_load($_SESSION['username'])) {
                $this->loggedIn = true;
                return true;
            }
            return false;
        } else {
            $this->_logout();
            return false;
        }
    }
    
    function _login($u, $p, $session=true) {
    
        if ($this->checkUser($u)) {
            if ($this->checkPassword($u, $p)) {
            
                if ($session) {
                    $_SESSION['username'] = $u;
                    $_SESSION['logintime'] = time();
                    $_SESSION['activity'] = $_SESSION['logintime'];
                }
                $this->loggedIn = true;
                return $this->_load($u, $session);
            } else {
                ## Wrong p/w
                $this->_logout();
                return false;
            }
        } else {
            ## No User
            $this->_logout();
            return false;
        }
    }
    
    function _load($u, $session=true) { // also used to refresh session
    
        if ($this->checkUser($u)) {
            if ($session)
                $_SESSION['activity'] = time();
            $c = $this->fetchUser($u);
            $this->details = $c['details'];
            $this->details['password'] = 'YES';
            $this->options = $c['options'];
            $this->permissions = $c['permissions'];
            return true;
        } else {
            return false;
        }
    }
    
    function _logout() {
    
        unset($_SESSION['username']);
        unset($_SESSION['activity']);
        unset($_SESSION['logintime']);
        $this->details = NULL;
        $this->options = NULL;
        $this->permissions = NULL;
        $this->roles = NULL;
        
        $this->loggedIn = false;
        return true;
    }
    
    // Data Functions
    function hasPermission($perm) {
        
        if (is_array($perm)) return $this->hasPermissions($perm);
        if (in_array($perm, (array)$this->permissions)) return true;
        return false;
    }
    function hasPermissions($perms) {
        
        if (!is_array($perms)) return $this->hasPermission($perms);
        $has = true;
        foreach ($perms as $p) {
            if (!$this->hasPermission($p)) $has = false;
        }
        return $has;
    }
    function hasPerm($perms) {
        
        return $this->hasPermission($perms);
    }
}
