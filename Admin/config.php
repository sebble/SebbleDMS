<?php

## configure system
User::$dir = dirname(__FILE__).'/users/';
Data::$types = dirname(__FILE__).'/types/';
Data::$configs = dirname(__FILE__).'/configs/';
Data::$controllers = dirname(__FILE__).'/controllers/';

if (defined('CONFIG_ADMIN'))
{
    Data_Admin::$dir = dirname(__FILE__).'/pages/';
    Controller_Admin::$template = dirname(__FILE__).'/admin.html';
    Data_Website::setDB('localhost','root','password','sebbledms');
}
else if (defined('CONFIG_WEBSITE'))
{
    Controller_Website::$allow_explicit = false;
    Controller_Website::$subdir = '/repos/SebbleDMS/Admin/website';
    Controller_Website::$request_method = '404';

    //if (!defined('LOAD_CONFIG'))
    //    trigger_error('Unauthorized access to config.', E_USER_WARNING);
    
    Data_Website::setDB('localhost','root','password','sebbledms');
}
else if (defined('CONFIG_WEBSITE_POST'))
{
    Controller_Website::$allow_explicit = false;
    Controller_Website::$subdir = '/repos/SebbleDMS/Admin/website';
    Controller_Website::$request_method = 'path_info';
    
    Data_Website::setDB('localhost','root','password','sebbledms');
}
