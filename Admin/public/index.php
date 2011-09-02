<?php

define('DEBUG',true);
require('./../../Data/Data.php');
define('CONFIG_ADMIN',true);
require('./../config.php');

## execute controller
Data::Controller('Admin');

