<?php


class Data_SiteContent extends Data {

    
    static $static;
    
    var $var;
    
    
    function ls() {
    
        # list content
        # list active content
        # ? content_editor => + inactive content
        # ? website_admin => + inactive content
    }
    
    static function _createTable() {
    
        $table = Data_Website::$contentTable;
        
        $sql = "CREATE TABLE IF NOT EXISTS `$table` (
                `id` int(11) NOT NULL AUTO_INCREMENT,
                `site` text NOT NULL,
                `author` text NOT NULL,
                `created` date NOT NULL,
                `modified` date NOT NULL,
                `title` text NOT NULL,
                `content` text NOT NULL,
                `description` text NOT NULL,
                `name` text NOT NULL,
                `status` int(2) NOT NULL DEFAULT '0',
                `meta_details` text,
                PRIMARY KEY (`id`));";
        # execute query
    }
}
















