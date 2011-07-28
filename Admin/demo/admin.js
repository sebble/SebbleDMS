/* -- Admin.js -- */

/* Global Vars (hopefully) */

var user = {};
var data = {};
var app  = {};
var ui   = {};

/* Load Config  */
app.curPage = "";

function loadConfig (config) {
  
  app.appName = config.appName;
  app.homePage = config.homePage;
  app.loginMsg = config.loginMsg;
}

/* On Load */
$(function() {
    buildTemplate('dialog','login');
    // cleanup this mess
    // catch hashchange
    $(window).bind('hashchange', function() {
        updateState(location.hash);
    })
    //updateState(location.hash);
    // do login
    doLogin();
});

/*-----------------*/
/* -- Templates -- */
/*-----------------*/
function buildTemplate (uiName, tpName) {
    
    if (tpName===undefined) tpName = uiName;
    $('#ui-'+uiName).jqotesub($('#tpl-'+tpName), {});
    // maybe we could work out how to control the scope here?
}
function fetchTemplate (tpName) {

    // this is incase we later load tpls from external sources
    // we would also add templates to the DOM to allow caching
}

/*-------------------*/
/* -- Bind Events -- */
/*-------------------*/
// not sure..

/*------------------*/
/* -- Animations -- */
/*------------------*/
// aesthetic things like folding goups and sections
function showDialog() {
    
    //$('#ui-overlay').css('opacity', 0.7).fadeIn('slow');
    //$('#ui-overbox').fadeIn('slow');
    $('#ui-overlay').css('opacity', 0.7).show();
    $('#ui-overbox').show();
}

/*----------------------*/
/* -- AJAX Functions -- */
/*----------------------*/
// maybe also form functions

/*-------------------------*/
/* -- Forms and Actions -- */
/*-------------------------*/
// 

/*------------------*/
/* -- UI Dialogs -- */
/*------------------*/
/* Change all dialogs to always execute callback? 
   We would then return true/false/'input'   */
/* I have started playing with more complex popups,
   for these it is not good to 'callback/unbind' on
   <ENTER> as some may have a textarea.. */
/* On that last note I think submit should close
   dialogs if open, so, it's time to get back to ajax
   forms, then embed in custom dialogs*/
function myAlert (myMessage, callback, delay) {

    if (delay===undefined) delay = 300;
    
    ui.alert = myMessage;
    buildTemplate('dialog','alert');
    showDialog();
    
    // delay before bind to avoid accidental keypress
    function bind() {
        $('#ui-okay').bind('click', function (e) {
            myUnbind();
            if (callback!==undefined) callback();
        });
        $(window).bind('keyup.myMsg', function (e) {
            if (e.which==27 || e.which==13) {
                myUnbind();
                if (callback!==undefined) callback();
            }
        });
    }
    setTimeout(bind, delay);
}

function myConfirm (myMessage, callback, delay) {

    if (delay===undefined) delay = 300;
    
    ui.confirm = myMessage;
    buildTemplate('dialog','confirm');
    showDialog();
    
    // delay before bind to avoid accidental keypress
    function bind() {
        $('#ui-yes').bind('click', function (e) {
            myUnbind();
            callback();
        });
        $('#ui-no').bind('click', function (e) {
            myUnbind();
        });
        $(window).bind('keyup.myMsg', function (e) {
            if (e.which==13) {
                myUnbind();
                callback();
            }
            if (e.which==27) {
                myUnbind();
            }
        });
    }
    setTimeout(bind, delay);
}

function myInput (myMessage, callback, delay) {

    if (delay===undefined) delay = 300;
    
    ui.input = myMessage;
    buildTemplate('dialog','input');
    showDialog();
    $('input#input').focus();
    
    // delay before bind to avoid accidental keypress
    function bind() {
        $('#ui-okay').bind('click', function (e) {
            myUnbind();
            callback($('input#input').val());
        });
        $('#ui-cancel').bind('click', function (e) {
            myUnbind();
        });
        $(window).bind('keyup.myMsg', function (e) {
            if (e.which==13) {
                myUnbind();
                callback($('input#input').val());
            }
            if (e.which==27) {
                myUnbind();
            }
        });
    }
    setTimeout(bind, delay);
}

function myModal (template, callback, delay) { // this callback is for fancy templates that need scripts to run once built

    if (delay===undefined) delay = 300;
    
    buildTemplate('dialog', template);
    if (callback!==undefined) callback();
    showDialog();
    $('#ui-dialog .focus').focus(); // optional focus element
    
    // delay before bind to avoid accidental keypress
    function bind() {
        /*$('#ui-okay').bind('click', function (e) {
            myUnbind();
        });
        $('#ui-cancel').bind('click', function (e) {
            myUnbind();
        });*/
        // must let user decide when to unbind.. unfortunately
        /*$(window).bind('keyup.myMsg', function (e) {
            if (e.which==13 || e.which==27) {
                myUnbind();
            }
        });*/
        // This also messses up textareas
    }
    setTimeout(bind, delay);
}

function myUnbind() {

    $('#ui-okay').unbind('click');
    $('#ui-yes').unbind('click');
    $('#ui-no').unbind('click');
    $(window).unbind('keyup.myMsg');
    
    // animation too
    $('#ui-overlay').hide();
    $('#ui-overbox').hide();
}

function myModalClose() {

    myUnbind();
}

/*---------------------*/
/* -- Notifications -- */
/*---------------------*/
// back-compat function, removed
// may still rename notify like myNotify or something

function notify (className, msg, timeout) {
    
    if (timeout===undefined) timeout = 3000;
    
    // pseudo-queue
    if ($('#ui-notify').is(":visible")) {
        setTimeout(function() {notify(className, msg, timeout)}, 500);
    } else {
        $('#ui-notify').removeClass('error').removeClass('success')
        .removeClass('notice').addClass(className).html('<p>'+msg+'</p>').show();
        setTimeout(function() {
            $('#ui-notify').fadeOut();
        }, timeout);
    }
}
/*
$(function() {
    // example notification queue with custom timeout
    notify('success', 'You have reached the demo!');
    notify('notice', 'You are still here.', 5000);
    notify('error', 'You are still here.');
});
*/

/*----------------------*/
/* -- AJAX Functions -- */
/*----------------------*/

function doLogin() {

    // check if already logged in
    ajaxPost('Auth/info',null,function(data){
        if(!data.loggedIn) return;
        
        user = data;
        // load page
        $('#ui-overbox').fadeOut();
        if(data) notify('success','<b>Logged In</b>: Loading control panel.', 3000);
        buildControlPanel();
    });
    // prepare login form anyway
    $('#form-login input').first().focus();
    ajaxifyForm('form-login', function(data) {
        $('#form-login input').val('');
        if(!data) notify('error','<b>Error</b>: Login failed.', 3000);
        else {
          notify('success','<b>Success</b>: Loading control panel.', 3000);
          doLogin();
        }
    })
}

function doLogout() {
    
    window.data = {};
    ajaxPost('Auth/logout',null,function(data){
        
        if(data) notify('notice','<b>Logged Out</b>: You are now logged out.', 3000);
        $('#ui-overlay').css('opacity', 1).fadeIn();
        $('#ui-head').fadeOut();
        buildTemplate('dialog','login');
        $('#ui-overbox').fadeIn();
    });
}

function ajaxifyForm(formId, callback) {

    $('form#'+formId).unbind('submit.forms').bind('submit.forms', function(e) {
        e.preventDefault();
        doSubmit();
    });

    $('form#'+formId+' .submit').unbind('click.forms').bind('click.forms', doSubmit);
    
    $('form#'+formId+' input').unbind('keypress.forms').bind('keypress.forms', function(e) {
        if (e.which == 13) doSubmit();
    });
    
    function doSubmit() {
    
        var formAction = $('#'+formId).attr('action');
        var formData = $('#'+formId).serializeArray();
        ajaxPost(formAction, formData, callback);
    }
}

function ajaxPost(action, data, callback, async) {

    //$.post("index.php/"+action, data, function(r){ callback(jQuery.parseJSON(r)) });
    
    if (async===undefined) async=false;
    
    $.ajax({
      type: 'POST',
      url: "index.php/"+action,
      data: data,
      success: callback,
      dataType: 'json',
      async: async,
      cache: false
    });
}

function buildControlPanel() {

    // load header, generate side-bar, determine page, load page
    $('#ui-head').jqotesub($('#tpl-head'), {});
    
    buildSidebar();
    updateState();
}

function buildSidebar() {

    //console.log("Building Sidebar");
    ajaxPost('Admin/userPages',null,function(data){
        
        $('#ui-side').jqotesub($('#tpl-side'), data);
        
        $('#ui-overlay').fadeOut(2000);
        $('#ui-head').fadeIn(2000);
        
        animateSidebar();
    });
}

function animateSidebar() {
    
 //   $('a').die('click.catch').live('click.catch', function(e) {
 //       //e.preventDefault(); // no need, this will change the ancor for us
 //       console.log("Caught");
 //   });
}

function updateState() {
    
    var re_hash = /#\!\/([a-z0-9-_]+)\/([a-z0-9-_]+)/i;
    var m = location.hash.match(re_hash);
    if (m!==null && m.length == 3) {
        var group = m[1];
        var page = m[2];
        
        loadPage(group, page);
    }
}

function loadPage(group, page) {

    //$('#ui-main').slideUp();
    $('#ui-main').hide();
    
    ajaxPost('Admin/fetchPage/'+group+'/'+page,null,function(data){
      
      //console.log(data);
      if (!data) {
          notify('error','<b>Error</b>: Error loading page.', 3000);
      } else {
          app.curPage = data.group+' :: '+data.title;
          $('#ui-head').jqotesub($('#tpl-head'), {});
		  // Line 164 is the original line from the code.  As you would
		  // expect, removing it means Chrome and Firefox
		  // no longer change the title of the page when a new section is 
		  // loaded.  For some reason, removing it means
		  // IE8 (and even IE7) will start to load the content as intended,
		  // and Chrome and Firefox still work.
		  //$('title').jqotesub($('#tpl-title'), {});
		  //
		  // --------------------------------------------------------------------------------------------------------
		  // IE8 does not object to the following line existing:
		  //$('#ui-title').jqotesub($('#tpl-title'), {});
		  // But this may not be much use as <title> doesn't support classes
		  // according to W3C.  The whole IE issue
		  // could actually stem from <title> not supporting standard event
		  // attributes according to W3C.
		  // --------------------------------------------------------------------------------------------------------
          // SM: does this fix it?
          document.title = $.jqote($('#tpl-title'), {});
          
		  $('#ui-main').jqotesub(data.html, {});
          $('#ui-main').fadeIn();
          // and scrollTo top
          $.scrollTo(0, 0);
          // grab all forms..
          captureForms();
      }
    });
}

function loadData(url, params) {

    if (params===undefined) params={}
    ajaxPost(url, params, function(r) {
        varName = url.replace("/", "_");
        //console.log(r)
        window.data[varName] = r;
    }, false);
}

function captureForms() {
    // note many differnet forms/actions
    // some buttons may do simple things (activate/deactivate)
    // some actions may go via a confirm dialog (delete)
    // some forms may update an input on change (text input, select/option)
    // some forms may wait until submit/enter is pressed
    // some forms may also have files
    // some inputs may just be for files

    $('form').bind('submit.grab', function(e) {
        e.preventDefault();
        // see ajaxifyForm above, change both, leave open for file uploads..
    });
    
    /*
    $('#form-login input').first().focus();
    ajaxifyForm('form-login', function(data) {
        $('#form-login input').val('');
        if(!data) doAlert('error','<b>Error</b>: Login failed.', 3000);
        else {
          doAlert('success','<b>Success</b>: Loading control panel.', 3000);
          doLogin();
        }
    })
    */
}







