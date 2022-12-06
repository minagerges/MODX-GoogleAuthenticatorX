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
class emailInstructions extends modProcessor {
    public function process() {
        if(!$this->modx->user->hasSessionContext('mgr')){
            return $this->modx->error->failure( $this->modx->lexicon('permission_denied') );
        }
        else{
            $userid = $this->getProperty('id');
            include_once $this->modx->getOption('core_path').'components/GoogleAuthenticatorX/model/googleauthenticator.class.php';
            $GA = new GAx($this->modx);
            $GA->LoadUserByID($userid);
            $gaSettings = $GA->GetDecryptedSettingsArray();
            if ($gaSettings) {
                $user = $this->modx->getObject('modUser', $userid);
                $mgrLanguage = $user->getOption('manager_language');
                $this->modx->lexicon->load("$mgrLanguage:GoogleAuthenticatorX:emailtpl");
                $subject = $this->modx->lexicon('gax.notifyemail_subject');
                $body = $this->modx->lexicon('gax.notifyemail_body',array(   /*modify array according to template*/
                    'username' => $GA->UserName,));
                $body = '<html><body>'.$body.'</body></html>';
                if(!$user->sendEmail($body,array('subject' => $subject) )) {
                    return $this->modx->error->failure($this->modx->lexicon('gax.emailfail') . $this->modx->mail->mailer->ErrorInfo);
                }
                return $this->modx->error->success($this->modx->lexicon('gax.emailsuccess'));
            }
            else{
                return $this->modx->error->failure($this->modx->lexicon('Failed loading User GAx data.'));
            }
        }
    }
}
return 'emailInstructions';