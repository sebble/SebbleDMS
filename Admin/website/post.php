<?php

define('DEBUG',true);
require('../../Data/Data.php');
define('CONFIG_WEBSITE_POST',true);
require('../config.php');

## execute controller
Data_Website::$website = 'post';
Data::Controller('Website');

