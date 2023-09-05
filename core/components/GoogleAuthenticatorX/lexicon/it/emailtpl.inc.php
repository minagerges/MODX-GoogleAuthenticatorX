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
$_lang['gax.emailsuccess'] = 'E-mail inviata con successo.';
$_lang['gax.emailfail'] = 'Invio fallito.';

$_lang['gax.qremail_subject'] = 'Il tuo QR-code Google Authenticator Modx';
$_lang['gax.qremail_body'] = '<p>Ciao [[+username]],</p><p>Per utilizzare Google Authenticator hai bisogno di un dispositivo supportato, (Android, iPhone, BlackBerry).'
. 'Per alcuni dispositivi potresti avere bisogno anche di un QR-code scanner. Apri Google Authenticator '
. 'e fai la scansione del QR-code qui sotto; una volta completata la scansione con successo, vedrai una chiave di autenticazione che cambia ogni 30 secondi.'
. 'Questa è la chiave richiesta al login.</p>'
. '<br/> <img src="[[+qrurl]]"></body></html>';

$_lang['gax.notifyemail_subject'] = 'E\' stata attivata l\'autenticazione a 2 fattori per il tuo account Modx';
$_lang['gax.notifyemail_body'] = '<p>Ciao [[+username]],</p><p>Hai ricevuto questo messaggio perché è stata attivata l\'autenticazione a 2 fattori per il tuo account Modx. 
		  Per utilizzare Google Authenticator hai bisogno di un dispositivo supportato, (Android, iPhone, BlackBerry). Per alcuni dispositivi potresti avere bisogno anche di un QR-code scanner.
		  Avrai la possibilità di fare login senza chiave di autenticazione una sola volta; una volta fatto login, potrai fare la scansione del QR-code e verrai disconnesso.</p>
		  <p>Apri Google Authenticator e fai la scansione del QR-code fornito; una volta completata la scansione con successo, vedrai una chiave di autenticazione che cambia ogni 30 secondi. Questa è la chiave richiesta quando fai login.</p>
		  <p><br/><strong>NB. Il QR-code sara visibile solamente per 60 secondi.</strong></p><hr/>
		  <div style="margin-top: 0px;">
		  <h3>Istruzioni step by step</h3>
		  <h4>Requisiti</h4>

		  <p>Per utilizzare Google Authenticator sul tuo dispositivo Android, è necessaria una versione di Android uguale o maggiore della 2.1.</p>
		  <p>Per utilizzare Google Authenticator sul tuo iPhone, iPod Touch, o iPad, è necessaria una versione di iOS uguale o maggiore della 5.0. Inoltre, per poter installare l\'app sul tuo iPhone utilizzando un QR code, è necessario avere un modello 3G o superiore.</p>
		  <p>Per utilizzare Google Authenticator sul tuo dispositivo BlackBerry, è necessaria una versione OS compresa tra 4.5 e 7-0. Inoltre, assicurati che il tuo dispositivo BlackBerry sia configurato per Inglese US.</p>

		  <h4>Scaricare l\'app</h4>

		  <ol><strong>Per dispositivi Android:</strong>
			<li>Vai su <a href="//play.google.com/">Google Play</a>.</li>
			<li>Cerca <strong>Google Authenticator</strong>.</li>
			<li>Scarica e installa l\'applicazione.</li>
		  </ol>
		  
		  <ol><strong>Per iPhone, iPad e altri dispositivi iOS</strong>
			<li>Visit l\'App Store.</li>
			<li>Cerca <strong>Google Authenticator</strong>.</li>
			<li>Scarica e installa l\'applicazione.</li>
		  </ol>
		  
		  <ol><strong>Per dispositivi BlackBerry</strong>
			<li>Apri il browser sul tuo BlackBerry.</li>
			<li>Vai su m.google.com/authenticator.</li>
			<li>Scarica e installa l\'applicazione.</li>
		  </ol>

		  <h4>Configurare l\'app</h4>

		  <ol>
			<li>Sul tuo dispositivo, apri Google Authenticator.</li>
			<li>Se è la prima volta che utilizzi Authenticator, clicca il pulsante <strong>Add an account</strong>. Se stai aggiungendo un nuovo account, seleziona “Add an account” dal menu.</li>
			<li>Per collegare il tuo dispositivo al tuo account:
			  <ul>
				<li><strong>Con un QR code</strong>: Seleziona <strong>Scan a QR code</strong>. Se Authenticator non riesce a trovare un barcode scanner app sul tuo dispositivo, ti potrebbe essere richiesto di installarne uno. Se desideri installare un barcode scanner app sul tuo dispositivo, clicca <strong>Install</strong> e concludi l\'installazione. Una volta che l\'app è installata, riapri Google Authenticator e inquadra il QR code sullo schermo del tuo computer.</li>
				<li><strong>Con la secret key</strong>: Seleziona <strong>Enter a setup key</strong> e inserisci l\'indirizzo e-mail del tuo account Google nel campo <strong>Account</strong>. Successivamente, inserisci la secret key nel campo <strong>Key</strong>. Assicurati di aver selezionato di rendere la key <strong>Time based</strong> e clicca su "Add."</li>
			  </ul>
			  <br>
		  </ol>
		</div>';