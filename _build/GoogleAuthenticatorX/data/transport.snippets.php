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
$snippets = array();
$snippets[0] = $modx->newObject('modSnippet');
$snippets[0]->set('id', 1);
$snippets[0]->set('name', 'GAxLoginHook');
$snippets[0]->set('description', '2 factor authentication pre-hook for (Login extra)');
$snippets[0]->set('category',PKG_NAME);
$snippets[0]->set('snippet', file_get_contents($sources['snippets'] . 'GAxLoginHook.php'));

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->set('id', 2);
$snippets[1]->set('name', 'GAxUserQRcode');
$snippets[1]->set('description', ' Returns GoogleAuthenticatorX user QR-Code for 2 factor authentication');
$snippets[1]->set('category',PKG_NAME);
$snippets[1]->set('snippet', file_get_contents($sources['snippets'] . 'GAxUserQRcode.php'));

return $snippets;