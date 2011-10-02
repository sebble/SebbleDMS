// --- iframes
$(function(){
    
    // load iframe
    function iframe(url, pxTop) {
        
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
                e.preventDefault();
                var url = 'index.php/'+$(e.target).attr('href');
                var pxTop = $iframe.contents().find('body').scrollTop();
                iframe(url, pxTop);
            });
        });
    }
    
    // onload
    iframe('index.php/');
    
});
