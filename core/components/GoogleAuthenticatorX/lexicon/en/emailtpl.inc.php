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

$_lang['gax.qremail_subject'] = 'Your Google Authenticator QR-code';
$_lang['gax.qremail_body'] = '<p>Hello [[+username]],</p><p>To use Google Authenticator you need a supported mobile device, (Android, iPhone, BlackBerry)'
        . 'to run the Google Authenticator application. For some devices you might need a QR-code scanner as well. Open Google Authenticator application '
        . 'and scan the below QR-code, upon the success of this process you will see an authentication key changing every 30 seconds. '
        . 'This is the key required during your login.</p>'
        . '<br/> <img src="[[+qrurl]]"></body></html>';

$_lang['gax.notifyemail_subject'] = 'Your Google Authenticator QR-code';
$_lang['gax.notifyemail_body'] = '<p>Hello [[+username]],</p><p>You are recieving this email because Google Authenticator 2-step verification is enabled for your account. '
        . 'To use Google Authenticator need a supported mobile device, (Android, iPhone, BlackBerry)'
        . 'to run the Google Authenticator application. For some devices you might need a QR-code scanner as well. '
        . 'You will have only one successful courtesy login without authentication key requirment, '
        . 'upon successful login you will only be able to get your QR-code and logged out instantly'
        . 'Open Google Authenticator application '
        . 'and scan the provided QR-code, upon the success of this process you will see an authentication key changing every 30 seconds. '
        . 'This is the key required during your login.'
        . '<br/><strong>NB. QR-code will be visible on screen for one minutes only.</strong></p>';

$_lang['gax.emailsuccess'] = 'Email sent successfully.';
$_lang['gax.emailfail'] = 'Sending email failed.';