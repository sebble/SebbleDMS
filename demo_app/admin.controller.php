<?php

class Controller_Admin {

    static $response;
    
    function __construct() {
    
        if (count($_POST)>0) {
            // process control action
            
            $action = explode('_', $_POST['action']);
            $object = 'SebbleDMS_Data_'.$action[0];
            $filter = $action[1];
            $action = $action[2];
            $keys   = isset($_POST['keys']) ? $_POST['keys'] : array() ;
            $data   = $_POST['data'];
            if (!isset($_FILES)) $_FILES = array();
            
            echo "\$X = new $object;";
            echo "\$result = \$X->callAction($action, $filter, $keys, $data, $_FILES);";
            
        } else {
            // display control page
            
            $_SERVER['PATH_INFO'];
            
        }
    }
    
    
    static function Notify($status, $msg) {
        // this could be a class with languages and things...
        // this is now a header response
        header("X-Admin-Status: ".$status);
        header("X-Admin-Message: ".$msg);
        self::$response = array('notification'=>
            array('status'=>$status, 'msg'=>$msg));
    }
}


