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
if(!$modx->user->hasSessionContext('mgr')){
    return $modx->error->failure( $modx->lexicon('permission_denied') );
}
else{
    $userid = $scriptProperties['id'];
    include_once $modx->getOption('core_path').'components/GoogleAuthenticatorX/model/googleauthenticator.class.php';
    $GA = new GAx($modx);
    $GA->LoadUserByID($userid);
    $gaSettings = $GA->GetDecryptedSettingsArray();
    if ($gaSettings) {
        $modx->lexicon->load('GoogleAuthenticatorX:emailtpl');
        $subject = $modx->lexicon('gax.notifyemail_subject');
        $body = $modx->lexicon('gax.notifyemail_body',array(   /*modify array according to template*/
            'username' => $GA->UserName,));
        $body = '<html><body>'.$body.'</body></html>';
        $user = $modx->getObject('modUser', $userid);
        if(!$user->sendEmail($body,array('subject' => $subject) )) {
            return $modx->error->failure($modx->lexicon('gax.emailfail') . $modx->mail->mailer->ErrorInfo);
        }
        return $modx->error->success($modx->lexicon('gax.emailsuccess'));
    }
    else{
        return $modx->error->failure($modx->lexicon('Failed loading User GAx data.'));
    }
}