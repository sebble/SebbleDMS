<?php

## configure system
User::$dir = dirname(__FILE__).'/users/';
Data::$types = dirname(__FILE__).'/types/';
Data::$configs = dirname(__FILE__).'/configs/';
Data::$controllers = dirname(__FILE__).'/controllers/';

## configure controller and data
Data_Admin::$dir = dirname(__FILE__).'/pages/';
Controller_Admin::$template = dirname(__FILE__).'/admin.html';
