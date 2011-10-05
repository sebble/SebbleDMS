<?php

/** Check if environment is development and display errors **/

function setReporting() {
    if (defined('DEBUG')) {
	    error_reporting(E_ALL);
	    ini_set('display_errors','On');
    } else {
	    error_reporting(E_ALL);
	    ini_set('display_errors','Off');
	    ini_set('log_errors', 'On');
	    ini_set('error_log', $_SERVER['DOCUMENT_ROOT'].'/tmp/logs/error.log');
    }
}
setReporting();

/** Check for Magic Quotes and remove them **/

if (!defined('MQ_REMOVED')) {
    function stripSlashesDeep($value) {
	    $value = is_array($value) ? array_map('stripSlashesDeep', $value) : stripslashes($value);
	    return $value;
    }

    function removeMagicQuotes() {
        if ( get_magic_quotes_gpc() ) {
	        $_GET    = stripSlashesDeep($_GET   );
	        $_POST   = stripSlashesDeep($_POST  );
	        $_COOKIE = stripSlashesDeep($_COOKIE);
        }
    }
    removeMagicQuotes();
    define('MQ_REMOVED',true);
}

/** Check register globals and remove them **/

if (!defined('GLOBALS_UNREGISTERED')) {
    function unregisterGlobals() {
        if (ini_get('register_globals')) {
            $array = array('_SESSION', '_POST', '_GET', '_COOKIE', '_REQUEST', '_SERVER', '_ENV', '_FILES');
            foreach ($array as $value) {
                foreach ($GLOBALS[$value] as $key => $var) {
                    if ($var === $GLOBALS[$key]) {
                        unset($GLOBALS[$key]);
                    }
                }
            }
        }
    }
    unregisterGlobals();
    define('GLOBALS_UNREGISTERED',true);
}


/** Default Configuration -- clone below for custom config **/

SebbleDMS::$dataTypeDir = dirname(__FILE__).'/../types/';
SebbleDMS::$controllerDir = dirname(__FILE__).'/../controllers/';
SebbleDMS::$dataTypeConfDir = dirname(__FILE__).'/../types/';
SebbleDMS::$controllerConfDir = dirname(__FILE__).'/../controllers/';
SebbleDMS_User_Storage::$users = dirname(__FILE__).'../config/users.json';
SebbleDMS_User_Storage::$groups = dirname(__FILE__).'../config/groups.json';
