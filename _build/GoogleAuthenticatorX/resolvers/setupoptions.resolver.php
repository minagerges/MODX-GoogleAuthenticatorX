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
    require_once dirname(dirname(dirname(dirname(dirname(__FILE__))))) . '/revolution/config.core.php';
    require_once MODX_CORE_PATH . 'model/modx/modx.class.php';
    $modx= new modX();
    $modx->initialize('web');
    $modx->setLogLevel(modX::LOG_LEVEL_INFO);
    $modx->setLogTarget('ECHO');
}
 
$success= false;
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
        if($options['sendnotifymail']){
            $modx->log(modX::LOG_LEVEL_WARN,'Started sending emails...');
            $recepients = array();
            
            /* Get UserGroups with mgr access and send to them emails NOT TESTED*/
            $mgrGroups = $modx->getObject('modAccessContext', array('target'=>'mgr'));
            $users = $modx->getCollection('modUser');
            foreach($iuser as $users){
                $iuserGroups =  $iuser->getUserGroups();
                if($iuser->isMember($mgrGroups)){
                    $iuser->sendEmail($body, array('subject'=>$subject));
                }
            }
        }
        
        $modx->log(modX::LOG_LEVEL_INFO,'Started sending emails...');
        $modx->log(modX::LOG_LEVEL_ERROR,'Starting sending emails...');
         $object->xpdo->log(xPDO::LOG_LEVEL_ERROR,'[GAx]  setting could not be found, so the setting could not be changed.');
        $modx->log(modX::LOG_LEVEL_WARN,'[GoogleAuthenticatorX] Remember to configure your own account\'s Google Authenticator before enabling 2-factor Authentication');
        $modx->log(modX::LOG_LEVEL_ERROR,"Options: mail? {$options['sendnotifymail']}  Courtesy?{$options['enablecourtesy']}");
        $success = true;
        break;
    
    case xPDOTransport::ACTION_UPGRADE:
    case xPDOTransport::ACTION_UNINSTALL:
        $success= true;
        break;
}
return $success;