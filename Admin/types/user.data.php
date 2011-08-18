<?php


class Data_User extends Data {
    
    // suggestion: use this to map url vars to named vars
    var $urlmap = array('info'=>array('user'));
    
    
    function ls() {
    
        $d = User::$dir;
        $f = scandir($d);
        
        $us = array();
        
        foreach ($f as $uf) {
        
            if ($uf=='.'||$uf=='..') continue;
            $us[] = substr($uf, 0, -5);
        }
        
        return $us;
    }
    function lsl() {
    
        if (!$this->user->hasRole('user_admin')) return false;
        
        $us = $this->ls();
        
        $long = array();
        foreach ($us as $u) {
            if (file_exists(User::$dir.$u.'.json')) {
                $data = json_decode(file_get_contents(User::$dir.$u.'.json'),true);
                unset($data['details']['password']);
                $long[$u] = $data['details'];
            }
        }
        
        return $long;
    }
    
    function info($data, $keeppw = false) {
    
        $u = (isset($data['user'])) ? $data['user'] : $this->user->details['username'];
        
        if ($this->user->details['username'] == $u ||
            $this->user->hasRole('user_admin')) {
        
            if (file_exists(User::$dir.$u.'.json')) {
                
                $data = json_decode(file_get_contents(User::$dir.$u.'.json'),true);
                if (!$keeppw)
                    $data['details']['password'] = 'YES';
                return $data;
            }
        }
        return false;
    }
    
    function setName($data) {
    
        $u = (isset($data['user'])) ? $data['user'] : $this->user->details['username'];
        $n = $data['fullname'];
        
        if ($old = $this->info(array('user'=>$u), true)) {
        
            $old['details']['fullname'] = $n;
            $this->_saveUser($u, $old);
            return $old;
            return true; ### Return new value or return true?
        }
        return false;
    }
    function setDetails($data) {
    
        $u = (isset($data['user'])) ? $data['user'] : $this->user->details['username'];
        
        if ($old = $this->info(array('user'=>$u), true)) {
        
            unset($data['username']);
            foreach ($data as $d=>$v) {
                $old['details'][$d] = $v;
            }
            
            $this->_saveUser($u, $old);
            return $old;
            return true; ### Return new value or return true?
        }
        return false;
    }
    function setPassword($data) {
    
        $u = (isset($data['user'])) ? $data['user'] : $this->user->details['username'];
        $o = $data['current_password'];
        $p = $data['new_password'];
        $q = $data['new_password2'];
        
        if ($old = $this->info(array('user'=>$u),true)) {
        
            // check current password
            if (!User::check_salt($o,$old['details']['password']))
                return array('notification'=>array('status'=>'error',
                    'msg'=>'Incorrect password.'));
            // check valid password
            if (strlen($p)<4)
                return array('notification'=>array('status'=>'error',
                    'msg'=>'New password too short.'));
            // check both match
            if ($p!=$q)
                return array('notification'=>array('status'=>'error',
                    'msg'=>'New passwords do not match.'));
            // salty pass
            $salt = substr(md5(time()), -6);
            $string = $salt.':'.md5($salt.$p);
            $old['details']['password'] = $string;
            $this->_saveUser($u, $old);
            return array('notification'=>array('status'=>'success',
                'msg'=>'Password changed.'));
            return $old;
            return true; ### Return new value or return true?
        }
        return false;
    }
    function setRole($data) { ### a blank checkbox will not submit it's name
    
        $u = (isset($data['user'])) ? $data['user'] : $this->user->details['username'];
        $r = $data['role'];
        $s = $data['status'];
        
        if ($this->user->hasRole('user_admin')) { ### note here users cannot modify own roles
        
            $old = $this->info(array('user'=>$u), true);
            if ($this->_false($s) && ($k = array_search($r, $old['roles'])))
                unset($old['roles'][$k]);
            else if ($this->_true($s) && !in_array($r, $old['roles']))
                $old['roles'][] = $r;
            $this->_saveUser($u, $old);
            return $old;
            return true; ### Return new value or return true?
        }
        return false;
    }
    function setPermission($data) {
    
        $u = (isset($data['user'])) ? $data['user'] : $this->user->details['username'];
        $p = $data['permission'];
        $s = $data['status'];
        
        if ($this->user->hasRole('user_admin')) { ### note here users cannot modify own roles
        
            $old = $this->info(array('user'=>$u), true);
            if ($this->_false($s) && ($k = array_search($p, $old['permissions']))!==false)
                unset($old['permissions'][$k]);
            else if ($this->_true($s) && !in_array($p, $old['permissions']))
                $old['permissions'][] = $p;
            $this->_saveUser($u, $old);
            return $old;
            return true; ### Return new value or return true?
        }
        return false;
    }
    
    function setOption($data) {
    
        $u = (isset($data['user'])) ? $data['user'] : $this->user->details['username'];
        $k = $data['option_name'];
        $v = $data['option_value'];
    }
    
    
    function _saveUser($user, $data) {
    
        if (file_exists(User::$dir.$user.'.json')) {
            file_put_contents(User::$dir.$user.'.json', json_encode($data));
            #$data = json_decode(file_get_contents(User::$dir.$user.'.json'),true);
            #if (!$keeppw)
            #    $data['details']['password'] = 'YES';
            return true;
        }
        return false;
    }
    function _editable($user) {
        
        
    }
    
    function _false($var) {
    
        if ($var=='false') return true;
        if ($var===false) return true;
        return false;
    }
    function _true($var) {
    
        if ($var=='true') return true;
        if ($var===true) return true;
        return false;
    }
    
    
    
    function userPages() {
    
        // use user's roles and permissions to determine pages..
        // how?  use page-role-group configs
        // as above, maybe above should be ls..?
        $userPages = array();
        
        $groups = scandir(self::$dir);
        foreach ($groups as $grp) {
            if ($grp=='.'||$grp=='..') continue;
            
            $files = scandir(self::$dir.$grp);
            if (file_exists(self::$dir.$grp.'/'.$grp.'.json')) {
                $js = json_decode(file_get_contents(self::$dir.$grp.'/'.$grp.'.json'), true);
                #if ($js===NULL) exit("Error: Invalid Page Config ($grp)");
                foreach ($js['pages'] as $k=>$pg) {
                    $has = true;
                    if (!$this->user->hasRoles($pg['roles'])) $has = false;
                    if (!$this->user->hasPermissions($pg['perms'])) $has = false;
                    if (!$has) unset($js['pages'][$k]);
                    else {
                        unset($js['pages'][$k]['roles']);
                        unset($js['pages'][$k]['perms']);
                    }
                }
                $userPages[$grp] = $js;
            }
        }
        return $userPages;
    }
    
    function hasPage($group, $page, $mustLogin = true) {
    
        if ($mustLogin && !$this->user->loggedIn) return false;
        
        $pages = $this->userPages();
        $this->pages = $pages; // dirty caching for fetchPage()
        
        if (isset($pages[$group]['pages'][$page])) return true;
        return false;
    }
    
    function fetchPage($group, $page) {
    
        if ($this->hasPage($group, $page)) {
            $file = self::$dir.$group.'/'.$page.'.html';
            if (file_exists($file)) {
                return array('html'=>file_get_contents($file),
                             'group'=>$this->pages[$group]['name'],
                             'title'=>$this->pages[$group]['pages'][$page]['name'],);
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
















