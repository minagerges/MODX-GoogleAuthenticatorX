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
if (isset($object) && isset($object->xpdo)) {
    $modx = $object->xpdo;
}
if (!isset($modx)) {
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/config.core.php';
    require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
    $modx= new modX();
    $modx->initialize('web');
    $modx->setLogLevel(modX::LOG_LEVEL_INFO);
    $modx->setLogTarget('ECHO');
}
 
$success= false;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        $modx->log(modX::LOG_LEVEL_INFO, "xPDOTransport::ACTION_INSTALL");
        /*Validate requirements*/
        if(!meetsRequirements()){
            $modx->log(modX::LOG_LEVEL_ERROR,"Installation requirements not met: php version > 5.4 AND/OR OpenSSL encryption/decryption failed.");
            $success = false;
            break;
        }

        setEncrptKeySetting();

        if($options['sendnotifymail']){ /* Email all users with manager access */
            $modx->getService('lexicon','modLexicon');
            $modx->log(modX::LOG_LEVEL_WARN,'Started sending emails to users with manager access...');
            $mgrCtx = $modx->getObject('modContext', array('key' => 'mgr'));
            $subject = '2-step verification is enabled';
            $users = $modx->getCollection('modUser');
            foreach($users as $iuser){
                if(checkPolicy('frames', $mgrCtx, $iuser) ){
                    //Get body and subject for each user manager language
                    $mgrLanguage = $iuser->getOption('manager_language');
                    $modx->lexicon->load("GoogleAuthenticatorX:emailtpl", $mgrLanguage);
                    $subject = $modx->lexicon('gax.notifyemail_subject');
                    $body = $modx->lexicon('gax.notifyemail_body',array( 'username' => $iuser->get('username'),));
                    $body = '<html><body>'.$body.'</body></html>';
                    if($iuser->sendEmail($body, array('subject'=>$subject)) ){
                        $modx->log(modX::LOG_LEVEL_INFO,"Sent email to user:({$iuser->get('username')})  id:{$iuser->get('id')}");
                    }
                    else{
                        $modx->log(modX::LOG_LEVEL_WARN,"Sending email failed, user:({$iuser->get('username')})  id:{$iuser->get('id')}");
                    }
                }
            }
        }
        if($options['enablegax']){
            $Setting = $modx->getObject('modSystemSetting', 'gax_disabled');
            $Setting->set('value', 0);
            $Setting->save();
            $modx->log(modX::LOG_LEVEL_WARN,"Enabled 2-step verification");
            $modx->log(modX::LOG_LEVEL_INFO,"Refreshing system settings cache...");
            $cacheRefreshOptions =  array( 'system_settings' => array() );
            $modx->cacheManager-> refresh($cacheRefreshOptions);
            
        }
        
        $success = true;
        break;

    case xPDOTransport::ACTION_UPGRADE:
        $modx->log(modX::LOG_LEVEL_INFO, "xPDOTransport::ACTION_UPGRADE");
        /* Validate requirements */
        if(!meetsRequirements()){
            $modx->log(modX::LOG_LEVEL_ERROR,"Installation requirements not met: php version > 5.4 AND/OR OpenSSL encryption/decryption failed.");
            $success = false;
            break;
        }
        setEncrptKeySetting();

        /* Upgrade old cipher for older releases */
        //check previous version
        $legacyPackageExist = false;
        $response = $modx->runProcessor('workspace/packages/version/getlist', array('signature' => 'googleauthenticatorx') );
        if ($response->isError()) {
            $modx->log(modX::LOG_LEVEL_ERROR,"Failed to detect previous release, skipping user data migration.");
        }
        $legacyPackage = $modx->fromJSON($response->response)['results'];
        $pkgidx;
        if(!empty($legacyPackage['0'])) {
            if($legacyPackage['0']['installed']) {
                $pkgidx = 0;
            }
            else {
                $pkgidx = 1;
            }
        }
        $modx->log(modX::LOG_LEVEL_INFO, "Detected previous install idx[$pkgidx]: " . $legacyPackage[$pkgidx]['signature']);
        if(!empty($legacyPackage) && !empty($legacyPackage[$pkgidx]) && is_array($legacyPackage[$pkgidx]) ){
            $version_major = $legacyPackage[$pkgidx]['version_major'];
            $version_minor = $legacyPackage[$pkgidx]['version_minor'];
            if($version_major == 1 && $version_minor <= 2) {
                $modx->log(modX::LOG_LEVEL_WARN,"Migration of user data required!");
                $legacyPackageExist = true;
            }
        }
        else{
            $modx->log(modX::LOG_LEVEL_INFO, "No legacy package was found.");
        }

        if($legacyPackageExist == true) {
            $modx->log(modX::LOG_LEVEL_WARN,"Legacy version detected, re-encrypting data...");
            //Re-encrypt user data
            $encKeySetting = $modx->getObject('modSystemSetting', array('key' => 'gax_encrypt_key'));
            $encKey = $encKeySetting->get('value');
            $users = $modx->getCollection('modUser');
            foreach ($users as $iuser) {
                $modx->log(modX::LOG_LEVEL_INFO,"Validating data for user: " . $iuser->get('username'));
                $profile = $iuser->getOne('Profile');
                $extended = $profile->get('extended');
                if(is_array($extended) && array_key_exists('GoogleAuthenticatorX', $extended)){
                    if(is_array($extended['GoogleAuthenticatorX']) && array_key_exists('Settings', $extended['GoogleAuthenticatorX']) ) {
                        $modx->log(modX::LOG_LEVEL_INFO,"Re-encrypting data for user: " . $iuser->get('username'));
                        $gaSettings = $extended['GoogleAuthenticatorX']['Settings'];
                        $IV = openssl_random_pseudo_bytes(openssl_cipher_iv_length('AES-256-CBC'));
                        $incourtesy = mcryptDecrypt($gaSettings['incourtesy']);
                        $incourtesy = opensslEncrypt($incourtesy, $encKey, $IV);
                        $secret = mcryptDecrypt($gaSettings['secret']);
                        if(strlen($secret) != 16) {
                            $modx->log(modX::LOG_LEVEL_ERROR,"Invalid 2FA secret, aborting re-encrypting for current user." );
                            continue;
                        }
                        $secret = opensslEncrypt($secret, $encKey, $IV);
                        $uri = mcryptDecrypt($gaSettings['uri']);
                        $uri = opensslEncrypt($uri, $encKey, $IV);
                        $QRurl = mcryptDecrypt($gaSettings['qrurl']);
                        $QRurl = opensslEncrypt($QRurl, $encKey, $IV);
                        $userIV = base64_encode($IV);

                        $newData = array(
                            'incourtesy' => $incourtesy,
                            'secret' => $secret,
                            'uri'    => $uri,
                            'qrurl'  => $QRurl,
                            'iv'     => $userIV,
                        );
                        $modx->log(modX::LOG_LEVEL_INFO,"Saving data..." );
                        $extended['GoogleAuthenticatorX']['Settings'] = $newData;
                        $profile->set('extended', $extended);
                        $profile->save();
                    }
                    else {
                        $modx->log(modX::LOG_LEVEL_WARN,"Invalid 2FA data, new set will be created on next load!");
                    }
                }
                else {
                    $modx->log(modX::LOG_LEVEL_WARN,"No GoogleAuthenticatorX data found, new set will be created on next load!");
                }
            }
        }
        $success = true;
        break;
    
    case xPDOTransport::ACTION_UNINSTALL:
        $success= true;
        break;
}
return $success;

/**
 * Determine is a user attributes satisfy an Object policy
 * 
 * @param array $criteria An associative array providing a key and value to
 * search for within the matched policy attributes between policy and
 * principal.
 * @param type $target A target modAccess class name
 * @param type $user
 * @return boolean
 **/
function checkPolicy($criteria, $target, $user) {
    if ($user->get('sudo')) return true;
    if (!is_array($criteria) && is_scalar($criteria)) {
        $criteria = array("{$criteria}" => true);
    }
    $policy = $target->findPolicy();
    if (true) {
        $principal = $user->getAttributes($target);
        if (!empty($principal)) {
            foreach ($policy as $policyAccess => $access) {
                foreach ($access as $targetId => $targetPolicy) {
                    foreach ($targetPolicy as $policyIndex => $applicablePolicy) {
                        $principalPolicyData = array();
                        $principalAuthority = 9999;
                        if (isset($principal[$policyAccess][$targetId]) && is_array($principal[$policyAccess][$targetId])) {
                            foreach ($principal[$policyAccess][$targetId] as $acl) {
                                $principalAuthority = intval($acl['authority']);
                                $principalPolicyData = $acl['policy'];
                                $principalId = $acl['principal'];
                                if ($applicablePolicy['principal'] == $principalId) {
                                    if ($principalAuthority <= $applicablePolicy['authority']) {
                                        if (!$applicablePolicy['policy']) {
                                            return true;
                                        }
                                        if (empty($principalPolicyData)) $principalPolicyData = array();
                                        $matches = array_intersect_assoc($principalPolicyData, $applicablePolicy['policy']);
                                        if ($matches) {
                                            $matched = array_diff_assoc($criteria, $matches);
                                             if (empty($matched)) {
                                                return true;
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        return false;
    }

}

function meetsRequirements(){
    $result = true;
    $php_ver_comp = version_compare(phpversion(), '5.4.0');
    if ($php_ver_comp < 0) {
        $result = false;
    }
    if (!function_exists('openssl_encrypt') || !function_exists('openssl_decrypt')) {
        $result = false;
    }
    return $result;
}

function mcryptDecrypt($Cyphered) {
    global $modx;
    $Cyphered = base64_decode($Cyphered);
    $encryption_key =  str_replace('-','', $modx->uuid);
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = substr($Cyphered, 0, $iv_size);
    $Cyphered = substr($Cyphered, $iv_size);
    $decrypted_string = mcrypt_decrypt(MCRYPT_RIJNDAEL_128, $encryption_key, $Cyphered, MCRYPT_MODE_CBC, $iv);
    return $decrypted_string;
}

function opensslEncrypt($plainText, $encryptionKey, $IV) {
    return openssl_encrypt($plainText, 'AES-256-CBC', $encryptionKey, OPENSSL_RAW_DATA & OPENSSL_ZERO_PADDING, $IV);
}

function setEncrptKeySetting() {
    global $modx;
    $gax_enckey_setting = $modx->getObject('modSystemSetting', array('key' => 'gax_encrypt_key'));
    if(!$gax_enckey_setting) {
        $modx->log(modX::LOG_LEVEL_WARN,"Generating Encryption Key into System Setting!");
        $gax_enckey_setting = $modx->newObject('modSystemSetting');
        $gax_enckey_setting->set('key', 'gax_encrypt_key');
        $gax_enckey_setting_settings = array(
            'value' => bin2hex(openssl_random_pseudo_bytes(32)),
            'xtype' => 'password',
            'namespace' => 'GoogleAuthenticatorX',
            'area' => 'Default'
        );
        $gax_enckey_setting->fromArray($gax_enckey_setting_settings);
        $gax_enckey_setting->save();
    }
    else if(!preg_match('/^[0-9A-Fa-f]{64}$/', $gax_enckey_setting->get('value'))) {
        $modx->log(modX::LOG_LEVEL_ERROR,"Invalid Encryption Key in System Setting, regenerating key!");
        $gax_enckey_setting->set('value', bin2hex(openssl_random_pseudo_bytes(32)));
        $gax_enckey_setting->save();
    }
}

