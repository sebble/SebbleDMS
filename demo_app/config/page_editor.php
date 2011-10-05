<?php

/*

    Object/filter/actionName
    Object/actionName
    
    Object/filter/actionName/key1/key2

*/

if (count($_POST)>0) {
    
    // testing the naming scheme
    $action = explode('/', $_POST['action']);
    $count = count($action);
    $pre = 'DMSData_';
    if ($count == 2) {
        $object = $pre.$action[0];
        $filter = '';
        $action = $action[1];
    } else if ($count == 3) {
        $object = $pre.$action[0];
        $filter = $action[1];
        $action = $action[2];
    }
    
    #echo json_encode($_POST);exit;
    
    require 'cppage.data.php';
    $X = new $object;
    @$X->action($action, $filter, $_POST['keys'], $_POST['data']);
    
    
    //echo json_encode(array($object,$filter,$action));
    
} else {

?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>HTML5</title>
  
  <meta name="description" content="" />
  <meta name="keywords" content="" />
  <meta name="author" content="Sebastian Mellor &lt;sebble@sebble.com&gt;" />
  
  <script src="https://ajax.googleapis.com/ajax/libs/jquery/1.6.4/jquery.min.js" type="text/javascript"></script>
  <script type="text/javascript">
$(function(){
    
    // scrolled selector extension
    (function($) {
       $.extend($.expr[":"], {
            scrolled: function(element) {
                return $(element).scrollTop()>0;
            }
       });
    })(jQuery)
    
    // watch all fields
    $('.form').delegate('input,select,textarea','change',function(e){
        $(this).removeClass('warning error saved').addClass('changed');
    });
    
    // listen for submit
    $('.form input[type="submit"]')
      .add('.form button[type="submit"]').click(function(e){
        e.preventDefault();
        var $this = $(this);
        
        var $form = $this.closest('.form');
        var $not  = $form.find('.form input,.form textarea,.form select');
        var $data = $form.find('input,textarea,select')
                .not(':disabled,input.key')
                .not('input[type="hidden"],input[type="submit"],input[type="reset"]')
                .not($not);
                //.not('.form .form input,.form .form textarea,.form .form select');
        var $keys = $form.find('input.key').not($not);
        $data.removeClass('changed error warning').addClass('saved')
                .delay(1000).animate({outlineWidth:"2px"},0,'swing',function(){$(this).removeClass('saved')});
        
        var data = {};
        $data.each(function(){
            var $this = $(this);
            data[$this.attr('name')] = $this.val();
        });
        
        var keys = {};
        $keys.each(function(){
            var $this = $(this);
            keys[$this.attr('name')] = $this.val();
        });
        
        var actn = $this.attr('name');
        
        waiting();
        $.ajax(
            'page_editor.php',
            {
                async: false,
                cache: false,
                data: {keys: keys, data: data, action: actn},
                //dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    //console.log([data, jqXHR]);
                    console.log(data);
                    $save = $('.changed, .error, .warning');
                    if ($save.length == 0) {
                        //window.location.reload(true);
                        reload();
                        done();
                    } else {
                        $('#reload').animate({top:0});
                    }
                },
                type: 'POST'
            }
        );
    });
    window.onbeforeunload = function() {
        $save = $('.changed, .error, .warning');
        if ($save.length > 0) {
            $('.saved').removeClass('saved');
            $save.removeClass('changed').addClass('warning');
            return "If you leave this page, unsaved changes will be lost.";
        }
    };
    $('#reload').click(function(){
        //window.location.reload();
        reload();
    });
    
    function waiting(){
    
        $('body', window.parent.document).append($('<div id="wait">').css(
            {position:'fixed',top:0,left:0,width:'100%',height:'100%',
             background:'url("ajax-loader.gif") center center no-repeat rgba(255,255,255,0.5)'}));
    }
    function done(){
    
        $('#wait', window.parent.document).remove();
    }
    
    function reload(){
    
        /*$.ajax(
            'page_editor.php',
            {
                async: false,
                cache: false,
                success: function(data, textStatus, jqXHR) {
                    //console.log(data);
                    var $iframe = $("<iframe class=\"fader\" >");
                    var scr = $('body').scrollTop();
                    $('body').append($iframe);
                    $iframe.contents().find('body').html(data).scrollTop(scr);
                    $iframe.fadeIn();
                },
                type: 'GET'
            }
        );*/
        
        $save = $('.changed, .error, .warning');
        if ($save.length > 0) {
            $('.saved').removeClass('saved');
            $save.removeClass('changed').addClass('warning');
            var leave = confirm("If you reload this page, unsaved changes will be lost.\nLeave page?")
            if (!leave) return;
        }
        
        var $iframe = $("<iframe class=\"fader\" src=\"page_editor.php\">");
        console.log(window.parent.document);
        var scr = $('body').scrollTop();
        $('body', window.parent.document).append($iframe);
        $iframe.load(function(){
            //console.log($iframe.contents().find('body'));
            $iframe.contents().find('body').scrollTop(scr);
            $iframe.fadeIn(100,function(){
                $('#reload').remove();
                $('table').remove();
                $('iframe', window.parent.document).not($iframe).remove();
            });
        });
    }
    
});
  </script>
  <style>

.fader {
    position: fixed;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    border: none;
    width: 100%;
    height: 100%;
    background-color: #fff;
    display: none;
}

body {
    font-family: verdana;
}
.changed {
    outline: solid 2px rgba(0,0,255,0.5);
}
.saved {
    outline: solid 2px rgba(0,255,0,0.5);
}
.warning {
    outline: solid 2px rgba(255,127,0,0.5);
}
table thead td {
    border-bottom: solid 2px #999;
}
table td {
    padding: 0.3em 0.5em;
}
table tbody td {
    color: #999;
}
table tbody td input[type="text"] {
    border: none;
    border: dashed 1px #ddd;
    cursor: pointer;
    width: 7em;
    color: #333;
    padding: 0;
    margin: 0;
    font-family: inherit;
    font-size: inherit;
}
table button {
    border: none;
    background: transparent;
    padding: 0;
    margin: 0;
    cursor: pointer;
}
table input[type="submit"] {
    font-family: inherit;
    font-size: inherit;
    cursor: pointer;
}

#reload {
    position: fixed;
    top: -40px;
    left: 0;
    right: 0;
    line-height: 2em;
    border-bottom: solid 2px #f90;
    background-color: #fed;
    text-align: center;
    cursor: pointer;
}
body {
    padding-top: 2em;
}
  </style>
</head>
<body>
<div id="reload">Reload this page to see any changes.</div>
<?php

function table($data) {
    
    $html = "<table cellspacing=0 class=\"form\">\n  <thead>\n    <tr><td>Domain</td><td>Group</td><td>Title</td><td>Slug</td><td>Require</td><td>Actions</td></tr>\n  </thead>\n  <tfoot>\n    <tr><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td>&nbsp;</td><td><input type=\"submit\" name=\"CpPage/update\" value=\"Save Changes\" /></td></tr>\n  </tfoot>\n  <tbody>\n";
    foreach ($data as $domain => $d) {
        $html .= "    <tr><td>{$domain}</td><td>&nbsp;</td><td>&nbsp;</td><td>{$d['default']}</td><td><input type=\"text\" name=\"{$domain}_requires\" value=\"{$d['requires']}\" /></td><td class=\"form\"><input type=\"hidden\" class=\"key\" name=\"domain\" value=\"{$domain}\" /><button type=\"submit\" name=\"CpPage/delete\"><img src=\"../public/icons/delete.png\" /></button></td></tr>\n";
        foreach ($d['groups'] as $group => $g) {
            $html .= "    <tr><td>&nbsp;</td><td><input type=\"text\" name=\"{$domain}_{$group}_title\" value=\"{$g['title']}\" /></td><td>&nbsp;</td><td><input type=\"text\" disabled value=\"{$group}\" /></td><td><input type=\"text\" name=\"{$domain}_{$group}_requires\" value=\"{$g['requires']}\" /></td><td class=\"form\"><input type=\"hidden\" class=\"key\" name=\"domain\" value=\"{$domain}\" /><input type=\"hidden\" class=\"key\" name=\"group\" value=\"{$group}\" /><button type=\"submit\" name=\"CpPage/delete\"><img src=\"../public/icons/delete.png\" /></button> <!--<button type=\"submit\" name=\"CpPage/moveUp\"><img src=\"../public/icons/arrow_up.png\" /></button> <button type=\"submit\" name=\"CpPage/moveDown\"><img src=\"../public/icons/arrow_down.png\" /></button>--></td></tr>\n";
            foreach ($g['pages'] as $page => $p) {
                $html .= "    <tr><td>&nbsp;</td><td>&nbsp;</td><td><input type=\"text\" name=\"{$domain}_{$group}_{$page}_title\" value=\"{$p['title']}\" /></td><td>{$group}.<input type=\"text\" name=\"{$domain}_{$group}_{$page}_slug\" disabled value=\"{$page}\" /></td><td><input type=\"text\" name=\"{$domain}_{$group}_{$page}_requires\" value=\"{$p['requires']}\" /></td><td class=\"form\"><input type=\"hidden\" class=\"key\" name=\"domain\" value=\"{$domain}\" /><input type=\"hidden\" class=\"key\" name=\"group\" value=\"{$group}\" /><input type=\"hidden\" class=\"key\" name=\"page\" value=\"{$page}\" /><button type=\"submit\" name=\"CpPage/delete\"><img src=\"../public/icons/delete.png\" /></button> <!--<button type=\"submit\" name=\"CpPage/moveUp\"><img src=\"../public/icons/arrow_up.png\" /></button> <button type=\"submit\" name=\"CpPage/moveDown\"><img src=\"../public/icons/arrow_down.png\" /></button>--> <button type=\"submit\" name=\"CpPage/setHomepage\"><img src=\"../public/icons/house.png\" /></button></td></tr>";
            }
            $html .= "    <tr class=\"form\" class=\"new\"><td>&nbsp;<input type=\"hidden\" class=\"key\" name=\"domain\" value=\"{$domain}\" /><input type=\"hidden\" class=\"key\" name=\"group\" value=\"{$group}\" /></td><td>&nbsp;</td><td><input type=\"text\" placeholder=\"Page Title\" name=\"title\" /></td><td>{$group}.<input type=\"text\" placeholder=\"pageslug\" name=\"slug\" /></td><td><input type=\"text\" name=\"requires\" placeholder=\"requires\" /></td><td><button type=\"submit\" name=\"CpPage/insertPage\"><img src=\"../public/icons/add.png\" /></button></td></tr>";
        }
        $html .= "    <tr class=\"form\" class=\"new\"><td>&nbsp;<input type=\"hidden\" class=\"key\" name=\"domain\" value=\"{$domain}\" /></td><td><input type=\"text\" placeholder=\"Group Title\" name=\"title\" /></td><td>&nbsp;</td><td><input type=\"text\" placeholder=\"groupslug\" name=\"slug\" /></td><td><input type=\"text\" name=\"requires\" placeholder=\"requires\" /></td><td><button type=\"submit\" name=\"CpPage/insertGroup\"><img src=\"../public/icons/add.png\" /></button></td></tr>";
    }
    $html .= "    <tr class=\"form\" class=\"new\"><td><input type=\"text\" placeholder=\"DOMIAN\" name=\"domain\" /></td><td>&nbsp;</td><td>&nbsp;</td><td><input type=\"text\" placeholder=\"default.page\" name=\"slug\" /></td><td><input type=\"text\" name=\"requires\" placeholder=\"requires\" /></td><td><button type=\"submit\" name=\"CpPage/insertDomain\"><img src=\"../public/icons/add.png\" /></button></td></tr>";
    
    $html .= "  </tbody>\n</table>\n";
    
    return $html;
}

echo table(json_decode(file_get_contents('pages.json'),true));

?>
</body>
</html>
<?php
}
?>
