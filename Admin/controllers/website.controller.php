<?php

//////////                       
// Standard website controller   
/////                            

class Controller_Website {

    static $allowExplicit = false;
    static $requestMethod = 'path_info';
    
    static $subdir = false;
    static $index = 'index.php';
    
    var $url = '/';
    var $method = 'unknown';
    
    function __construct() {
    
        $this->detectMethod();
        $this->detectUrl();
        Data_Website::_connectDB();
        
        // Do action or show page..
        $this->doAction();
        $this->showPage($this->url);
    }
    
    ///// Initiation methods
    
    function detectMethod() {
    
        $url = parse_url($_SERVER['REQUEST_URI']);
        $this->method = 'unknown';
        if (isset($_SERVER['REDIRECT_STATUS']) && $_SERVER['REDIRECT_STATUS'] == 404)
            $this->method = '404';
        else
        if (isset($_SERVER['PATH_INFO']))
            $this->method = 'path_info';
        else
        if ($url['path'] == '/'.Controller_Website::$index)
            $this->method = 'explicit';
        else
        if ($url['path'] == '/')
            $this->method = 'implicit';
        else
            $this->method = 'mod_rewrite';
    }
    
    function detectUrl() {
    
        switch ($this->method) {
            case 'path_info':
                $this->url = $_SERVER['PATH_INFO'];
                break;
            case 'explicit':
                $this->url = '/';
                break;
            case '404':
                // some IIS specific code here
            case 'implicit':
            case 'mod_rewrite':
            default:
                $url = parse_url($_SERVER['REQUEST_URI']);
                $this->url = $url['path'];
                break;
        }
    }
    
    /*function connectDB() {
    
        $link = mysql_connect(self::$dbAuth[0], self::$dbAuth[1], self::$dbAuth[2]);
        if (!$link) {
            trigger_error("Could not connect DB", E_USER_ERROR);
        }
        $db = mysql_select_db(self::$dbAuth[3], $link);
        if (!$db) {
            trigger_error("Could not select DB" . mysql_error(), E_USER_ERROR);
        }
        self::$dbLink = &$link;
        Data_Website::$dbLink = &$link;
    }*/
    
    ///// Action methods
    
    function doAction() {
    
        ## detect action (_POST or _GET var)
        ## execute action
    }
    
    function showPage ($url) {
    
        $P = new Data_Page;
        $P->showPage($url);
    }
    
    ///// Utility methods
    
    // Is this going to be needed?
    static function buildUrl ($url) {
      
        if (Controller_Website::$request_method == 'path_info')
            $url = '/index.php' . $url;
        if (Controller_Website::$subdir)
            $url = Controller_Website::$subdir . $url;
        return $url;
    }
    
    /*static function setDB ($server, $user, $pass, $db) {
    // setting the variables this way should protect them from prying eyes..
        self::$dbauth = array($server, $user, $pass, $db);
    }*/
}

/* Notes */
/*

MySQL Databases

## dms_pages
$sql = "CREATE TABLE `footloose`.`dms_pages` (`id` INT NOT NULL AUTO_INCREMENT, `site` TEXT NOT NULL, `slug` TEXT NOT NULL, `title` TEXT NOT NULL, `description` TEXT NULL, `keywords` TEXT NULL, `template` TEXT NOT NULL, `layout` TEXT NOT NULL, `active` INT NOT NULL, `regex` TEXT NOT NULL, PRIMARY KEY (`id`)) ENGINE = MyISAM;";

*/
