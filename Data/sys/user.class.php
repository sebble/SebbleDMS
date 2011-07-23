<?php

session_start();

class User {
    
    // user details
    var $details;
    var $options;
    var $permissions;
    var $loggedIn = false;
    
    // options
    static $timeout = 30;
    static $dir;
    
    function __construct() {
    
        $this->_authenticate();
    }
    
    function _authenticate() {
        
        // attempt to recover existing session
        if (isset($_SESSION['username'])&&$_SESSION['activity']>time()-User::$timeout*60) {
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
    
    function _login($u, $p) {
    
        if (file_exists(User::$dir.$u.'.json')) {
            $c = json_decode(file_get_contents(User::$dir.$u.'.json'), true);
            if (User::check_salt($p,$c['details']['password'])) {
            
                $_SESSION['username'] = $u;
                $_SESSION['logintime'] = time();
                $_SESSION['activity'] = $_SESSION['logintime'];
                #echo "Logged In";
                $this->loggedIn = true;
                return $this->_load($u);
            } else {
                #echo "Wrong PW";
                $this->_logout();
                return false;
            }
        } else {
            #echo "No user";
            $this->_logout();
            return false;
        }
    }
    
    function _load($u) { // also used to refresh session
    
        if (file_exists(User::$dir.$u.'.json')) {
            $_SESSION['activity'] = time();
            $c = json_decode(file_get_contents(User::$dir.$u.'.json'), true);
            $this->details = $c['details'];
            $this->details['password'] = 'YES';
            $this->options = $c['options'];
            $this->permissions = $c['permissions'];
            $this->roles = $c['roles'];
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
    
    
    
    /* Data Functions */
    
    function hasRole($role) {
        
        if (in_array($role, (array)$this->roles)) return true;
        return false;
    }
    
    function hasPermission($perm) {
        
        if (in_array($perm, (array)$this->permissions)) return true;
        return false;
    }
    
    function hasRoles($roles) {
        
        $has = true;
        foreach ($roles as $r) {
            if (!$this->hasRole($r)) $has = false;
        }
        return $has;
    }
    
    function hasPermissions($perms) {
        
        $has = true;
        foreach ($perms as $p) {
            if (!$this->hasPermission($p)) $has = false;
        }
        return $has;
    }
    
    function getOpt($opt) {
        
        if(isset($this->options[$opt])) {
            return $this->options[$opt];
        }
        return null;
    }
    
    
    
    /* Utility funtions */
    
    static function check_salt($password, $salt_md5) {
    
        $salt_md5 = explode(':',$salt_md5);
        if (md5($salt_md5[0].$password) == $salt_md5[1]) {
            return true;
        }
        return false;
    }
}

/** A class to enable similar access to the authentication functions **/
class Data_Auth extends User {

    function login ($data) {
    
        return $this->_login($data['username'], $data['password']);
    }

    function logout () {
    
        return $this->_logout();
    }
    
    function info () {
    
        return $this;
    }
}


?>
