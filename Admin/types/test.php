<?php

////////////////////////////////////////////////////////////////////////////////

function takes_only_array($data) {

    $data = fix_args_array(func_get_args(),array('par1','par2','par3'));

    print_r(get_defined_vars());
}

function fix_args_array($args, $expected) {

    if (is_array($args[0])) return $args[0];
    
    $ret = array();
    foreach ($args as $k=>$arg) {
        
        if (!isset($expected[$k])) return $ret;
        $ret[$expected[$k]] = $arg;
    }
    return $ret;
}

takes_only_array(array('par2'=>'val2','par1'=>'val1'));

takes_only_array('val1','val2');

#function call_array_func()

////////////////////////////////////////////////////////////////////////////////

function takes_real_params($par1, $par2=NULL, $par3=NULL) {

    fix_real_params(array(&$par1,&$par2,&$par3),array('par1','par2','par3'));
    
    print_r(get_defined_vars());
}

function fix_real_params($args, $expected) {

    if (!is_array($args[0])) return;
    
    $tmp = $args[0];
    foreach ($expected as $k=>$n) {
        
        if (isset($tmp[$n]))
            $args[$k] = $tmp[$n];
    }
    return;
}

takes_real_params(array('par2'=>'val2','par1'=>'val1'));

takes_real_params('val1','val2');

////////////////////////////////////////////////////////////////////////////////
