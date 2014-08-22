Ext.onReady(function() {
    getuserqrcode();
    CourtesyWindowFn();
    setTimeout(function(){location.href = './';}, 60000);
});
var getuserqrcode = function(){
    MODx.Ajax.request({
    url: MODx.config.assets_url + 'components/GoogleAuthenticatorX/connectors/connector.php',
    params:{action:'getuserqrcode'},
    listeners:{
        'success':{fn:function(r){ document.getElementById("qrimg").src = r.object.qrurl; forcedlogout();},scope:this },
        'failure':{fn:function(){ forcedlogout(); },scope:this}}});
};
var forcedlogout = function(){
    MODx.fireEvent('beforeLogout');
    MODx.Ajax.request({
    url: MODx.config.connector_url,
    params:{
        action:'security/logout',
       login_context: 'mgr'},
    listeners:{
        'success':{fn:function(r){ MODx.fireEvent('afterLogout'); } , scope:this},
        'failure':{fn:function( ){ },scope:this}}});
};
var CourtesyWindowFn = function(){
    var CourtesyWindow = new MODx.Window({
        title: _('gax'),
		labelWidth: 150,
		closable: false,
		collapsible: false,
		maximizable: false,
		modal: true,
        width: 450,
        height: 500,
		fields: [{
			html: '<p>' + _('gax.courtesy_notification') + '</p><hr/>'
                            + '<img id="qrimg" src=""/>'
		}],
            buttons: [{
			text: _('logout'),
			handler: function() {
                            MODx.logout();
                        }
                    }]
    });
    CourtesyWindow.show(Ext.getBody());
};