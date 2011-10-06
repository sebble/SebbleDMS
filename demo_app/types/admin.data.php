<?php

class SebbleDMS_Data_Admin extends SebbleDMS_Data {

    static $pages;
    static $config;
    
    static $AdminPage;

    static $filters = array(
            'signIn'     => 'public',
            'signOut'     => 'public'
        );
    
    
    // DMS actions
    function signIn($data) {
    
        if ($this->User->_login($data['username'], $data['password'], $data['domain'])) {
            return $this->Message("Logged in as ".$this->User->details['username']);
        } else {
            return $this->Error("Login failed :(");
        }
    }
    function signOut() {
    
        $this->User->_logout();
        $this->Message("Logged out");
    }
    
    // internal functions
    function lsDomains() {
        
        $d = array();
        $files = scandir('/var/www/repos/SebbleDMS/demo_app/admin/pages/');
        foreach ($files as $f) {
            if (preg_match('#^[A-Z]+$#',$f)) $d[] = $f;
        }
        return $d;
    }
}
