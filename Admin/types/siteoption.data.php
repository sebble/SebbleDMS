<?php


class Data_SiteOption extends Data {

    
    function _connect() {
    
        Data_Website::_connect();
    }
    
    /* Data Fetching Functions */
    
    function ls() {
    
        $link = Data_Website::$dbLink;
        $table = Data_Website::$optionTable;
        $website = Data_Website::$website;
        
        if (Data_Website::$website)
            $whereSite = "WHERE `site`='$website'";
        else
            $whereSite = '';
        
        $sql = "SELECT * FROM `$table` $whereSite;";
        $q = mysql_query($sql, $link);
        
        $result = array();
        while ($r=mysql_fetch_array($q)){
            $result[]=$r;
        }
        
        return $result;
    }
    
    /* Admin Functions */
    
    function add($data) {
    
        if (!$this->user->hasRole('website_admin'))
            return Controller_Admin::Notify('error','Permissions');
        
        $link = Data_Website::$dbLink;
        $table = Data_Website::$optionTable;
        
        $site  = mysql_real_escape_string($data['site']);
        $name  = mysql_real_escape_string($data['name']);
        $value = mysql_real_escape_string($data['value']);
        
        if ($site=='') $site = 'main';
        if ($name=='') return Controller_Admin::Notify('error','Must enter a name');
        
        $sql = "INSERT INTO `$table` (`site`,`name`,`value`)
                  VALUES ('$site','$name','$value');";
        $q = mysql_query($sql, $link);
        
        return Controller_Admin::Notify('success','Option added');
    }
    
    function updateById($data) {
    
        if (!$this->user->hasRole('website_admin'))
            return Controller_Admin::Notify('error','Permissions');
        
        $link = Data_Website::$dbLink;
        $table = Data_Website::$optionTable;
        
        if (!isset($data['id']))
            return Controller_Admin::Notify('error','Invalid ID');
        
        $id = intval($data['id']);
        $value = mysql_real_escape_string($data['value']);
        
        $sql = "UPDATE `$table` SET `value`='$value' WHERE `id`=$id;";
        $q = mysql_query($sql, $link);
        
        return Controller_Admin::Notify('success','Option updated');
    }
    
    function rm($data) {
    
        if (!$this->user->hasRole('website_admin'))
            return Controller_Admin::Notify('error','Permissions');
        
        $link = Data_Website::$dbLink;
        $table = Data_Website::$optionTable;
        
        if (!isset($data['id']))
            return Controller_Admin::Notify('error','Invalid ID');
        
        $id = intval($data['id']);
        
        $sql = "DELETE FROM `$table` WHERE `id`=$id;";
        $q = mysql_query($sql, $link);
        
        return Controller_Admin::Notify('success','Option removed');
    }
    
    /* Database Functions */
    
    static function _createTable() {
    
        $table = Data_Website::$optionTable;
        
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `site` text NOT NULL,
                `name` text NOT NULL,
                `value` text NOT NULL,
                PRIMARY KEY (`id`));";
        # execute query
    }
}
















