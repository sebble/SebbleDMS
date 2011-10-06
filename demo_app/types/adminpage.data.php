<?php

class SebbleDMS_Data_AdminPage extends SebbleDMS_Data {

    static $Smarty;
    
    function getPageBySlug($keys) {
    
        //$this->prepareSmarty();
        if ($this->User->loggedIn) {
            if ($keys['slug']=='') $keys['slug'] = $this->domainHome();
            $this->smartyPage($keys['slug'], $this->User->domain);
        } else {
            $this->smartyPage('login');
        }
    }
    
    // internal functions
    function prepareSmarty() {
    
        require '../Smarty/Smarty.class.php';
        self::$Smarty = new Smarty();
        self::$Smarty->setTemplateDir('/var/www/repos/SebbleDMS/demo_app/admin/pages/');
        self::$Smarty->setCompileDir('/var/www/repos/SebbleDMS/demo_app/admin/tpl_c');
        self::$Smarty->setConfigDir('/var/www/repos/SebbleDMS/demo_app/admin/');
        self::$Smarty->setCacheDir('/var/www/repos/SebbleDMS/demo_app/admin/cache');
        self::$Smarty->debugging = true;
        self::$Smarty->caching = false;
        self::$Smarty->assign('user',$this->User);
        self::$Smarty->assign('session',$_SESSION);
        self::$Smarty->assign('domains', $this->lsDomains());
    }
    
    function smartyPage($slug, $domain = false) {
    
        $this->prepareSmarty();
        
        if ($domain) $tpl = $domain.'/'.$slug.'.tpl';
        else $tpl = $slug.'.tpl';
        if (file_exists('/var/www/repos/SebbleDMS/demo_app/admin/pages/'.$tpl)) {
            $this->Data(self::$Smarty->fetch($tpl));
        } else {
            $this->Data(self::$Smarty->fetch('404.tpl'));
        }
    }
    
    function loadPage($slug) {
        
        return file_get_contents(self::$pages.$slug.'.tpl');
    }
    
    function domainHome() {
        
        $pages = json_decode(
            file_get_contents('/var/www/repos/SebbleDMS/demo_app/config/pages.json')
            , true);
        
        return $pages[$this->User->domain]['default'];
    }
    function lsDomains() {
        
        $d = array();
        $files = scandir('/var/www/repos/SebbleDMS/demo_app/admin/pages/');
        foreach ($files as $f) {
            if (preg_match('#^[A-Z]+$#',$f)) $d[] = $f;
        }
        return $d;
    }
}
