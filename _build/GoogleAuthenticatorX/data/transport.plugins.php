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
$e = array(
            'OnBeforeManagerLogin',
            'OnManagerLoginFormRender',
            'OnManagerPageBeforeRender',
            'OnManagerPageInit',
            'OnUserDuplicate',
            'OnUserFormPrerender',
            'OnUserFormSave',
	);
$events = array();
foreach ($e as $ev) {
    $events[$ev] = $modx->newObject('modPluginEvent');
    $events[$ev]->fromArray(array(
        'event' => $ev,
        'priority' => 0,
        'propertyset' => 0
    ),'',true,true);
}

$plugins = array();
$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->set('id',1);
$plugins[0]->set('name','GoogleAuthenticatorX');
$plugins[0]->set('description','Add Google Authenticator 2-step verification to MODX manager login.');
$plugins[0]->set('plugincode', getPluginContent($sources['plugins'] . 'included.googleauthenticatorx.plugin.php'));
$plugins[0]->set('category', 0);
$plugins[0]->addMany($events);
$modx->log(xPDO::LOG_LEVEL_INFO,'Packaged in '.count($events).' Plugin Events');

function getPluginContent($filename) {
    $o = file_get_contents($filename);
    $o = trim(str_replace(array('<?php','?>'), '', $o));
    return $o;
}

return $plugins;