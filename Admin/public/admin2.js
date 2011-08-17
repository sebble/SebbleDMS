/* Globals */

var Sebble = {'User':{'details':{'username':'username'}},'App':{
    'homePage':'homePage','appName':'appName','loginMsg':'loginMsg',
    'curPage':'curPage','curSection':'curSection'
}}

/* Actions */

// init
$(function(){
    
    checkLogin(function(login){
        if (login) {
            buildCP();
        } else {
            showLogin();
        }
    });
    
    $(window).bind('hashchange', function() {
        updateState(location.hash);
    })
    
});

/* Display */

function showPage() {

    $('#ui-main').hide();
    // load page ...
    $('#ui-main').fadeIn();
}

function showLogin() {

    // hide control panel
    $('#ui-head').fadeOut();
    $('#ui-side').fadeOut();
    $('#ui-main').fadeOut();
    // show login
    showDialog('login');
}

/* Not Used *
function showCP() {

    $('#ui-head').fadeIn();
    $('#ui-side').fadeIn();
    $('#ui-main').fadeIn();
}
* /Not Used */

function showPopup(template) {

    // check for other popup
    $('#ui-overlay').show();
    // build popup
    buildTemplate('popup', template);
    // show popup
    $('#ui-overlay2').show();
    $('#ui-popup').show();
}
function showDialog(template) {

    // check for other dialogs
    $('#ui-overlay').show();
    // build dialog
    buildTemplate('dialog', template);
    // show dialog
    $('#ui-dialog').show();
}
function hidePopup() {

    $('#ui-dialog').hide();
    $('#ui-overlay2').hide();
    $('#ui-popup').hide();
    $('#ui-overlay').hide();
}
function hideDialog() {

    $('#ui-overlay').hide();
    $('#ui-dialog').hide();
}

function notify (className, msg, timeout) { /* showNotification() */
    
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

function buildCP() {

    checkLogin(function(loggedIn){
        if (loggedIn) {
            hidePopup();
            buildTemplate('head');
            $('#ui-head').fadeIn();
            buildTemplate('side');
            $('#ui-side').fadeIn();
            updateState();
        } else {
            showLogin();
        }
    });
}

function buildTemplate (uiName, tpName) {

    if (tpName===undefined) tpName = uiName;
    $('#ui-'+uiName).jqotesub($('#tpl-'+tpName), window.Sebble);
    // maybe also need different names (for the user popups)
    
    ajaxifyAll(uiName);
}

/* Ajax */

function ajaxPost(action, data, callback, async) {

    //$.post("index.php/"+action, data, function(r){ callback(jQuery.parseJSON(r)) });
    
    if (async===undefined) async=false;
    
    $.ajax({
        type: 'POST',
        url: "index.php/"+action,
        data: data,
        success: function(data){
            // check for notifications
            if (data.notification !== undefined) {
                notify(data.notification.status, data.notification.msg);
                // should data be double wrapped in this case?
                callback(data.data);
            } else
                callback(data);
        },
        error: function(jqXHR, textStatus, errorThrown){
                notify('error', 'An error occured.');
                //console.log(jqXHR);
                //console.log(textStatus);
                console.log(errorThrown);
        },
        dataType: 'json',
        async: async,
        cache: false
    });
}

function loadData(url, params) {

    if (params===undefined) params={}
    ajaxPost(url, params, function(r) {
        varName = url.replace("/", "_");
        //console.log(r)
        //console.log(varName)
        window.Sebble[varName] = r;
    }, false);
}

/* Ajaxify */

function ajaxifyAll(uiName) {

    ajaxifyDialog('#ui-'+uiName);
    ajaxifyForms('#ui-'+uiName);
    ajaxifyTables('#ui-'+uiName);
}

function ajaxifyTables(element) {

    $(element).find('table').dataTable();
    
    $(element).find('table tr').live('click', function () {
    
		  var nTds = $('td', this);
		  var sId = $(nTds[0]).text();
		  
		  var nTable = $(this).parent().parent();
		  var nRow = $(this);
		  var popup = nTable.data('popup');
		  
		  if (popup !== undefined) {
		      var src = nTable.data('popup-src');
		      var datastr = nRow.data('popup-data');
		      console.log(datastr);
		      loadData(src, datastr);
		      myModal(popup);
		  }
	  });
}

function ajaxifyForms(element) {

    $(element).children('form').submit(function(e){
        e.preventDefault();
        var autoClose  = setDefault($(this).data('autoclose'), true);
        var refresh    = setDefault($(this).data('refresh'), true);
        var formAction = $(this).attr('action');
        var formData   = $(this).serializeArray();
        var fn = window[$(this).data('oncomplete')];
        ajaxPost(formAction, formData, function(data){
            if (refresh) updateState();
            if (autoClose) hideDialog();
            if (typeof fn === 'function') fn(data);
        });
    });
}

function ajaxifyDialog(element) {

    $(element).find('.dialog-close').click(function(e){
        hideDialog();
    });
    $(element).find('.popup-close').click(function(e){
        hidePopup();
    });
}


/* Other */

function checkLogin(callback) {

    ajaxPost('Auth/info',null,function(data){
        if(data.loggedIn) {
            callback(true);
        }
        else {
            callback(false);
        }
    });
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

    $('#ui-main').hide();
    
    ajaxPost('Admin/fetchPage/'+group+'/'+page,null,function(data){
      
      if (!data) {
          //notify('error','<b>Error</b>: Error loading page.', 3000);
      } else {
          window.Sebble.App.curSection = data.group;
          window.Sebble.App.curPage = data.title;
          //$('#ui-head').jqotesub($('#tpl-head'), {});
          buildTemplate('head');
          setTitle($.jqote($('#tpl-title'), window.Sebble));
          
          $('#ui-main').html(data.html);
          $('#ui-main [id|="ui"]').each(function(i) {
              buildTemplate($(this).attr('id').substr(3));
          });
          
          // fadeIn and..
          $('#ui-main').fadeIn();
          // ..scrollTo top
          $.scrollTo(0, 0);
          
          ajaxifyAll();
      }
    });
}

function doLogout() {
    
    window.data = {};
    ajaxPost('Admin/authLogout',null,function(data){
        setTitle('SebbleDMS');
        showLogin();
        $("ui-head").html(' ');
        $("ui-side").html(' ');
        $("ui-main").html(' ');
        $("ui-popup").html(' ');
        $("ui-dialog").html(' ');
        window.data = {};
        window.user = {};
    });
}

function setDefault(value, dfault) {

    if (value===undefined) return dfault;
    else return value;
}

function setTitle(value) {

    document.title = value;
}

function loadConfig(config) {

     $.extend(window.Sebble.App, config);
}
