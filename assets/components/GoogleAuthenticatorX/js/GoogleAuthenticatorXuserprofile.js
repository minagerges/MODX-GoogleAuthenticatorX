Ext.onReady(function() {
var div = document.getElementById('modx-panel-profile-update');
div.innerHTML = div.innerHTML + '<div class="x-form-item x-tab-item x-form-element">'
        + '<label for="qrcode" style="width:auto;" class="x-form-item-label">Google Authentication QR-code:\n\</label></div>'
        + '<div id="qrcode"><img id="qrimg" src=""></div>';
    MODx.Ajax.request({
    url: MODx.config.assets_url + 'components/GoogleAuthenticatorX/connectors/connector.php',
    params:{action:'getuserqrcode'},
    listeners:{
        'success':{fn:function(r){ document.getElementById("qrimg").src = r.object.qrurl;},scope:this },
        'failure':{fn:function(){  },scope:this}}});
});