<?php


if(isset($_SERVER['PATH_INFO'])) {
    /* Data sample controller */
    define('DEBUG',true);
    require('./../Data.php');
    Data::Controller('Admin');
    
} else {
    /* Return demo template */
    echo file_get_contents('demo.html');
}
