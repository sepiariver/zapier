<?php
/**
 * zapierRemoveSubscription
 * 
 * Removes a record from the Zapier Subscriptions table.
 * Zapier recommends NOT requiring authentication to call this action, but you
 * could, by using the OAuth2Server Extra.
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
$checkOAuth2ServerClientId = $modx->getOption('checkOAuth2ServerClientId', $scriptProperties, false);

// Expected vars
$expectedFields = array('access_token' => '', 'id' => '', 'target_url' => '', 'client_id' => '');
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
$subscription = null;

// Check client_id if specified
if ($checkOAuth2ServerClientId) {

    // In order to check client_id we must have at least one of these
    if (empty($post['access_token']) && empty($post['client_id'])) return;

    // Paths
    $oauth2Path = $modx->getOption('oauth2server.core_path', null, $modx->getOption('core_path') . 'components/oauth2server/');
    $oauth2Path .= 'model/oauth2server/';
    
    // Get Class
    if (file_exists($oauth2Path . 'oauth2server.class.php')) $oauth2 = $modx->getService('oauth2server', 'OAuth2Server', $oauth2Path, $scriptProperties);
    if (!($oauth2 instanceof OAuth2Server)) {
        $modx->log(modX::LOG_LEVEL_ERROR, '[zapierAddSubscription] could not load the OAuth2Server class!');
        return;
    }
    
    if (!$post['client_id']) {
        
        // Get client_id from token, cause that's all we have
        $token = $modx->getObject('OAuth2ServerAccessTokens', array('access_token' => $post['access_token']));
        if (!$token) return;
    
        // We can finally check for client_id
        $clientId = $token->get('client_id');
        
        // If after all that we still don't have it, escape (because $checkOAuth2ServerClientId was true)
        if (!$clientId) return;
        
    } else {
        $clientId = $post['client_id'];
    }

    $subscription = $modx->getObject('ZapierSubscriptions', array(
        'target_url' => $post['target_url'],
        'client_id' => $clientId,
    ));
    
} else {
    // Otherwise, just use the ID provided
    $subscription = $modx->getObject('ZapierSubscriptions', $post['id']);
}

// If we don't find a match we have to escape
if (!$subscription) {
    $success['message'] = 'No subscription found.';
    return $modx->toJSON($success);
}

// Otherwise, clean house
$success['success'] = $subscription->remove();
if (!$success['success']) {
    $success['message'] = 'Subscription was identified but an unknown error occurred when trying to remove it.';
} 

return $modx->toJSON($success);