<?php

class SebbleDMS_User_Storage {

    static $users = '../config/users.json';
    static $groups = '../config/groups.json';
    
    // User data access functions
    function checkPassword($u, $p) {
    
        if ($c = $this->_fetchUser($u)) {
            return $this->checkSalt($p,$c['details']['password']);
        }
        return false;
    }
    function fetchUser($u) {
    
        if ($c = $this->_fetchUser($u)) {
            unset($c['details']['password']);
            return $c;
        }
        return false;
    }
    
    // Other functions
    function _fetchUser($u) {
        
        if (file_exists(DMS_User_Data::$users)) {
            $users = json_decode(file_get_contents(DMS_User_Data::$users), true);
            // check user
            if (!isset($users[$u])) return false;
            $users[$u]['username'] = $u;
            // merge groups
            if ($groups = $this->_fetchGroups()) {
                foreach ($users[$u]['groups'] as $gp) {
                    if (isset($groups[$gp])) {
                        
                    }
                }
            }
            return $users[$u];
        }
        return false;
    }
    function _fetchGroups() {
        
        if (file_exists(DMS_User_Data::$groups)) {
            return json_decode(file_get_contents(DMS_User_Data::$groups), true);
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


class SebbleDMS_Data_User {

    
}
