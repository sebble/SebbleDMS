var Admin = {

    iframe: function(url, pxTop) {
    
        if (pxTop == undefined) pxTop = 0;
        
        var $iframe = $("<iframe>").addClass('admin-iframe').attr('src',url);
        $('body', window.parent.document).append($iframe);
        $iframe.load(function(){
        
            $ic = $iframe.contents();
            $ic.find('body').scrollTop(pxTop);
            $iframe.fadeIn(100,function(){
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
          .add('.form button[type="submit"]').click(function(e){
            
            Admin.submit(this);
        });
        $('.form input').keyup(function(e){
            if (e.keyCode==13) {
                $(this).closest('.form')
                  .find('input[type="submit"],button[type="submit"]')
                  .first().click();
            }
        });
    },
    
    notify: function() {
    
        
    },
    
    submit: function(elem) {
    
        var $this = $(elem);
        
        var $form = $this.closest('.form');
        var $not  = $form.find('.form input,.form textarea,.form select');
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
        
        $.ajax(
            'admin.php',
            {
                async: false,
                cache: false,
                data: {keys: keys, data: data, action: actn},
                //dataType: 'json',
                success: function(data, textStatus, jqXHR) {
                    //console.log([data, jqXHR]);
                    console.log(data);
                    //$save = $('.changed, .error, .warning');
                    //if ($save.length == 0) {
                    //    //window.location.reload(true);
                    //    //reload();
                    //    //done();
                    //} else {
                    //    //$('#reload').animate({top:0});
                    //}
                },
                type: 'POST'
            }
        );
    }
    
}
