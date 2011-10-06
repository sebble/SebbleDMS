var Admin = {

    iframe: function(url, pxTop) {
    
        if (pxTop == undefined) pxTop = 0;
        
        var $iframe = $("<iframe>").addClass('admin-iframe').attr('src',url);
        $('body', window.parent.document).append($iframe);
        $iframe.load(function(){
        
            $ic = $iframe.contents();
            $ic.find('body').scrollTop(pxTop);
            $('.admin-iframe', window.parent.document).not($iframe).fadeOut(600);
            $iframe.fadeIn(600,function(){
                $('iframe', window.parent.document).not($iframe).remove();
            });
            
            // set new title
            window.parent.document.title = $iframe.contents()[0].title;
            
            // steal links
            $ic.find('a').click(function(e){
            
                var href = $(e.target).attr('href');
                if (href.substr(0,7) == 'http://')
                    { $(e.target).attr('target','_blank'); return; }
                if (href.substr(0,8) == 'https://')
                    { $(e.target).attr('target','_blank'); return; }
                if (href.substr(0,7) == 'mailto:') return;
                
                e.preventDefault();
                var pxTop = $iframe.contents().find('body').scrollTop();
                Admin.iframe('admin.php/'+href, pxTop);
            });
        });
    },
    
    enhance: function() {
    
        // watch input fields
        $('.form').delegate('input,select,textarea','change',function(e){
            $(this).removeClass('warning error saved').addClass('changed');
        });
        
        // listen for actions
        $('.form input[type="submit"]')
          .add('.form button[type="submit"]')
          .add('.form a.action').click(function(e){
            e.preventDefault();
            Admin.submit(this);
        });
        $('.form input').keyup(function(e){
            if (e.keyCode==13) {
                $(this).closest('.form')
                  .find('input[type="submit"],button[type="submit"]')
                  .first().click();
            }
        });
        
        $('form.form').submit(function(e){
            e.preventDefault();
        });
    },
    
    notify: function(status, msg) {
    
        console.log('Status-'+status+': '+msg);
        var $notify = $("<div>").addClass('admin-notify')
            .html('<p>'+msg+'</p>').addClass('status'+status);
        $('body', window.parent.document).append($notify);
        var fadeOut = function(){
            $notify.fadeOut(600, function(){
                $notify.remove();
            });
        }
        setTimeout(fadeOut, 1000);
    },
    
    submit: function(elem) {
    
        var $this = $(elem);
        
        var $form = $this.closest('.form');
        var $not  = $('.form input',$form).add('.form textarea',$form).add('.form select',$form);
        var $data = $form.find('input,textarea,select')
                .not(':disabled,input.key')
                .not('input[type="hidden"],input[type="submit"],input[type="reset"]')
                .not($not);
        var $keys = $form.find('input.key').not($not);
        
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
        if (actn == undefined) actn = $this.attr('href');
        
        console.log(actn);
        
        var onError = function(jqXHR, textStatus, errorThrown) {
                        window.parent.Admin.notify('500', 'An error occured.');
                        console.log(errorThrown);
        }
        var onSuccess = function(data, textStatus, jqXHR) {
                    console.log(data);
                    var status = jqXHR.getResponseHeader("X-Admin-Status");
                    var msg    = jqXHR.getResponseHeader("X-Admin-Message");
                    if (msg != '') {
                        window.parent.Admin.notify(status, msg);
                    }
                    var data = $.parseJSON(data);
                    console.log(data);
                    if (status > 299) {
                        // error
                        $data.removeClass('warning error saved changed').addClass('error');
                    } else {
                        // success
                        $data.removeClass('warning error saved changed').addClass('saved');
                        // check for other changed elements
                        $save = $('.changed, .error, .warning');
                        if ($save.length == 0) {
                            // reload window
                            Admin.iframe(window.location, $('body').scrollTop());
                            console.log('reload');
                        } else {
                            // warn user about other fields
                        }
                    }
                }
        
        $.ajax(
            'admin.php',
            {
                async: false,
                cache: false,
                data: {keys: keys, data: data, action: actn},
                //dataType: 'json', // remove to see PHP errors
                error: onError,
                success: onSuccess,
                type: 'POST'
            }
        );
    }
    
}
