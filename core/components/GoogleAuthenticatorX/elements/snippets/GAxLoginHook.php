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
$props = $hook->getValues();
$username = $props['username'];
$gacode = $props['token'];
$modx->getService('lexicon','modLexicon');
$modx->lexicon->load('GoogleAuthenticatorX:default');
$errorMsg = $modx->lexicon('gax.invalidcode');
$failMessage = $modx->getOption('2FAerror', $scriptProperties, $errorMsg);

if($props['service'] != 'login'){
    return true;
}

if(empty($username)){
    return false;
}
if(empty($gacode)) {
    $errorMsg = $modx->lexicon('gax.enterkey');
    $hook->addError('user', $errorMsg);
    return false;
}

include_once $modx->getOption('core_path').'components/GoogleAuthenticatorX/model/googleauthenticator.class.php';
$GA = new GAx($modx);
$GA->LoadUserByName($username);
if($GA->UserCodeMatch($gacode)) {
    return true;
}
$hook->addError('user', $errorMsg);
return false;