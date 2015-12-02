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
$userid = $modx->getOption('userID', $scriptProperties, $modx->user->get('id'));
$size = $modx->getOption('size', $scriptProperties, '200');
if($userid == 0){
    return;
}
$placeHolder = $modx->getOption('toPlaceholder', $scriptProperties, 'gax');
include_once $modx->getOption('core_path').'components/GoogleAuthenticatorX/model/googleauthenticator.class.php';
$GA = new GAx($modx);
$GA->LoadUserByID($userid);
$gaSettings = $GA->GetDecryptedSettingsArray();
$qrCode = '';
if ($gaSettings) {
    $user = $modx->getObject('modUser', $userid);
    $username = $user->get('username');
    $accountname = $username . '::' . $modx->getOption('site_url') ;
    $secret = $gaSettings['secret'];
    $issuer = $modx->getOption('gax_issuer', null, $modx->getOption('site_name'));
    if(empty($issuer)){ $issuer = $modx->getOption('site_url'); }
    $uri = 'otpauth://totp/'.$accountname.'?secret='.$secret.'&issuer='.$issuer;
    $urlencoded = urlencode($uri);
    $imgPath = 'https://chart.googleapis.com/chart?chs='.$size.'x'.$size.'&chld=M|0&cht=qr&chl='.$urlencoded;

    $modx->setPlaceholders(array('secret'=>$secret, 'uri'=>$uri, 'qrCode'=> $imgPath), $placeHolder . '.');
}
return;