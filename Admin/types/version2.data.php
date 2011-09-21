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


