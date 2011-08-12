<?php

## configure system
User::$dir = dirname(__FILE__).'/users/';
Data::$types = dirname(__FILE__).'/types/';
Data::$configs = dirname(__FILE__).'/types/';
Data::$controllers = dirname(__FILE__).'/controllers/';

## configure data  --  ./types/sample.config.php
Data_Sample::$manager = 'Steve'; 

## configure controller
Controller_Sample::$html = file_get_contents('demo.html');
