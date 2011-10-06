<?php

class SebbleDMS_Controller_Admin {

    static $response;
    
    function __construct() {
    
        
        if (count($_POST)>0) {
            // process control action
            
            $action = explode('_', $_POST['action']);
            $object = 'SebbleDMS_Data_'.$action[0];
            $filter = $action[1];
            $action = $action[2];
            $keys   = isset($_POST['keys']) ? $_POST['keys'] : array() ;
            $data   = isset($_POST['data']) ? $_POST['data'] : array() ;
            if (!isset($_FILES)) $_FILES = array();
            
            $X = new $object;
            $result = $X->callAction($action, $filter, $keys, $data, $_FILES);
            
            header("X-Admin-Status: {$X->Response['code']}");
            header("X-Admin-Message: {$X->Response['message']}");
            echo json_encode($X->Response['data']);
            
        } else if (isset($_SERVER['PATH_INFO'])) {
            // display control page
            
            $keys = array('slug'=>substr($_SERVER['PATH_INFO'], 1));
            
            $A = new SebbleDMS_Data_AdminPage;
            $A->filter = 'public';
            $A->getPageBySlug($keys);
            
            header("X-Admin-Status", $A->Response['code']);
            header("X-Admin-Message", $A->Response['message']);
            echo $A->Response['data'];
            
        } else {
            // display admin template
            
            echo file_get_contents('admin.html');
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


