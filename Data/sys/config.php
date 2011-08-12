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
	    ini_set('error_log', ROOT.DS.'tmp'.DS.'logs'.DS.'error.log');
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


/** Autoload for Data types **/

function __autoload_data_types($classname) {

    $datatype = explode('_', $classname);
    if ($datatype[0]!='Data') return;
    $file = Data::$types . strtolower($datatype[1]) . '.data.php';
    if (file_exists($file)){
        require_once($file);
        $config = Data::$configs . strtolower($datatype[1]) . '.config.php';
        if (file_exists($config)) {
            include_once($config);
        }
    }
}
spl_autoload_register('__autoload_data_types');

function __autoload_controllers($classname) {

    $controller = explode('_', $classname);
    if ($controller[0]!='Controller') return;
    $file = Data::$controllers . strtolower($controller[1]) . '.controller.php';
    if (file_exists($file)){
        require_once($file);
    }
}
spl_autoload_register('__autoload_controllers');
## note that this is now defined _BEFORE_ the Data::$types directory is defined.

/** Require some system files **/

require('data.class.php');
require('user.class.php');


/** Default Configuration -- clone below for custom config **/

User::$timeout = 30;
User::$dir = dirname(__FILE__).'/../users/';
Data::$types = dirname(__FILE__).'/../types/';
Data::$configs = dirname(__FILE__).'/../configs/';
Data::$controllers = dirname(__FILE__).'/../controllers/';
