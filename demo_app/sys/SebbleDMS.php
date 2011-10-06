<?php

class SebbleDMS {

    static $dataTypeDir;
    static $controllerDir;
    static $dataTypeConfDir;
    static $controllerConfDir;
    
    static function Controller ($controller) {
    
        $class = 'SebbleDMS_Controller_'.$controller;
        $X = new $class;
    }
}

// PHP Autoload Class Functions
function __autoload_data_types($classname) {

    $datatype = explode('_', $classname);
    if ($datatype[1]!='Data') return;
    $file = SebbleDMS::$dataTypeDir . strtolower($datatype[2]) . '.data.php';
    if (file_exists($file)){
        require_once($file);
        $config = SebbleDMS::$dataTypeConfDir . strtolower($datatype[2]) . '.config.php';
        if (file_exists($config)) {
            include_once($config);
        }
    }
}
function __autoload_controllers($classname) {

    $controller = explode('_', $classname);
    if ($controller[1]!='Controller') return;
    $file = SebbleDMS::$controllerDir . strtolower($controller[2]) . '.controller.php';
    if (file_exists($file)){
        require_once($file);
        $config = SebbleDMS::$controllerConfDir . strtolower($controller[2]) . '.config.php';
        if (file_exists($config)) {
            include_once($config);
        }
    }
}
spl_autoload_register('__autoload_data_types');
spl_autoload_register('__autoload_controllers');

// require system files
require 'data.class.php';
require 'user.data.php';
require 'user.class.php';
require 'auth.data.php';

// load default configs
require 'config.php';
