<?php
define('DEBUG',true);
require '../sys/SebbleDMS.php';
SebbleDMS::$controllerDir = dirname(__FILE__).'/../';
SebbleDMS::$controllerConfDir = dirname(__FILE__).'/../';
SebbleDMS::Controller('Admin');
exit;

