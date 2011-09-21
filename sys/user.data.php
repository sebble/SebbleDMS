<?php

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


class DMS_Data_User {

    
}
