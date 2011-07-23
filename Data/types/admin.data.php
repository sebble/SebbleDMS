<?php


class Data_Admin extends Data {

    
    static $dir; ## location of templates and such..
    
    
    function templateMain() {
    
        // fetch admin template
    }
    
    function templatePage($page) {
    
        // fetch cp-page template
    }
    
    function authLogin() {
    
        // wrapper for built in login?
    }
    
    function authLogout() {
    
        // wrapper for built in logout?
    }
    
    function userInfo() {
    
        // wrapper for built in logout?
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
















