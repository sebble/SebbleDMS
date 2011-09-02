<?php


class Data_SitePage extends Data {

    
    function _connect() {
    
        Data_Website::_connect();
    }
    
    /* Data Fetching Functions */
    
    function ls() {
    
        $link = Data_Website::$dbLink;
        $table = Data_Website::$pageTable;
        $website = Data_Website::$website;
        
        
        if (Data_Website::$website)
            $whereSiteActive = "WHERE `site`='$website'";
        else
            $whereSiteActive = '';
        
        if (!$this->user->hasRole('website_admin')) {
            if ($whereSiteActive == '')
                $whereSiteActive = "WHERE `active`=1";
            else
                $whereSiteActive .= " AND `active`=1";
        }
        
        
        $sql = "SELECT * FROM `$table` $whereSiteActive;";
        $q = mysql_query($sql, $link);
        
        $result = array();
        while ($r=mysql_fetch_array($q)){
            $result[]=$r;
        }
        
        return $result;
    }
    
    function details($data) {
    
        $link = Data_Website::$dbLink;
        $table = Data_Website::$pageTable;
        $website = Data_Website::$website;
        
        if (!isset($data['id']))
            return Controller_Admin::Notify('error','Invalid ID');
        $id = intval($data['id']);
        
        
        if (Data_Website::$website)
            $whereSiteActiveId = "WHERE `site`='$website'";
        else
            $whereSiteActiveId = '';
        
        if (!$this->user->hasRole('website_admin')) {
            if ($whereSiteActiveId == '')
                $whereSiteActiveId = "WHERE `active`=1";
            else
                $whereSiteActiveId .= " AND `active`=1";
        }
        
        if ($whereSiteActiveId == '')
            $whereSiteActiveId = "WHERE `id`=$id";
        else
            $whereSiteActiveId .= " AND `id`=$id";
        
        
        $sql = "SELECT * FROM `$table` $whereSiteActiveId;";
        $q = mysql_query($sql, $link);
        
        if ($r=mysql_fetch_array($q)){
            return $r;
        }
        
        return Controller_Admin::Notify('error','Missing page');
    }
    
    /* Admin Functions */
    
    function rm($data) {
    
        if (!$this->user->hasRole('website_admin'))
            return Controller_Admin::Notify('error','Permissions');
        
        $link = Data_Website::$dbLink;
        $table = Data_Website::$pageTable;
        
        if (!isset($data['id']))
            return Controller_Admin::Notify('error','Invalid ID');
        
        $id = intval($data['id']);
        
        $sql = "DELETE FROM `$table` WHERE `id`=$id;";
        $q = mysql_query($sql, $link);
        
        return Controller_Admin::Notify('success','Page removed');
    }
    
    function setActive($data) {
    
        if (!$this->user->hasRole('website_admin'))
            return Controller_Admin::Notify('error','Permissions');
        
        $link = Data_Website::$dbLink;
        $table = Data_Website::$pageTable;
        
        if (!isset($data['id']))
            return Controller_Admin::Notify('error','Invalid ID');
        if (!isset($data['active']))
            return Controller_Admin::Notify('error','Must set active value');
        
        $id = intval($data['id']);
        
        if ($data['active']=='true')
            $active = 1;
        else
            $active = 0;
        
        $sql = "UPDATE `$table` SET `active`=$active WHERE `id`=$id;";
        $q = mysql_query($sql, $link);
        
        return Controller_Admin::Notify('success','Page visibility changed');
    }
    
    function update($data) {
    
        if (!$this->user->hasRole('website_admin'))
            return Controller_Admin::Notify('error','Permissions');
        
        $link = Data_Website::$dbLink;
        $table = Data_Website::$pageTable;
        
        if (!isset($data['id']))
            return Controller_Admin::Notify('error','Invalid ID');
        
        $id = intval($data['id']);
        
        if (isset($data['active']))
            $active = 1;
        else
            $active = 0;
        
        $sql = "UPDATE `$table` SET `active`=$active WHERE `id`=$id;";## more
        $q = mysql_query($sql, $link);
        
        return Controller_Admin::Notify('success','Page updated');
    }
    
    function add($data) {
    
        if (!$this->user->hasRole('website_admin'))
            return Controller_Admin::Notify('error','Permissions');
        
        $link = Data_Website::$dbLink;
        $table = Data_Website::$pageTable;
        
        
        $site  = mysql_real_escape_string($data['site']);
        $slug  = mysql_real_escape_string($data['slug']);
        $title = mysql_real_escape_string($data['title']);
        
        if ($site=='') $site = 'main';
        if ($slug=='') return Controller_Admin::Notify('error','Must enter a slug');
        if ($title=='') return Controller_Admin::Notify('error','Must enter a title');
        
        $sql = "INSERT INTO `$table` (`site`,`slug`,`title`,`active`)
                  VALUES ('$site','$slug','$title',0);";
        $q = mysql_query($sql, $link);
        
        return Controller_Admin::Notify('success','Page updated');
    }
    
    /* Database Functions */
    
    static function _createTable() {
    
        $table = Data_Website::$pageTable;
        
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `site` text NOT NULL,
                `slug` text NOT NULL,
                `title` text NOT NULL,
                `description` text NOT NULL,
                `keywords` text NOT NULL,
                `template` text NOT NULL,
                `options` text NOT NULL,
                `regex` text NOT NULL,
                `active` int(1) NOT NULL DEFAULT '0',
                PRIMARY KEY (`id`));";
        # execute query
    }
}
















