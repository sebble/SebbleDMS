<?php

class SebbleDMS_Data {

    var $User;
    var $Response;
    static $keyOrder = array();
    static $defaultFilter = 'public';
    static $object;
    static $filters;
    
    // Constructor
    function __construct($User=false) {
        
        $this->resetResponse();
        
        if($User===false) {
            $this->User = DMS_User::singleton();
        } else {
            $this->User = $User;
        }
        
        $object = explode('_', get_class($this));
        self::$object = $object[2];
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
    
    // User Authentication
    function verifyAction(&$action, &$filter) {
    
        // action
        $action = preg_replace('#[^a-z0-9]+#i', '', $action);
        
        // set filter
        $filter = preg_replace('#[^a-z0-9]+#i', '', $filter);
        if ($filter == '') $filter = self::defaultFilter;
        
        // get object name
        $object = explode('_', get_class($this));
        $object = strtolower($object[2]);
        
        // check user perm for this object and filter
        if (!$this->User->hasFilter($object, $filter)) {
            $this->Error("Invalid filter '$filter' for object '$object'");
            return false;
        }
        
        // check action has this filter
        if (!$this->hasFilter($action, $filter)) {
            $this->Error("Invalid filter '$filter' for action '$action'");
            return false;
        }
        
        return true;
    }
    
    static function hasFilter($action, $filter) {
    
        if (!isset(self::$filters[$action])) {
            $this->Error("No filters defined for this action '$action'");
            return false;
        }
        if (self::$filters[$action] == $filter) return true;
        else if (is_array(self::$filters[$action])) {
            if (in_array($filter, self::$filters[$action])) return true;
        }
        return false;
    }
    
    // Action Controls
    function callAction($action, $filter='', $keys=array(), 
                        $data=array(), $files=array()) {
    
        $this->resetResponse();
        
        // verify action & permission
        if (!self::verifyAction($action, $filter)) {
            $this->Error("Invalid filter '$filter' for '$action'");
        }
        
        // unnamed id key
        if (isset($keys[0]) && count(self::keyOrder)>0) {
            // first element of keyOrder is used as id key
            $keys[self]::keyOrder[0]] = $value;
            unset($keys[0]);
        }
        
        // reorder keys
        if (count(self::keyOrder)>0) {
            $keys_sorted = array();
            foreach(self::keyOrder as $key) {
                if (isset($keys[$key])) $keys_sorted[] = $key;
            }
        } else {
            $keys_sorted = array_keys($keys);
        }
        
        // form 'by' string
        $by = array_map(function($i){
            return ucfirst($i);
        }, $keys_sorted);
        $by = implode('',$by);
        if ($by != '') $by = 'By'.$by;
        
        // has files?
        if (count($files)>0) $file = 'WithFiles';
        else $file = '';
        
        // form method string
        $method = $filter.'_'.$action.$by.$file;
        
        if (!method_exists($this, $method)) {
            $class = explode('_', get_class($this));
            $this->Error("Invalid action '$method' for object '{$class[2]}'");
        } else {
            $this->prepare($data, $keys);
            $this->connect();
            $params = ($by=='') ? array($data, $files) : array($keys, $data, $files) ;
            call_user_func_array(array($this, $method), $params);
            $this->close();
        }
    }
    
    // Data Validation
    function prepare(&$data, &$keys) {
    
        // -- clean or modify data and keys before passing to action method
    }
    function connect() {
    
        // -- create a persistent connection for data processing duration
    }
    function close() {
    
        // -- automatically save data after manipulations
    }
}
