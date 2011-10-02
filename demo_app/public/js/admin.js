var Admin = {

    iframe: function(url, pxTop) {
    
        if (pxTop == undefined) pxTop = 0;
        
        var $iframe = $("<iframe>").addClass('iframe').attr('src',url);
        $('body', window.parent.document).append($iframe);
        $iframe.load(function(){
        
            $iframe.contents().find('body').scrollTop(pxTop);
            $iframe.fadeIn(100,function(){
                $('iframe', window.parent.document).not($iframe).remove();
            });
            window.parent.document.title = $iframe.contents()[0].title;
            
            // steal links
            $iframe.contents().find('a').click(function(e){
            
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
    }
    
}
