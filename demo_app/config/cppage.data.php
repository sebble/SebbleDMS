<?php

class DMSData_CpPage {

    function action($action, $filter, $keys, $data) {
    
        $filter = preg_replace('#[^a-z]+#i', '', $filter);
        if ($filter != '') $filter = $filter.'_';
        $action = preg_replace('#[^a-z]+#i', '', $action);
        $by = array_map(function($i){
            return ucfirst($i);
        }, array_keys($keys));
        $by = implode('',$by);
        if ($by != '') $by = 'By'.$by;
        
        $method = $filter.$action.$by;
        // also check perms
        
        if (!method_exists($this, $method)) {
            echo "Bad method '$method'";
        } else {
            $data = $this->clean($data);
            $keys = $this->clean($keys);
            $params = ($by=='') ? array($data) : array($keys, $data) ;
            $this->connect();
            call_user_func_array(array($this, $method), $params);
            $this->close();
        }
        
        //echo $filter.$action.$by;
        
    }
    
    // ---- DMS
    //   -- Domain
    function insertDomain($data) {
    
        $this->pages[$data['domain']] = array('default'=>$data['slug'],
                                              'requires'=>$data['requires'],
                                              'groups'=>array());
        print_r($this->pages);
    }
    function deleteByDomain($keys) {
    
        unset($this->pages[$keys['domain']]);
    }
    function setHomepageByDomainGroupPage($keys, $data) {
    
        $this->pages[$keys['domain']]['default'] = $keys['group'].'.'.$keys['page'];
    }
    //   -- Group
    function insertGroupByDomain($keys, $data) {
    
        $this->pages[$keys['domain']]['groups'][$data['slug']]
                                      = array('title'=>$data['title'],
                                              'requires'=>$data['requires'],
                                              'pages'=>array());
    }
    function deleteByDomainGroup($keys) {
    
        unset($this->pages[$keys['domain']]['groups'][$keys['group']]);
    }
    
    //   -- Page
    function insertPageByDomainGroup($keys, $data) {
    
        $this->pages[$keys['domain']]['groups'][$keys['group']]['pages'][$data['slug']]
                                      = array('title'=>$data['title'],
                                              'requires'=>$data['requires']);
    }
    function deleteByDomainGroupPage($keys) {
    
        unset($this->pages[$keys['domain']]['groups'][$keys['group']]['pages'][$keys['page']]);
    }
    
    //   -- All
    function update($data) {
    
        //print_r($data);
        foreach ($data as $update => $value) {
            $update = explode('_', $update);
            switch (count($update)) {
                case 2:
                    $this->pages[$update[0]]['requires'] = $value;
                    break;
                case 3:
                    $this->pages[$update[0]]['groups'][$update[1]][$update[2]] = $value;
                    break;
                case 4:
                    $this->pages[$update[0]]['groups'][$update[1]]['pages'][$update[2]][$update[3]] = $value;
                    break;
            }
        }
    }
    
    // ---- I/O
    function clean($data) {
    
        if (isset($data['domain']))
            $data['domain'] = strtoupper(preg_replace('#[^a-z]+#i','',$data['domain']));
        
        return $data;
    }
    function connect() {
        
        $this->loadPages();
    }
    function close() {
        
        $this->savePages();
    }
    
    // ---- Internals
    var $pages;
    
    function loadPages() {
    
        $this->pages = json_decode(file_get_contents('pages.json'),true);
    }
    
    function savePages() {
    
        file_put_contents('pages.json',json_encode($this->pages));
    }
    
    // ---- Inherited
    static function Notify($status, $msg) {
        // this could be a class with languages and things...
        // this is now a header response
        header("X-Admin-Status: ".$status);
        header("X-Admin-Message: ".$msg);
        self::$response = array('notification'=>
            array('status'=>$status, 'msg'=>$msg));
    }
    
}
