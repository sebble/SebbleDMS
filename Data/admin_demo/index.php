<?php

define('DEBUG',true);
require('./../Data.php');

## configure controller
Controller_Admin::$template = file_get_contents('demo.html');

## execute controller
Data::Controller('Admin');

