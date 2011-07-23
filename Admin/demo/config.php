<?php

#define('BASE','/var/www/N4/');

User::$dir = dirname(__FILE__).'/../../Data/users/';
Data_Admin::$dir = dirname(__FILE__).'/pages/';
Controller_Admin::$template = dirname(__FILE__).'/admin.html';
#Data_Gallery::$dir = BASE.'gallery/';
