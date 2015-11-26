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
    case xPDOTransport::ACTION_UPGRADE:
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
    $policy = $target->findPolicy();//$this->findPolicy();
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