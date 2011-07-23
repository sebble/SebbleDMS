<?php


class Controller_Admin {

    static $template;
    
    function __construct() {
    
        if(isset($_SERVER['PATH_INFO'])) {
            
            // remove leading & trailing slash
            $url = trim($_SERVER['PATH_INFO'], '/');
            $url = explode('/', $url);
            
            // get class and method
            $class = 'Data_' . array_shift($url);
            $method = array_shift($url);
            
            // gather params (path_info, post, _files)
            $params = $url;
            if(count($_POST)>0 || count($_GET)>0) ## get, post, cookie [1]
                $params[] = array_merge($_POST,$_GET);
            if(count($_FILES)>0) ## files? really? [2]
                $params[] = $_FILES;
            
            $X = new $class;
            $result = call_user_func_array(array($X, $method), $params);
            
            echo json_encode($result);
            
        } else {
            echo file_get_contents(self::$template);
        }
    }
}
