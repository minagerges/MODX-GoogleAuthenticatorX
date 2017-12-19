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
$eventName = $modx->event->name;
switch($eventName) {
    case 'OnBeforeManagerLogin': /* validate authentication code */
        $username = $_POST['username'];
        $gacode = $_POST['ga_code'];
        $remember = isset($_POST['ga_remember']) ? $_POST['ga_remember'] : 0;
        $remember = intval($remember) === 1;
        $output = '';
        include_once $modx->getOption('core_path')."components/GoogleAuthenticatorX/model/googleauthenticator.class.php";
        $GA = new GAx($modx);
        $GA->LoadUserByName($username);
        
        if(!$modx->getOption('gax_disabled',null,false)){
            $modx->controller->addLexiconTopic('GoogleAuthenticatorX:default');
            if(!$GA->UserExist){
                $output = true;
            }
            else if($GA->userGAdisabled){
                $output = true;
                $modx->log(modX::LOG_LEVEL_ERROR,"GoogleAuthenticator for user:($username) is disabled." );
            }
            else if($modx->getOption('gax_courtesy_enabled',null,false) && $GA->UserInCourtesy ){
                $output = true;
                $modx->log(modX::LOG_LEVEL_ERROR,"GoogleAuthenticatorX: user:($username) logged in courtesy mode." );
            }
            else if (empty($gacode) && $GA->stillRecall()) {
                $modx->log(modX::LOG_LEVEL_ERROR,"GoogleAuthenticatorX: user:($username) logged in remember mode." );
                $output = true;
            }
            else if(empty($gacode)) {
                 $output = $modx->lexicon('gax.enterkey');
            }
            else if(preg_match("/^[0-9]{6}$/",$gacode) < 1) { 
                $output = $modx->lexicon('gax.invalidformat');
            }
            else if($GA->UserCodeMatch($gacode, $remember)){ 
                $output = true;
            }
            else{
                $output = $modx->lexicon('gax.invalidcode');
            }
        }
        else {
            $output = true;
            $modx->log(modX::LOG_LEVEL_ERROR,"GoogleAuthenticator: is disabled." );
        }
        $modx->event->_output = $output;
        break;
    case 'OnManagerLoginFormRender': /* Load authentication code field */
        if(!$modx->getOption('gax_disabled',null,false)) {
            $modx->controller->addLexiconTopic('GoogleAuthenticatorX:default');
            $output = '<br><br><div class="x-form-item login-form-item">'
                    . '<label for="GoogleAuthenticator">&nbsp;'.$modx->lexicon('gax.authkey')
                    . '<div class="x-form-element ">'
                    . '<input type="text" name="ga_code" value="" tabindex="2" autocomplete="off" maxlength="6"'
                    . 'class="x-form-text x-form-field" placeholder="'.$modx->lexicon('gax.authkey').'"/></label>';

            $rememberPeriod = intval($modx->getOption('gax_remember_period', null, 30));
            if ($rememberPeriod > 0) {
                $output .= '<div class="x-form-check-wrap modx-login-rm-cb" style="float: none;"><input type="checkbox" name="ga_remember" id="ga_remember" value="1" class="x-form-checkbox x-form-field">'
                        .'<label class="x-form-cb-label" for="ga_remember">'.$modx->lexicon('gax.remember', array('period' => $rememberPeriod)).'</label></div>';
            }
            
            $output .= '</div>'
                    . '</div>';
            $modx->event->_output = $output;
        }
        break;
        
    case 'OnUserDuplicate': /* Reset secret for duplicated user */
        $userid = $user->get('id');
        include_once $modx->getOption('core_path')."components/GoogleAuthenticatorX/model/googleauthenticator.class.php";
        $GA = new GAx($modx);
        $GA->LoadUserByID($userid);
        $GA->resetsecret();
        break;
    case 'OnUserFormSave': /* if new user create GAxsettings not to wait for next login attempt */
        if($scriptProperties['mode'] == 'new'){
            $userid = $user->get('id');
            include_once $modx->getOption('core_path')."components/GoogleAuthenticatorX/model/googleauthenticator.class.php";
            $GA = new GAx($modx);
            $GA->LoadUserByID($userid);
        }
        break;
    case 'OnUserFormPrerender': /* Adding GoogleAuthenticatorX tab */
        $modx->controller->addLexiconTopic('GoogleAuthenticatorX:default');
        $jsurl = $modx->getOption('assets_url') . 'components/GoogleAuthenticatorX/js/';
        $modx->regClientStartupScript($jsurl.'GoogleAuthenticatorXuserTab.js');
        break;
    case 'OnManagerPageInit': /* Show secret in profile*/
        if($action == 'security/profile' || $action == 76){ //show user his qrcode in his profile
            if($modx->getOption('gax_profile_enabled')){
                $jsurl = $modx->getOption('assets_url') . 'components/GoogleAuthenticatorX/js/';
                $modx->regClientStartupScript($jsurl.'GoogleAuthenticatorXuserprofile.js');
            }
        }
        break;
    case 'OnManagerPageBeforeRender': /* Check courtesy status */
        $userid = $modx->user->get('id');
        include_once $modx->getOption('core_path')."components/GoogleAuthenticatorX/model/googleauthenticator.class.php";
        $GA = new GAx($modx);
        $GA->LoadUserByID($userid);
        if($GA->UserInCourtesy && !$modx->getOption('gax_disabled') && !$GA->userGAdisabled){
            $modx->controller->addLexiconTopic('GoogleAuthenticatorX:default');
            $jsurl = $modx->getOption('assets_url') . 'components/GoogleAuthenticatorX/js/';
            $modx->regClientStartupScript($jsurl.'GoogleAuthenticatorXcourtesy.js');
            $GA->ResetCourtesy();
        }
        break;
}
return;