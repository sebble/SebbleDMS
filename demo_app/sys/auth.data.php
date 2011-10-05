<?php

class SebbleDMS_Data_Auth extends SebbleDMS_Data {

    static $filters = array(
            'login'     => 'public',
            'logout'    => 'public',
            'info'      => 'public'
        );
    
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
    
        return $this->Data($this->User);
    }
}
