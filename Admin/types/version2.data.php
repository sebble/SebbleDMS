<?php

/* DMS Data Type - version 2 */
/*
  Sample Data Type
*/

class DMS_Data_Sample extends DMS_Data {

    static $permissions = array(
        'Sample_User',
        'Sample_Admin',
        'Sample_Moderator'
    );
    static $parameters = array(
        'mod' => array('par1','par2')
    );
    
    // -- Controller accessible functions
    function ls($params) {
    
        if ($this->User->hasRoles(array('Sample_Admin','Sample_User'))) {
            return $this->Message('This was successful (as admin)..');
            return $this->Error("A specific error for the user.");
            return $this->Data($returnData);
        } else if ($this->User->hasRoles('Sample_User'))) {
            return $this->_lsUserFiles();
        }
    }
    
    function mod($params) {
    
        // list all things
    }
}

?><?php

class DMS {

    static function Authenticate() {}

    static function Controller() {
    
        $class = 'DMS_Controller_'.$controller;
        $X = new $class;
    }
}

?><?php

class DMS_Data {

    var $User;
    var $Response;
    static $permissions = array();
    static $parameters = array();
    
    // Constructor
    function __construct($User=false) {
        
        $this->resetResponse();
        
        if($User===false) {
            $this->User = DMS_User::singleton();
        } else {
            $this->User = $User;
        }
    }
    
    // Response Controls
    function Message($message, $code=200) {
        
        $this->Response['code'] = $code;
        $this->Response['message'] = $message;
    }
    
    function Error($message, $code=500) {
        
        $this->Response['code'] = $code;
        $this->Response['message'] = $message;
    }
    
    function Data($data, $message=false, $code=200) {
        
        $this->Response['data'] = $data;
        $this->Response['code'] = $code;
        if ($message)
            $this->Response['message'] = $message;
    }
    
    function resetResponse() {
    
        $this->Response = array(
            'code'    => 400,
            'message' => '',
            'data'    => null
        );
    }
    
    // Action Controls
    function callAction($action, $params) {
    
        $this->resetResponse();
        
        if (isset($params[0]) && isset($this->parameters[$action])) {
            foreach ($interface[$action] as $k=>$pName) {
                if (isset($params[$k])) {
                    $params[$pName] = $params[$k];
                    unset($params[$k]);
                } break;
            }
        }
        
        if ((int)method_exists($this, $action)) {
            return call_user_func(array($this, $action), $params);
        }
        $this->Error("Invalid action ($action).");
    }
}

?><?php

class DMS_User_Data {

    static $users = '../users/';
    static $groups = '../groups/';
    
    // User data access functions
    function checkUser($u) {
    
        return file_exists(DMS_User_Data::$users.$u.'.json');
    }
    function checkPassword($u, $p) {
    
        if ($c = $this->fetchUser($u)) {
            return $this->checkSalt($p,$c['details']['password']);
        }
        return false;
    }
    function fetchUser($u) {
    
        if ($c = $this->fetchUser($u)) {
            ## space here to authenticate again if desired
            $c['details']['password'] = 'YES';
            ## parse groups
            foreach ($c['groups'] as $g) {
                if ($gp = $this->_fetchGroup($g)) {
                    $c['options'] = array_merge($gp['options'], $c['options']);
                    $c['permissions'] = array_merge($gp['permissions'], $c['permissions']);
                }
            }
            return $c;
        }
        return false;
    }
    
    // Other functions
    function _fetchUser($u) {
        
        if (file_exists(DMS_User_Data::$users.$u.'.json')) {
            return json_decode(file_get_contents(DMS_User_Data::$users.$u.'.json'), true);
        }
        return false;
    }
    function _fetchGroup($g) {
        
        if (file_exists(DMS_User_Data::$groups.$g.'.json')) {
            return json_decode(file_get_contents(DMS_User_Data::$groups.$g.'.json'), true);
        }
        return false;
    }
    
    // Utility function
    function checkSalt($password, $salt_md5) {
    
        $salt_md5 = explode(':',$salt_md5);
        if (md5($salt_md5[0].$password) == $salt_md5[1]) {
            return true;
        }
        return false;
    }
}

?><?php

session_start();

class DMS_User extends DMS_User_Data {

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

?><?php

class DMS_Data_Auth extends DMS_Data {

    function login($data) {
    
        if ($this->User->_login($data['username'], $data['password'])) {
            return $this->Message("Logged in as ".$this->User->details['username']);
        } else {
            return $this->Error("Login failed :(");
        }
    }

    function logout() {
    
        $this->User->_logout();
        return $this->Message("Logged out");
        
    }
    
    function info() {
    
        return $this->User;
    }
}

?>
