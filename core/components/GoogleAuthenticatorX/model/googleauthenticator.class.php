<?php
/* 
  *  GoogleAuthenticatorX
  * 
  *  Copyright 2014 by Mina Gerges <mina@minagerges.com>
  * 
  *  GoogleAuthenticatorX is free software; you can redistribute it and/or modify it under the
  *  terms of the GNU General Public License as published by the Free Software
  *  Foundation; either version 2 of the License, or (at your option) any later
  *  version.
  * 
  *  GoogleAuthenticatorX is distributed in the hope that it will be useful, but WITHOUT ANY
  *  WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
  *  A PARTICULAR PURPOSE. See the GNU General Public License for more details.
  * 
  *  You should have received a copy of the GNU General Public License along with
  *  WipeCache; if not, write to the Free Software Foundation, Inc., 59 Temple
  *  Place, Suite 330, Boston, MA 02111-1307 USA
 */
class GAx {
    public $userGAdisabled = false;
    public $UserExist = false;
    public $UserInCourtesy = false;
    public $UserName, $UserID;

    private $ga   = '';
    private $modx = '';
    
    private $user = array();
    private $GAusrSettings = array();

    private $cipherMethod   = 'AES-256-CBC';
    private $cipherOptions  = OPENSSL_RAW_DATA & OPENSSL_ZERO_PADDING;
    private $encryptionKey, $userIV;
    
    function __construct(modX &$modx) {
        $this->modx =& $modx;
        require_once dirname(__FILE__) . '/GoogleAuthenticatorX.helper.php';
        $this->ga = new PHPGangsta_GoogleAuthenticator();
        $this->modx->getService('lexicon','modLexicon'); 
        $this->modx->lexicon->load('GoogleAuthenticatorX:default');
        $encryptionKey = $this->modx->getOption('gax_encrypt_key');
        if($encryptionKey && preg_match('/^[0-9A-Fa-f]{64}$/', $encryptionKey)) {
            $this->encryptionKey = $encryptionKey;
        }
        else {
            $this->log('error', 'Invalid encryption key returned by "getOption", validating global setting...');
            $this->validateEncryptionKey();
        }
    }
    
    public function UserDisabled(){
        if ($this->GAusrSettings['gadisabled'] == true) {
            return true;
        }
        else {
            return false;
        }
    }
    
    public function UserCodeMatch($EnteredCode){
        $secret = $this->GAusrSettings['secret'];
        if ($secret) { //split to no user found and secret not found
            $code = $this->ga->getCode($secret); // Recalculated for logging
            if ($this->ga->verifyCode($secret, $EnteredCode, 2)) {
                return true;
            }
            else {
                $msg = "key mismatch user:({$this->UserName}) id:{$this->UserID}". " - entered:$EnteredCode expected:$code"; 
                $this->log(warn, $msg);
                return false;
            }
        }
        else if(!$this->user) { //No user just return true to get user not found error from MODx
            return true;
        }
        else if(!$secret || !$this->ValidSecret($secret)){ //secret is not set or invalid
            $this->resetsecret();
            $msg = "missing or invalid secret for user:({$this->UserName}) id:{$this->UserID}";
            $this->log(error, $msg);
            return false; 
        }
    }
    
    public function resetsecret() {
        $profile = $this->user->getOne('Profile');
        $extended = $profile->get('extended');
        if($extended['GoogleAuthenticatorX']){
            $extended['GoogleAuthenticatorX']['Settings'] = null;
            $profile->set('extended', $extended);
            $profile->save();
        }
        $this->CreateDefaultSettings();
        $this->SaveGAuserSettings();
        $msg = "Secret reset for user:({$this->UserName}) id:{$this->UserID}";
        $this->log(error, $msg);
    }
    
    public function LoadUserByID($userid){
        $user = $this->modx->getObject('modUser', $userid);
        if($user){
            $this->log(debug, "Loading user by ID:$userid");
            $this->user = $user;
            $this->UserExist = true;
            $this->UserName = $this->user->get('username');
            $this->UserID = $userid;
            $this->GetGAuserSettings();
            return true;
        }
        else{
            $this->log(warn, "No user was found with ID:$userid");
            return false;
        }
    }
    
    public function LoadUserByName($username){
        $user = $this->modx->getObject('modUser',array('username' => $username));
        if($user){
            $this->log(debug, "Loading user by name:($username)");
            $this->user = $user;
            $this->UserExist = true;
            $this->UserID = $this->user->get('id');
            $this->UserName = $username;
            $this->GetGAuserSettings();
            return true;
        }
        else{
            $this->log(warn, "No user was found with name:($username)");
            return false;
        }
    }
    
    public function GetDecryptedSettingsArray(){
        $gasettings = $this->GAusrSettings;
        $gasettings['gadisabled'] = $this->userGAdisabled;
        return $gasettings;
    }

    /**
     * populate GoogleAuthenticatorX extended field to $this->GAuserSettings array
     */
    private function GetGAuserSettings(){   //populate the GA extended field to $this->GAuserSettings array
        $profile = $this->user->getOne('Profile');
        $extended = $profile->get('extended');
        $gaSettings = null;
        if (is_array($extended) && array_key_exists('GoogleAuthenticatorX', $extended)){
            if(is_array($extended['GoogleAuthenticatorX']) && array_key_exists('Settings', $extended['GoogleAuthenticatorX']) ) {
                $gaSettings = $extended['GoogleAuthenticatorX']['Settings'];
            }
        }
        if (is_array($gaSettings)) { // extended field container in place, we load settings.
            $this->userIV = base64_decode($gaSettings['iv']);
            // Validate IV to avoid php warning
            if(strlen(bin2hex($this->userIV))/2 != 16) {
                $this->userIV = $this->generateIV();
                $this->log(error, "Invalid stored IV, for user:({$this->UserName}) id:{$this->UserID}");
            }
            $this->GAusrSettings = $this->GetDecryptedArray($gaSettings);
            $this->GAusrSettings['incourtesy'] = preg_replace( '/[^[:print:]]/', '',$this->GAusrSettings['incourtesy']); //fix issue with decrypted string
            if(!$this->ValidSecret($this->GAusrSettings['secret'])){
                $this->resetsecret();
                $this->log(error, "Invalid secret for user:({$this->UserName}) id:{$this->UserID}");
            }
            $this->userGAdisabled = $this->GetUserGAxStatus();
            $this->UserInCourtesy = $this->GetUserCourtesyStatus();
            $this->log(debug, "Data loaded for user:({$this->UserName}) id:{$this->UserID}");
        }
        else { // No setting for the user, we populate all defaults then save
            $this->log(error, "No Google Authenticator data were found for user:({$this->UserName}) id:{$this->UserID}");
            $this->CreateDefaultSettings();
            $this->SaveGAuserSettings();
        }

    }
    
    private function ValidSecret($secret){
        $valid = $this->ga->validsecret($secret);
        if(!$valid){
            $this->log(debug, "Not a valid secret:$secret for user:({$this->UserName}) id:{$this->UserID}");
            return false;
        }
        return true;
    }
    
    private function SaveGAuserSettings(){ //saves settings array to the extended field
        $profile = $this->user->getOne('Profile');
        $extended = $profile->get('extended');
        $extended['GoogleAuthenticatorX']['Settings'] = $this->GetEncryptedArray();
        $profile->set('extended', $extended);
        $profile->save();
        $this->log(debug, "Settings saved for user:({$this->UserName}) id:{$this->UserID}" );
    }
    
    private function CreateDefaultSettings(){
        $this->log(debug, "Creating new default settings for user:({$this->UserName}) id:{$this->UserID}" );
        $username = $this->user->get('username');
        
        $secret = $this->ga->createSecret();
        $issuer = $this->modx->getOption('gax_issuer', null, $this->modx->getOption('site_name'), true);
        $mgrURLalt = $this->modx->getOption('manager_login_url_alternate');
        $mgrURL = empty($mgrURLalt) ? 
                $this->modx->getOption('url_scheme').$this->modx->getOption('http_host').$this->modx->getOption('manager_url') : $mgrURLalt ;
        $accountname = $username . '::' . $mgrURL ;
        $uri    = $this->ga->getURI($accountname, $secret, $issuer);
        $QRurl  = $this->ga->getQRCodeGoogleUrl($accountname, $secret, $issuer);
        $this->userIV = $this->generateIV();
        $this->GAusrSettings = array (
            'incourtesy' => $this->IsCourtesyEnabled()? 'yes': 'no',
            'secret' => $secret,
            'uri'    => $uri,
            'qrurl'  => $QRurl,
            'iv'     => base64_encode($this->userIV),
        );
        $this->userGAdisabled = $this->GetUserGAxStatus();
        $this->UserInCourtesy = $this->GetUserCourtesyStatus()? true: false;
    }
    
    private function GetUserGAxStatus(){
        $usersettings = $this->user->getSettings();
        if(isset($usersettings['gax_disabled'])){
            $this->log(info, "gax_disabled usersetting loaded for user:({$this->UserName}) id:{$this->UserID}");
            return $usersettings['gax_disabled'];
        }
        else{
            return false;
        }
    }
    
    private function IsCourtesyEnabled(){
        $GlobalCourtesyStatus = $this->modx->getOption('gax_courtesy_enabled',null, false);
        $usersettings = $this->user->getSettings();
        if(isset($usersettings['gax_courtesy_enabled'])){
            $this->log(info, "gax_courtesy_enabled usersetting loaded with value {$usersettings['gax_courtesy_enabled']} for user:({$this->UserName}) id:{$this->UserID}");
            return $usersettings['gax_courtesy_enabled'];
        }
        else{
            $this->log(debug, "Applying Global Courtesy logging value:{$GlobalCourtesyStatus}");
            return $GlobalCourtesyStatus;
        }
    }
    
    private function GetUserCourtesyStatus(){
        if($this->IsCourtesyEnabled() && $this->GAusrSettings['incourtesy']=='yes'){
            $this->log(info, "User is in courtesy mode - user:({$this->UserName}) id:{$this->UserID}");
            return true;
        }
        else{
            return false;
        }
    }
 
    public function ResetCourtesy(){
        $this->log(info, "Resetting courtesy status - user:({$this->UserName}) id:{$this->UserID}");
        $this->GAusrSettings['incourtesy'] = 'no';
        $this->UserInCourtesy = false;
        $this->SaveGAuserSettings();
    }
  
    public function SetUserGAxDisabled($status = false){
        $userid = $this->user->get('id');
        $object = $this->modx->getObject('modUserSetting', array('user' => $userid, 'key' => 'gax_disabled'));
        if ($object === null && $status) { //no user setting but status is true(GA disabled) then we create
            $this->log(info, "Creating gax_disabled userSetting - user:({$this->UserName}) id:{$this->UserID}");
            $object = $this->modx->newObject('modUserSetting');
            $object->set('user', $userid);
            $object->set('key', 'gax_disabled');
            $object->set('value', $status);
            $object->set('xtype', 'combo-boolean');
            $object->set('namespace', 'GoogleAuthenticatorX');
            //$object->set('area', 'GoogleAuthenticatorX');
            $object->save();
        }
        else if($object !== null && $object->get('value') != $status){ //user setting exists but status changing we just change it
            $this->log(info, "Changing gax_disabled userSetting to:($status) - user:({$this->UserName}) id:{$this->UserID}");
            $object->set('value', $status);
            $object->save();
        }
        $this->userGAdisabled = $status;
    }
    
    private function GetEncryptedArray(){
        $EncryptedSettings = array();
        $EncryptedSettings['incourtesy'] = $this->encrypt($this->GAusrSettings['incourtesy']);
        $EncryptedSettings['secret'] = $this->encrypt($this->GAusrSettings['secret']);
        $EncryptedSettings['uri']    = $this->encrypt($this->GAusrSettings['uri']);
        $EncryptedSettings['qrurl']  = $this->encrypt($this->GAusrSettings['qrurl']);
        $EncryptedSettings['iv']     = base64_encode($this->userIV);
        return $EncryptedSettings;
    }
    
    private function GetDecryptedArray($CypheredArray){
        $DecryptedArray = array();
        $DecryptedArray['incourtesy']   = $this->decrypt($CypheredArray['incourtesy']);
        $DecryptedArray['secret']       = $this->decrypt($CypheredArray['secret']);
        $DecryptedArray['uri']          = $this->decrypt($CypheredArray['uri']);
        $DecryptedArray['qrurl']        = $this->decrypt($CypheredArray['qrurl']);
        $DecryptedArray['iv']           = $CypheredArray['iv'];
        return $DecryptedArray;
    }
    
    private function encrypt($plainText) {
        return openssl_encrypt($plainText, $this->cipherMethod, $this->encryptionKey, $this->cipherOptions, $this->userIV);
    }
    
    private function decrypt($cypheredString) {
        return openssl_decrypt($cypheredString, $this->cipherMethod, $this->encryptionKey, $this->cipherOptions, $this->userIV);
    }

    private function validateEncryptionKey($overwriteValid = false) {
        $encryptionKey_setting =  $this->modx->getObject('modSystemSetting', array('key' => 'gax_encrypt_key'));
        if(!$encryptionKey_setting) {
            $encryptionKey_setting = $this->modx->newObject('modSystemSetting');
            $encryptionKey_setting->set('key', 'gax_encrypt_key');
            $encryptionKey_setting_arr = array(
                'value' => bin2hex(openssl_random_pseudo_bytes(32)),
                'xtype' => 'text-password',
                'namespace' => 'GoogleAuthenticatorX',
                'area' => 'Default'
            );
            $encryptionKey_setting->fromArray($encryptionKey_setting_arr);
            $encryptionKey_setting->save();
            $this->log(error, 'Created encryption key in system settings!');
        }
        if(!preg_match('/^[0-9A-Fa-f]{64}$/', $encryptionKey_setting->get('value'))) {
            $encryptionKey_setting->set('value', hash('sha256', $this->modx->uuid));
            $encryptionKey_setting->save();
            $this->log(error, 'Invalid encryption key in system settings! Value was reset.');
        }
        $this->encryptionKey = $encryptionKey_setting->get('value');
    }

    private function generateIV() {
        $ivlen = openssl_cipher_iv_length($this->cipherMethod);
        return openssl_random_pseudo_bytes($ivlen);
    }

    private function log ($loglevel, $msg){
        switch($loglevel){
            case 'info':
                $this->modx->log(modX::LOG_LEVEL_INFO, $msg,'','GoogleAuthenticatorX','-','-');
                break;
            case 'warn':
                $this->modx->log(modX::LOG_LEVEL_WARN, $msg,'','GoogleAuthenticatorX','-','-');
                break;
            case 'error':
                $this->modx->log(modX::LOG_LEVEL_ERROR, $msg,'','GoogleAuthenticatorX','-','-');
                break;
            case 'debug':
                $this->modx->log(modX::LOG_LEVEL_DEBUG, $msg,'','GoogleAuthenticatorX','-','-');
                break;
            default:
                $this->modx->log(modX::LOG_LEVEL_INFO, $msg,'','GoogleAuthenticatorX','-','-');
                break;
        }
    }
    
}
