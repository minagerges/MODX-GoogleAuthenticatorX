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
class GetUserQRCode extends modProcessor
{

    public function process()
    {
        if (!$this->modx->user->hasSessionContext('mgr')) {
            return $this->failure($this->modx->lexicon('permission_denied'));
        }

        $userid = $this->modx->user->get('id');
        include_once $this->modx->getOption('core_path') . 'components/GoogleAuthenticatorX/model/googleauthenticator.class.php';
        $GA = new GAx($this->modx);
        $GA->LoadUserByID($userid);
        $gaSettings = $GA->GetDecryptedSettingsArray();
        if ($gaSettings) {
            if ($this->modx->getOption('gax_profile_enabled') || $gaSettings['incourtesy'] == 'yes') {
                return $this->success('', ['qrurl' => $gaSettings['qrurl']]);
            }

            return $this->failure($this->modx->lexicon('permission_denied'));
        }

        return $this->failure($this->modx->lexicon('no_records_found'));
    }
}

return 'GetUserQRCode';
