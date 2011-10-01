// actually want this in global scope
var Admin;



(function( $ ){

    // private variables
    var notifyTimeout;
    
    // public Admin definiton
    Admin = {
        
        
        
        config: {
        
            title: "Title: $1"
        
        },
        
        elements: [],
        
        
        
        // initialise this application
        init: function() {
            
            // set up all events here
            
            // example event
            $('body').delegate("p", "hover", function(e){
            
                $(this).toggleClass("hover");
            
            });
            
            $('body').delegate(".ui-notify", "click", function(e){
            
                clearTimeout(notifyTimeout);
                
                $('.ui-notify').fadeOut(200, function(){
                
                    $(this).remove();
                    
                });
            
            });
            
        },
        
        
        
        title: function( title ) {
            
            setTitle(title.replace(/(.+)/i, Admin.config.title));
            
        },
        
        
        
        dialog: function() {
        
            // show/hide dialog
            
            this.dialog.hide = function() {
            
                console.log("hide");
            
            }
            
            console.log("dialog");
            
        },
        
        
        
        notify: function( notification, className, timeout ) {
        
            // defaults
            timeout     = setDefault(timeout,   2000);
            className   = setDefault(className, 'notice');
            
            // pseudo-queue
            if ($('.ui-notify').length > 0) {
            
                setTimeout(function() {
                
                    Admin.notify(notification, className, timeout);
                    
                }, 500);
                
            } else {
                
                $('<div />')
                    .addClass("ui-notify")
                    .addClass(className)
                    .html('<p>'+notification+'</p>')
                    .appendTo('body');
                
                notifyTimeout = setTimeout(function() {
                
                    $('.ui-notify').fadeOut(600, function(){
                    
                        $(this).remove();
                        
                    });
                    
                }, timeout);
                
            }
            
        }
        
    };
    
    
    
    function setDefault( value, dfault ) {

        if (value===undefined) return dfault;
        
        else return value;
        
    }
    
    
    
    function setTitle( value ) {
    
        document.title = value;
        
    }
    
    
    
    $(function(){
    
        // onload event stuff here
        Admin.init();
        
        $('body').append("<p>p1</p><p>p2</p>");
        
        console.log("Ready.");
        
    });
    
    
    
})( jQuery );
