/* Globals */

var Sebble = {'User':{'details':{'username':'username'}},'App':{
    'homePage':'homePage','appName':'appName','loginMsg':'loginMsg',
    'curPage':'curPage','curSection':'curSection','confirmMsg':'Are you sure?'
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
    });

    //showGCFPrompt();
    //CFInstall.check({preventPrompt: true, onmissing: showGCFPrompt, oninstall: refreshGCF});
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

function showDialog(template, aclass) {

    aclass = setDefault(aclass, '');
    
    // create new element
    var idnum = Math.floor(Math.random()*10000); // 1/10000 possibility of trouble
    $('<div id="ui-overlay'+idnum+'" class="ui-overlay"></div>').appendTo('body');
    $('<div id="ui-dialog'+idnum+'" class="ui-dialog '+aclass+'" data-src="'+template+'"></div>').appendTo('body');

    $('#ui-overlay'+idnum).show();
    buildTemplate('dialog'+idnum, template);
    $('#ui-dialog'+idnum).show();
}
function hideDialog() {

    /*
    $('#ui-overlay'+idnum).hide(); // memory hog!
    $('#ui-dialog'+idnum).hide();
    */
    /*
    $('#ui-overlay'+idnum).remove(); // requires idnum -- pfft!
    $('#ui-dialog'+idnum).remove();
    */
    $('.ui-dialog').last().remove();
    $('.ui-overlay').last().remove();
}

function notify (className, msg, timeout) { /* showNotification() */
    
    if (timeout===undefined) timeout = 2000;
    
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

function showGCFPrompt() {

    showDialog('gcfprompt');
}
function refreshGCF() {

    window.location.reload(false)
}
function installGCF() {

    //$('<iframe src="http://www.google.com/chromeframe" width="100%" height="100%" style="position:fixed;top:0;left:0;width:100%;height:100%;border:none;margin:0;padding:0;z-index:99999;"></iframe>').appendTo('body');
    window.open('http://www.google.com/chromeframe','_blank');
}

/* Ajax */

function ajaxPost(action, data, callback, async) {

    //$.post("index.php/"+action, data, function(r){ callback(jQuery.parseJSON(r)) });
    
    if (async===undefined) async=false;
    
    $.ajax({
        type: 'POST',
        url: "index.php/"+action,
        data: data,
        success: function(data, status, XHR){
            // check for notifications
            var status = XHR.getResponseHeader("X-Admin-Status")
            var msg    = XHR.getResponseHeader("X-Admin-Message")
            if (status !== null) {
                notify(status, msg);
                if (status!='error') callback(data);
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
        console.log(r)
        //console.log(varName)
        window.Sebble[varName] = r;
    }, false);
}

/* Ajaxify */

function ajaxifyAll(uiName) {

    ajaxifyDialog('#ui-'+uiName);
    ajaxifyForms('#ui-'+uiName);
    ajaxifyTables('#ui-'+uiName);
    ajaxifyActions('#ui-'+uiName);
}

function ajaxifyTables(element) {

    window.Sebble.oTable = $(element).find('table').dataTable();
    // change above to something like
    
    $(element).find('table.fancy').each(function(i,el){
    
        var TID = $(el).attr('id');
        window.Sebble.DataTables[TID] = $(el).dataTable();
        
        $(el).find('tbody tr').live('click', function () {
            
            // make sure this isn't a 'more' row
            if ($(this).children().first().hasClass('more')) return;
            
		        //var RID = $('td', this).first().text();
		        var RID = $(this).data('row');
		        window.Sebble.DataTables[TID].selectedRow = RID;
		        window.Sebble.selectedRow = RID;
            
            var tplMore = $(el).data('more');
            var tplPopup = $(el).data('popup');
            
            if (tplMore !== undefined) {
                if ($(this).next().children().first().hasClass('more')) {
                    window.Sebble.DataTables[TID].fnClose(this);
                } else {
                    // close others first
                    $(this).siblings().each(function(i,el){
                        window.Sebble.DataTables[TID].fnClose(el);
                    });
                    var html = '<div id="ui-more">'
                             + $.jqote($('#tpl-'+tplMore), window.Sebble)
                             + '</div>';
                    window.Sebble.DataTables[TID].fnOpen(this, html, 'more');
                    ajaxifyAll('more');
                }
            }
            if ($(el).data('action') == 'dialog') {
                console.log('show dialog');
            }
        });
    });
}

function ajaxifyForms(element) {

    var idnum = $(element).attr('id').substr(9);
    $(element).find('form').submit(function(e){
        e.preventDefault();
        var autoClose  = setDefault($(this).data('autoclose'), true);
        var refresh    = setDefault($(this).data('refresh'), 'page');
        var onSuccess  = setDefault($(this).data('onsuccess'), false);
        if (onSuccess) { refresh = false; autoClose = false; }
        var confirm    = setDefault($(this).data('confirm'), false);
        var formAction = $(this).attr('action');
        var formData   = $(this).serializeArray();
        var fn = window[$(this).data('oncomplete')];
        var thisForm = this;
        
        function formSubmit() {
        
            ajaxPost(formAction, formData, function(data){
                if (refresh=='page') updateState();
                if (refresh=='dialog') {
                    var dialog = $('.ui-dialog').last().attr('id').substr(3);
                    var template = $('.ui-dialog').last().data('src');
                    buildTemplate(dialog, template);
                }
                if (autoClose) hideDialog(idnum);
                if (typeof fn === 'function') fn(data);
                if (typeof window[onSuccess] === 'function')
                    window[onSuccess](thisForm);
            });
        }
        
        if (confirm==false) {
            formSubmit();
        } else {
            if (confirm!==true) window.Sebble.App.confirmMsg = confirm;
            else window.Sebble.App.confirmMsg = 'Are you sure?';
            showDialog('confirm');
            var idnum = $('.ui-dialog').last().attr('id').substr(9);
            $('.ui-dialog').last().find('input[type="submit"]').click(function(){
                hideDialog(idnum);
                formSubmit();
            });
        }
    }).find('.form-submit').click(function(e){
        e.preventDefault();
        $(this).closest('form').submit();
    });
}

function ajaxifyDialog(element) {

    var idnum = $(element).attr('id').substr(9);
    $(element).find('.dialog-close').click(function(e){
        hideDialog(idnum);
    });
    
    $('.ui-dialog .overflow').css({maxHeight:($(window).height()-300)});
}
$(function(){
    $(window).resize(function(){
        $('.ui-dialog .overflow').css({maxHeight:($(window).height()-300)});
    });
});

function ajaxifyActions(element) {

    $(element).find('.action').click(function(e){
        e.preventDefault();
        
        var autoClose  = setDefault($(this).data('autoclose'), true);
        var refresh    = setDefault($(this).data('refresh'), 'page');
        var confirm    = setDefault($(this).data('confirm'), false);
        var formAction = $(this).data('action');
        var formData   = $(this).data('data');
        console.log(formData);
        var fn = window[$(this).data('oncomplete')];
        
        
        function formSubmit() {
        
            ajaxPost(formAction, formData, function(data){
                if (refresh=='page') updateState();
                if (refresh=='dialog') {
                    var dialog = $('.ui-dialog').last().attr('id').substr(3);
                    var template = $('.ui-dialog').last().data('src');
                    buildTemplate(dialog, template);
                }
                if (autoClose) hideDialog(idnum);
                if (typeof fn === 'function') fn(data);
            });
        }
        
        if (confirm==false) {
            formSubmit();
        } else {
            if (confirm!==true) window.Sebble.App.confirmMsg = confirm;
            else window.Sebble.App.confirmMsg = 'Are you sure?';
            showDialog('confirm');
            var idnum = $('.ui-dialog').last().attr('id').substr(9);
            $('.ui-dialog').last().find('input[type="submit"]').click(function(){
                hideDialog(idnum);
                formSubmit();
            });
        }
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
    
    var re_hash = /#\!\/([a-z0-9-_]+)\/([a-z0-9-_]+)(\?([\w\W]+))?/i;
    var m = location.hash.match(re_hash);
    if (m!==null && m.length >= 3) {
        var group = m[1];
        var page = m[2];
        
        loadPage(group, page);
        
        if (m[4]) {
            $('.dataTables_filter input').val(unescape(m[4]));
            window.Sebble.DataTables.mytable.fnFilter(unescape(m[4]));
        }
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
          window.Sebble.DataTables = {};
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
          
          //ajaxifyAll();
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
