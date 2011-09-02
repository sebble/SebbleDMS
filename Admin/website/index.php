<?php

define('DEBUG',true);
require('../../Data/Data.php');
define('CONFIG_WEBSITE',true);
require('../config.php');

## execute controller
Data_Website::$website = 'main';
Data::Controller('Website');

