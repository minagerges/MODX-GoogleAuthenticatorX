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
switch ($options[xPDOTransport::PACKAGE_ACTION]) {
    case xPDOTransport::ACTION_INSTALL:
    case xPDOTransport::ACTION_UPGRADE:

$output = <<<HTML
<p style="font-style: italic;"><strong>NB:</strong> Sending emails might extend the installation process time depending on the amount of users with manager access.</p>
<br/>
<div style="padding-left: 20px;">       
    <input type="checkbox" name="sendnotifymail" checked  /> 
    <label for="sendnotifymail" style="display: inline;">Email users with manager access <i>(New install only)</i></label>
    <br/><br/>
    <input type="checkbox" name="enablegax"/> 
    <label for="enablegax" style="display: inline;">Enable 2-factor verification</label>
    <br />
<p style="color:red;">If you enable 2-step verification, be ready with your mobile device for setting up Google Authenticator, otherwise you will lose your login session.</p>
</div>
HTML;
    break;
    default:
    case xPDOTransport::ACTION_UNINSTALL:
        $output = '';
    break;
}

return $output;
