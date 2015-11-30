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
$_lang['GoogleAuthenticatorX.gax'] = 'GoogleAuthenticatorX';

$_lang['gax'] = 'GoogleAuthenticatorX';
$_lang['gax.desc'] = 'Add 2-step verification to MODX manager login.';
$_lang['gax.description'] = 'Description';

$_lang['setting_gax_disabled'] = 'Disable 2-factor authentication';
$_lang['setting_gax_disabled_desc'] = 'Disabling 2-factor authentication will revert manager login back to default.';
$_lang['setting_gax_courtesy_enabled'] = 'Allow Courtesy login';
$_lang['setting_gax_courtesy_enabled_desc'] = 'Allowing courtesy login will enable users to have a 1 time login to retrieve their secret.';
$_lang['setting_gax_profile_enabled'] = 'Show secret in users profile';
$_lang['setting_gax_profile_enabled_desc'] = 'Allow users to see Google Authentication secret in their manager user profile.';
$_lang['setting_gax_issuer'] = 'QR-code issuer value';
$_lang['setting_gax_issuer_desc'] = 'Set the QR-code issuer value, default to site_name';

$_lang['gax.authkey'] = 'Authentication Key';
$_lang['gax.enterkey'] = 'Please enter Authentication key!';
$_lang['gax.invalidformat'] = 'Invlid authentication key format, expecting 6 digits!';
$_lang['gax.invalidcode'] = 'Invalid authentication key.';

$_lang['gax.hello'] = 'Hello';
$_lang['gax.usertab_desc'] = 'Here you can manage GoogleAuthenticatorX  <i>(Google Authenticator 2-factor authentication)</i> user specific tasks.';
$_lang['gax.enabled'] = 'Enabled';
$_lang['gax.disabled'] = 'Disabled';
$_lang['gax.lbl_status'] = 'Status';
$_lang['gax.lbl_secret'] = 'Secret';
$_lang['gax.lbl_uri'] = 'URI';
$_lang['gax.lbl_qrcode'] = 'QR-code';
$_lang['gax.btn_showsecret'] = 'Show secret';
$_lang['gax.btn_changestatus'] = 'Change Status';
$_lang['gax.btn_changestatus_confirm'] = 'Are you sure you want to change Google Authenticator status for this user?';
$_lang['gax.btn_enablegax'] = 'Enable Google Authenticator';
$_lang['gax.btn_disablegax'] = 'Disable Google Authenticator';
$_lang['gax.clkfrst'] = 'Please click "Show secret" first!';
$_lang['gax.btn_resetsecret'] = 'Reset Secret';
$_lang['gax.btn_resetsecret_confirm'] = 'Are you sure you want to reset Google Authenticator user secret? You will have to provide new key to user!';
$_lang['gax.btn_emailinstruct'] = 'Email instructions to user';
$_lang['gax.btn_emailinstruct_confirm'] = 'Are you sure you want to send instructions email to this user?';
$_lang['gax.btn_emailqr'] = 'Email QR-code to user';
$_lang['gax.btn_emailqr_confirm'] = 'Are you sure you want to email Google Authenticator secret to this user? This is the worst way to provide user secret!';

$_lang['gax.courtesy_notification'] = 'This is a Courtesy login, follow the instructions provided by your administrator to be able to perform future login.';
$_lang['gax.courtesy_qrfailed'] = 'Failed to retrieve QR-code';



$_lang['gax.confirm_resetsecret'] = 'Are you sure you want to reset Google Authenticator user secret? You will have to provide the new key to user!';