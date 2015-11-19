<?php
/**
 * zapierAddSubscription
 * 
 * Adds a record to the Zapier Subscriptions table.
 * HIGHLY recommended to ONLY USE THIS SNIPPET behind a login, or behind the 
 * OAuth2Server verification Snippet.
 * 
 * @package Zapier
 * @author @sepiariver <yj@modx.com>
 * Copyright 2015 by YJ Tso
 * 
 * This program is free software; you can redistribute it and/or modify it under
 * the terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT
 * ANY WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS
 * FOR A PARTICULAR PURPOSE. See the GNU General Public License for more
 * details.
 *
 * You should have received a copy of the GNU General Public License along with
 * this program; if not, write to the Free Software Foundation, Inc., 59 Temple
 * Place, Suite 330, Boston, MA 02111-1307 USA
 **/

// OPTIONS
$checkOAuth2ServerClientId = $modx->getOption('checkOAuth2ServerClientId', $scriptProperties, true);

// Expected vars
$expectedFields = array('access_token' => '', 'target_url' => '', 'event' => '');
$post = modX::sanitize($_POST, $modx->sanitizePatterns);
$post = array_intersect_key($post, $expectedFields);

// If no valid data just exit quietly
if (empty($post)) return; 

// Paths
$zapierPath = $modx->getOption('zapier.core_path', null, $modx->getOption('core_path') . 'components/zapier/');
$zapierPath .= 'model/zapier/';

// Get Classes
if (file_exists($zapierPath . 'zapier.class.php')) $zapier = $modx->getService('zapier', 'Zapier', $zapierPath, $scriptProperties);
if (!($zapier instanceof Zapier)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[zapierAddSubscription] could not load the required Zapier class!');
    return;
}

// Get ready for output
$success = array('success' => false);

// Check client_id if specified
$clientId = null;
$exists = 0;
if ($checkOAuth2ServerClientId) {
    
    // Paths
    $oauth2Path = $modx->getOption('oauth2server.core_path', null, $modx->getOption('core_path') . 'components/oauth2server/');
    $oauth2Path .= 'model/oauth2server/';
    
    // Get Class
    if (file_exists($oauth2Path . 'oauth2server.class.php')) $oauth2 = $modx->getService('oauth2server', 'OAuth2Server', $oauth2Path, $scriptProperties);
    if (!($oauth2 instanceof OAuth2Server)) {
        $modx->log(modX::LOG_LEVEL_ERROR, '[zapierAddSubscription] could not load the OAuth2Server class!');
        return;
    }

    // Get client_id from token, cause that's all we have
    $token = $modx->getObject('OAuth2ServerAccessTokens', array('access_token' => $post['access_token']));
    if (!$token) return;
    
    // We can finally check for client_id
    $clientId = $token->get('client_id');
    if (!$clientId) return;
    $exists = $modx->getCount('ZapierSubscriptions', array(
            'target_url' => $post['target_url'],
            'client_id' => $clientId,
            ));
} else {
    // If we don't care about the client_id we run a wider query
    $exists = $modx->getCount('ZapierSubscriptions', array('target_url' => $post['target_url']));
}

// If we found a match we have to escape
if ($exists > 0) {
    $success['message'] = 'Subscription exists.';
    return $modx->toJSON($success);
}

// Otherwise, if this is a new subscription, create it
$subscription = $modx->newObject('ZapierSubscriptions');
$post['client_id'] = $clientId;
$subscription->fromArray($post);
if ($subscription->save()) {
    $success['success'] = true;
} else {
    $success['message'] = 'Unknown error. The subscription could not be saved.';
}

return $modx->toJSON($success);