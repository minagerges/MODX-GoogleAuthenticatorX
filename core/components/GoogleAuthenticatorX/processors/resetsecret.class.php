<?php
/* 
  *  GoogleAuthenticator
  * 
  *  Copyright 2014 by Mina Gerges <mina@minagerges.com>
  * 
  *  GoogleAuthenticator is free software; you can redistribute it and/or modify it under the
  *  terms of the GNU General Public License as published by the Free Software
  *  Foundation; either version 2 of the License, or (at your option) any later
  *  version.
  * 
  *  GoogleAuthenticator is distributed in the hope that it will be useful, but WITHOUT ANY
  *  WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
  *  A PARTICULAR PURPOSE. See the GNU General Public License for more details.
  * 
  *  You should have received a copy of the GNU General Public License along with
  *  WipeCache; if not, write to the Free Software Foundation, Inc., 59 Temple
  *  Place, Suite 330, Boston, MA 02111-1307 USA
 */
class resetSecret extends modProcessor {
    public function process() {
        $loggeduser = $this->modx->getuser();
        if($loggeduser){
            $sudo = $loggeduser->get('sudo');
            if($sudo != True){
                return $this->modx->error->failure($this->modx->lexicon('permission_denied'));
            }
            else{
                $userid = $this->getProperty('id');
                include_once $this->modx->getOption('core_path').'components/GoogleAuthenticatorX/model/googleauthenticator.class.php';
                $GA = new GAx($this->modx);
                $GA->LoadUserByID($userid);
                $GA->resetsecret();
                return $this->modx->error->success('Reset.');
            }
        }
    }
}
return 'resetSecret';