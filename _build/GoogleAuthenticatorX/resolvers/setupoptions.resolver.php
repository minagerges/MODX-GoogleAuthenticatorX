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
        /*Validate requirements*/
        if(!meetsRequirements()){
            $modx->log(modX::LOG_LEVEL_ERROR,"Installation requirements not met: php version > 5.3 AND/OR mcrypt extension not loaded.");
            $success = false;
            break;
        }

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
        /* Validate requirements */
        if(!meetsRequirements()){
            $modx->log(modX::LOG_LEVEL_ERROR,"Installation requirements not met: php version > 5.3 AND/OR mcrypt extension not loaded.");
            $success = false;
            break;
        }

        /* Upgrade old cipher for older releases */
        //check previous version
        $legacyPackageExist = false;
        $response = $modx->runProcessor('workspace/packages/version/getlist', array('signature' => 'googleauthenticatorx') );
        if ($response->isError()) {
            $modx->log(modX::LOG_LEVEL_ERROR,"Failed to detect previous release, skipping user data migration.");
        }
        $legacyPackage = $modx->fromJSON($response->response)['results'];
        if(!empty($legacyPackage) && !empty($legacyPackage['1']) && is_array($legacyPackage['1']) ){
            $version = $legacyPackage['1']['version'];
            $release = $legacyPackage['1']['release'];
            $modx->log(modX::LOG_LEVEL_INFO, "previouse release: $version-$release");
            if($version == '1.0.0' && ($release == 'rc1' || $release == 'rc2') ) {
                $legacyPackageExist = true;
                $modx->log(modX::LOG_LEVEL_WARN,"Migration of user data required!");
            }
        }
        else{
            $modx->log(modX::LOG_LEVEL_INFO, "No legacy package was found.");
        }

        if($legacyPackageExist == true) {
            $modx->log(modX::LOG_LEVEL_WARN,"Previous version detected, re-encrypting data...");
            //Re-encrypt user data
            $mgrCtx = $modx->getObject('modContext', array('key' => 'mgr'));
            $users = $modx->getCollection('modUser');
            foreach ($users as $iuser) {
                if (checkPolicy('frames', $mgrCtx, $iuser)) {
                    $modx->log(modX::LOG_LEVEL_INFO,"Re-encrypting data for user: " . $iuser->get('username'));
                    $profile = $iuser->getOne('Profile');
                    $extended = $profile->get('extended');
                    if ($extended['GoogleAuthenticatorX']){
                        $gaSettings = $extended['GoogleAuthenticatorX']['Settings'];
                    }
                    if ($gaSettings) {
                        $uKey = $modx->uuid;
                        $incourtesy = legacyDecrypt($gaSettings['incourtesy'], $uKey);
                        $incourtesy = encrypt($incourtesy, $uKey);
                        $secret = legacyDecrypt($gaSettings['secret'], $uKey);
                        $secret = encrypt($secret, $uKey);
                        $uri = legacyDecrypt($gaSettings['uri'], $uKey);
                        $uri = encrypt($uri, $uKey);
                        $QRurl = legacyDecrypt($gaSettings['qrurl'], $uKey);
                        $QRurl = encrypt($QRurl, $uKey);

                        $newData = array(
                            'incourtesy' => $incourtesy,
                            'secret' => $secret,
                            'uri'    => $uri,
                            'qrurl'  => $QRurl
                        );
                        $extended['GoogleAuthenticatorX']['Settings'] = $newData;
                        $profile->set('extended', $extended);
                        $profile->save();
                    }
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
    if (!extension_loaded('mcrypt')) {
        $result = false;
    }
    $php_ver_comp = version_compare(phpversion(), '5.3.0');
    if ($php_ver_comp < 0) {
        $result = false;
    }
    return $result;
}

function legacyDecrypt($Cyphered, $key){
    $Cyphered = base64_decode($Cyphered);
    $liv_size = mcrypt_get_iv_size(MCRYPT_BLOWFISH, MCRYPT_MODE_ECB);
    $liv = mcrypt_create_iv($liv_size, MCRYPT_RAND);
    $decrypted_string = mcrypt_decrypt(MCRYPT_BLOWFISH, $key, $Cyphered, MCRYPT_MODE_ECB, $liv);
    return $decrypted_string;
}

function encrypt($plainTXT, $key){
    $key = str_replace('-','',$key);
    $iv_size = mcrypt_get_iv_size(MCRYPT_RIJNDAEL_128, MCRYPT_MODE_CBC);
    $iv = mcrypt_create_iv($iv_size, MCRYPT_RAND);
    $encrypted_string = mcrypt_encrypt(MCRYPT_RIJNDAEL_128, $key, $plainTXT, MCRYPT_MODE_CBC, $iv);
    return base64_encode($iv . $encrypted_string);
}