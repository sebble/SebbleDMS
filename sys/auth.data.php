<?php

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
    
        return $this->Data($this->User);
    }
}
