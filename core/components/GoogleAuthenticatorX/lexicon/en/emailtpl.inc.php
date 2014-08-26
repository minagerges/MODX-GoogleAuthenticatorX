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
$_lang['gax.emailsuccess'] = 'Email sent successfully.';
$_lang['gax.emailfail'] = 'Sending email failed.';

$_lang['gax.qremail_subject'] = 'Your Google Authenticator QR-code';
$_lang['gax.qremail_body'] = '<p>Hello [[+username]],</p><p>To use Google Authenticator you need a supported mobile device, (Android, iPhone, BlackBerry)'
        . 'to run the Google Authenticator application. For some devices you might need a QR-code scanner as well. Open Google Authenticator application '
        . 'and scan the below QR-code, upon the success of this process you will see an authentication key changing every 30 seconds. '
        . 'This is the key required during your login.</p>'
        . '<br/> <img src="[[+qrurl]]"></body></html>';

$_lang['gax.notifyemail_subject'] = '2-step verification login has been activated';
$_lang['gax.notifyemail_body'] = '<p>Hello [[+username]],</p><p>You are recieving this email because Google Authenticator 2-step verification is enabled for your account. 
		  To use Google Authenticator need a supported mobile device, (Android, iPhone, BlackBerry) to run the Google Authenticator application. For some devices you might need a QR-code scanner as well. 
		  You will have only one successful courtesy login without authentication key requirment, upon successful login you will only be able to get your QR-code and logged out instantly.</p>
		  <p>Open Google Authenticator application and scan the provided QR-code, upon the success of this process you will see an authentication key changing every 30 seconds. This is the key required during your login.</p>
		  <p><br/><strong>NB. QR-code will be visible on screen for 60 seconds only.</strong></p><hr/>
		  <div style="margin-top: 0px;">
		  <h3>Step by step instructions</h3>
		  <h4>Requirements</h4>

		  <p>To use Google Authenticator on your Android device, it must be running Android version 2.1 or later.</p>
		  <p>To use Google Authenticator on your iPhone, iPod Touch, or iPad, you must have iOS 5.0 or later. In addition, in order to set up the app on your iPhone using a QR code, you must have a 3G model or later.</p>
		  <p>To use Google Authenticator on your BlackBerry device, you must have OS 4.5-7.0. In addition, make sure your BlackBerry device is configured for US English -- you might not be able to download Google Authenticator if your device is operating in another language.</p>

		  <h4>Downloading the app</h4>

		  <ol><strong>For Android devices:</strong>
			<li>Visit <a href="//play.google.com/">Google Play</a>.</li>
			<li>Search for <strong>Google Authenticator</strong>.</li>
			<li>Download and install the application.</li>
		  </ol>
		  
		  <ol><strong>For iPhone, iPad & other iOS devices</strong>
			<li>Visit the App Store.</li>
			<li>Search for <strong>Google Authenticator</strong>.</li>
			<li>Download and install the application.</li>
		  </ol>
		  
		  <ol><strong>For BlackBerry devices</strong>
			<li>Open the web browser on your BlackBerry.</li>
			<li>Visit m.google.com/authenticator.</li>
			<li>Download and install the application.</li>
		  </ol>

		  <h4>Setting up the app</h4>

		  <ol>
			<li>On your phone, open the Google Authenticator application.</li>
			<li>If this is the first time you have used Authenticator, click the <strong>Add an account</strong> button. If you are adding a new account, choose “Add an account” from the app’s menu.</li>
			<li>To link your phone to your account:
			  <ul>
				<li><strong>Using QR code</strong>: Select <strong>Scan account barcode</strong>. If the Authenticator app cannot locate a barcode scanner app on your phone, you might be prompted to download and install one. If you want to install a barcode scanner app so you can complete the setup process, press <strong>Install</strong> then go through the installation process. Once the app is installed, reopen Google Authenticator, point your camera at the QR code on your computer screen.</li>
				<li><strong>Using secret key</strong>: Select <strong>Manually add account</strong>, then enter the email address of your Google Account in the box next to <strong>Enter account name</strong>. Next, enter the secret key on your computer screen into the box under <strong>Enter key</strong>. Make sure you\'ve chosen to make the key <strong>Time based</strong> and press "Save."</li>
			  </ul>
			  <br>
		  </ol>
		</div>';