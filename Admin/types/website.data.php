<?php


class Data_Website extends Data {

    static $dbLink;
    static private $dbAuth = array();
    
    static $optionTable  = 'options';
    static $pageTable    = 'pages';
    static $contentTable = 'content';
    
    static $website = false;
    
    static function _createTables() {
        
        Data_SitePage::_createTable();
        Data_SiteOption::_createTable();
        Data_SiteContent::_createTable();
    }
    
    function _connect() {
    
        self::_connectDB();
    }
    
    static function _connectDB() {
    
        $link = mysql_connect(self::$dbAuth[0], self::$dbAuth[1], self::$dbAuth[2]);
        if (!$link) {
            trigger_error("Could not connect DB", E_USER_ERROR);
        }
        $db = mysql_select_db(self::$dbAuth[3], $link);
        if (!$db) {
            trigger_error("Could not select DB" . mysql_error(), E_USER_ERROR);
        }
        self::$dbLink = $link;
    }
    
    static function setDB ($server, $user, $pass, $db) {
    // setting the variables this way should protect them from prying eyes..
        self::$dbAuth = array($server, $user, $pass, $db);
    }
}
















