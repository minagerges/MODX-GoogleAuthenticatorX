var username = MODx.user.username;
var btn = '<span class="x-btn x-btn-small x-btn-icon-small-left primary-button x-btn-noicon" unselectable="on" style="width: auto;">'
         +'<button type="button" class="x-btn-text" ';
Ext.onReady(function() {
    MODx.addTab("modx-user-tabs",{
        title: 'GoogleAuthenticatorX',
        items: [{
                html: '<h3>'+ _('gax.hello') +' ' + username +'!</h3>'
                        + '<p>' + _('gax.usertab_desc') +'</p>',
                border: false,
                bodyCssClass: 'panel-desc',
                width: '100%'
            },{ xtype: 'panel',
                html: '<br/><h2>GoogleAuthenticatorX</h2><p>' + _('gax.desc') +'</p> <hr />',
                width: '100%'
            },{ html: btn + 'onclick="PopulateData()">' + _('gax.btn_showsecret') +'</button></span>&nbsp;&nbsp;&nbsp;'
                    + btn + 'onclick="ChangeGAxStatus()" id="chng">' + _('gax.btn_changestatus') +'</button></span>&nbsp;&nbsp;&nbsp;'
                    + btn + 'onclick="ResetSecret()">' + _('gax.btn_resetsecret') +'</button></span>&nbsp;&nbsp;&nbsp;'
                    + btn + 'onclick="eMailInstruct()">' + _('gax.btn_emailinstruct') +'</button></span>&nbsp;&nbsp;&nbsp;'
                    + btn + 'onclick="eMailQR()">' + _('gax.btn_emailqr') +'</button></span>'
                    + '</span><br/><hr/>'
                ,width: '95%'
            },{ html: '<div class="x-form-item x-tab-item">'
                    + '<div class="x-form-element">'
                    + '<label class="x-form-item-label" style="width:70px;">' + _('gax.lbl_status') +': </label>'
                    + '<input type="text" id="gax_disabled" style="width:80%;" value=" - " class="x-form-text x-form-field" readonly/>'
                    + '<br/><br/>'
                    + '<label class="x-form-item-label" style="width:70px;">' + _('gax.lbl_secret') +': </label>'
                    + '<input type="text" id="gax_secret" style="width:80%;" value=" - " class="x-form-text x-form-field" readonly/>'
                    + '<br/><br/>'
                    + '<label class="x-form-item-label" style="width:70px;">' + _('gax.lbl_uri') +': </label>'
                    + '<input type="text" id="gax_uri" style="width:80%;" value=" - " class="x-form-text x-form-field" readonly/>'
                    + '<hr/><label class="x-form-item-label" style="width:70px;">' + _('gax.lbl_qrcode') +': </label><div id="qrcode"><img id="qrimg" src=""/></div>'
                ,width: '95%' }] }); });

var connectorurl = MODx.config.assets_url + 'components/GoogleAuthenticatorX/connectors/connector.php';
PopulateData = function() { 
var sts = document.getElementById("gax_disabled"); 
MODx.Ajax.request({
    url: connectorurl,
    params:{
        action:'getusergasettings',
        id: MODx.request.id,
        admin: MODx.user.id
    },
    listeners:{
        'success':{fn:function(r){ // if user disabled change button to enable
                        //Ext.get('qrcode').update('<img  src="'+decodeURIComponent(r.object.qrurl)+'"/>');
                        document.getElementById("qrimg").src = r.object.qrurl;
                        document.getElementById("gax_secret").value = r.object.secret;
                        document.getElementById("gax_uri").value = decodeURIComponent(r.object.uri);
                        if(r.object.gadisabled == true){ 
                            document.getElementById("chng").textContent = _('gax.btn_enablegax');
                            sts.value = _('gax.disabled'); sts.style.color = sts.style.borderColor = 'red'; sts.style.fontWeight = 'bold'; }
                        if(r.object.gadisabled == false){ 
                            sts.value = _('gax.enabled');
                            sts.style.removeProperty('border-color');
                            sts.style.removeProperty('color');
                            sts.style.removeProperty('font-weight');
                            document.getElementById("chng").textContent = _('gax.btn_disablegax'); }
                     } , scope:this},
        'failure':{fn:function(){  },scope:this}}});};
ChangeGAxStatus = function(btn){if(btn=='yes'){_ChangeGAxStatus();}
    else if(btn!='no'){ShowMessage( _('gax.btn_changestatus_confirm'), ChangeGAxStatus);};};
_ChangeGAxStatus = function() {
var status = ' - '; //document.getElementById("gax_disabled").value == _('gax.enabled')? 'ENABLED' : ' - ';
if(document.getElementById("gax_disabled").value == _('gax.enabled')){status = 'ENABLED'}
else if(document.getElementById("gax_disabled").value == _('gax.disabled')){status = 'DISABLED'};
MODx.Ajax.request({
    url: connectorurl,
    params:{
        action:'changestatus',
        id: MODx.request.id,
        admin: MODx.user.id,
        status: status},
    listeners:{
        'success':{fn:function(){ PopulateData(); } , scope:this},
        'failure':{fn:function(){  },scope:this}}});};
ResetSecret = function(btn){if(btn=='yes'){_ResetSecret();}
    else if(btn!='no'){ShowMessage( _('gax.btn_resetsecret_confirm'), ResetSecret);};};
_ResetSecret = function() {
MODx.Ajax.request({
    url: connectorurl,
    params:{
        action:'resetsecret',
        id: MODx.request.id,
        admin: MODx.user.id},
    listeners:{
        'success':{fn:function(){ PopulateData(); } , scope:this},
        'failure':{fn:function(){  },scope:this}}});};

eMailInstruct = function(btn){if(btn=='yes'){_eMailInstruct();}
    else if(btn!='no'){ShowMessage( _('gax.btn_emailinstruct_confirm'), eMailInstruct);};};
_eMailInstruct = function() {
MODx.Ajax.request({
    url: connectorurl,
    params:{
        action:'emailinstructions',
        id: MODx.request.id,
        admin: MODx.user.id},
    listeners:{
        'success':{fn:function(r){ NotifyMessage(r.message) } , scope:this},
        'failure':{fn:function(){  },scope:this}}});};
ShowMessage = function(msg,f){
    Ext.MessageBox.show({
        title : 'GoogleAuthenticatorX',
        msg : msg,
        width : 400,
        buttons : Ext.MessageBox.YESNO,
        fn : f,
        icon : Ext.MessageBox.QUESTION
    });
};

eMailQR = function(btn){if(btn=='yes'){_eMailQR();}
    else if(btn!='no'){ShowMessage( _('gax.btn_emailqr_confirm'), eMailQR);};};
_eMailQR = function() {
MODx.Ajax.request({
    url: connectorurl,
    params:{
        action:'emailsecret',
        id: MODx.request.id,
        admin: MODx.user.id},
    listeners:{
        'success':{fn:function(r){ NotifyMessage(r.message) } , scope:this},
        'failure':{fn:function(){  },scope:this}}});};
ShowMessage = function(msg,f){
    Ext.MessageBox.show({
        title : 'GoogleAuthenticatorX',
        msg : msg,
        width : 500,
        buttons : Ext.MessageBox.YESNO,
        fn : f,
        icon : Ext.MessageBox.QUESTION
    });
};
NotifyMessage = function(msg){
    Ext.MessageBox.show({
        title : 'GoogleAuthenticatorX',
        msg : msg,
        width : 400,
        buttons : Ext.MessageBox.OK,
        icon : Ext.MessageBox.INFORMATION
    });
};