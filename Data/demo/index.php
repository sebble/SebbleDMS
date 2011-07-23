<?php

/* Data sample controller */

if(isset($_SERVER['PATH_INFO'])) {

    require('./../Data.php');
    
    # move into other file..?
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

    /* Return demo template */
    echo file_get_contents('demo.html');
}

## [1] : using request instead of get/post will result in always passing the $data param as cookies are always set (as per user auth), this shouldn't be a problem as $data is the last in the param sequence and PHP allows overloading params. -- or not as it seems, where is my cookie? removed in PHP >= 5.3.0
## [2] : as files are probably only uploaded when $_FILES exists, it may seem slightly fruitless to include a copy of that array..
