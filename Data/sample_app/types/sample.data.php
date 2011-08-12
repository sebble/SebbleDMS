<?php

class Data_Sample extends Data {

    // some values to set in the config file
    // Data_Sample::$db_name = 'sampledb';
    static $manager;
    // this will be included in a JSON response -- wrong again
    
    // easy access to a persistent DB connection?
    private static $link;
    // this will not be included in JSON response
    
    function _connect() {
        // use this function instead of __construct
    
        // this is possibly a DB connect, or maybe an OAuth session
        // no need to include this function if there is nothing to connect to
        // still working on a sensible config file naming convention
        // will include the config load in the autoload fn
    }

    function showBoobies() {
    
        if ($this->user->getOpt('age') >= 18) {
            return array('message'=>'Hooray for boobies!');
        } else {
            return array('message'=>'Try this instead: www.lego.com');
        }
    }

    function makeCake() {
    
        if ($this->user->hasRole('sample_baker')) {
            if ($this->user->hasPermission('sample_access_cocoa')) {
                return array('cake'=>'An extra special chocolate cake!');
            } else {
                return array('cake'=>'A lovely Victoria sponge cake.');
            }
        } else {
            return array('error'=>'You are not a baker. Call '.self::$manager.'.');
        }
    }
    
    function parrot($string) {
    
        return func_get_args();
    }
}
