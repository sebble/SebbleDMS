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
    buildTemplate('login');
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
function buildTemplate (tpName) {

    $('#ui-'+tpName).jqotesub($('#tpl-'+tpName), {});
    // maybe we could work out how to control the scope here?
}
function fetchTemplate (tpName) {

    // this is incase we later load tpls from external sources
    // we would also add templates to the DOM to allow caching
}

/*-------------------*/
/* -- Bind Events -- */
/*-------------------*/


/*------------------*/
/* -- Animations -- */
/*------------------*/

/*----------------------*/
/* -- AJAX Functions -- */
/*----------------------*/


/*------------------*/
/* -- UI Dialogs -- */
/*------------------*/
function myAlert (myMessage, callback, delay) {

    if (delay===undefined) delay = 300;
    
    ui.alert = myMessage;
    buildTemplate('alert');
    $('#ui-alert').show();
    $('#ui-overlay').css('opacity', 0.7).fadeIn('slow');
    $('#ui-overbox').fadeIn('slow');
    
    // delay before bind to avoid accidental keypress
    function bind() {
        $('#ui-okay').bind('click', function (e) {
            myUnbind();
            callback();
        });
        $(window).bind('keyup.myMsg', function (e) {
            if (e.which==27 || e.which==13) {
                myUnbind();
                callback();
            }
        });
    }
    setTimeout(bind, delay);
}

function myConfirm (myMessage, callback, delay) {

    if (delay===undefined) delay = 300;
    
    ui.confirm = myMessage;
    buildTemplate('confirm');
    $('#ui-confirm').show();
    $('#ui-overlay').css('opacity', 0.7).fadeIn('slow');
    $('#ui-overbox').fadeIn('slow');
    
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

function myUnbind() {

    $('#ui-okay').unbind('click');
    $('#ui-yes').unbind('click');
    $('#ui-no').unbind('click');
    $(window).unbind('keyup.myMsg');
    
    // animation too
    $('#ui-overlay').hide();
    $('#ui-overbox').hide();
    $('#ui-confirm').hide();
    $('#ui-alert').hide();
}


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
        if(data) doAlert('success','<b>Logged In</b>: Loading control panel.', 3000);
        buildControlPanel();
    });
    // prepare login form anyway
    $('#form-login input').first().focus();
    ajaxifyForm('form-login', function(data) {
        $('#form-login input').val('');
        if(!data) doAlert('error','<b>Error</b>: Login failed.', 3000);
        else {
          doAlert('success','<b>Success</b>: Loading control panel.', 3000);
          doLogin();
        }
    })
}

function doLogout() {
    
    window.data = {};
    ajaxPost('Auth/logout',null,function(data){
        
        if(data) doAlert('notice','<b>Logged Out</b>: You are now logged out.', 3000);
        $('#ui-overlay').fadeIn();
        $('#ui-head').fadeOut();
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

function doAlert(className, msg, timeout) {
    
    $('#ui-alert').removeClass('error').removeClass('success')
    .removeClass('notice').addClass(className).html('<p>'+msg+'</p>').show();
    setTimeout(function() {
        $('#ui-alert').fadeOut();
    }, timeout);
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
          doAlert('error','<b>Error</b>: Error loading page.', 3000);
      } else {
          app.curPage = data.group+' :: '+data.title;
          $('#ui-head').jqotesub($('#tpl-head'), {});
		  // Line 164 is the original line from the code.  As you would expect, removing it means Chrome and Firefox
		  // no longer change the title of the page when a new section is loaded.  For some reason, removing it means
		  // IE8 (and even IE7) will start to load the content as intended, and Chrome and Firefox still work.
		  //$('title').jqotesub($('#tpl-title'), {});
		  //
		  // --------------------------------------------------------------------------------------------------------
		  // IE8 does not object to the following line existing:
		  //$('#ui-title').jqotesub($('#tpl-title'), {});
		  // But this may not be much use as <title> doesn't support classes according to W3C.  The whole IE issue
		  // could actually stem from <title> not supporting standard event attributes according to W3C.
		  // --------------------------------------------------------------------------------------------------------
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







