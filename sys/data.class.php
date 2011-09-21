<?php

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
