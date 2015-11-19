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

// Get ready for output
$success = array('success' => false);

// Paths
$zapierPath = $modx->getOption('zapier.core_path', null, $modx->getOption('core_path') . 'components/zapier/');
$zapierPath .= 'model/zapier/';

// Get Classes
if (file_exists($zapierPath . 'zapier.class.php')) $zapier = $modx->getService('zapier', 'Zapier', $zapierPath, $scriptProperties);
if (!($zapier instanceof Zapier)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[zapierAddSubscription] could not load the required Zapier class!');
    return;
}

// Create request object from $_POST and $_GET
$request = $zapier->getRequestVars();

// We need these
if (!$request || empty($request['target_url']) || empty($request['event'])) {
    
    $success['message'] = 'target_url and event parameters are required';
    return $modx->toJSON($success);
}  

// Check client_id if specified
$clientId = null;
$exists = 0;
if ($checkOAuth2ServerClientId) {

    // In order to check client_id we must have at least one of these
    if (empty($request['access_token']) && empty($request['client_id'])) return;
    
    if (!$request['client_id']) {
        
        $clientId = $zapier->getClientId($request['access_token']);    

        // If after all that we still don't have it, escape (because $checkOAuth2ServerClientId was true)
        if (!$clientId) return;
        
    } else {
        $clientId = $request['client_id'];
    }

    $exists = $modx->getCount('ZapierSubscriptions', array(
        'target_url' => $request['target_url'],
        'client_id' => $clientId,
    ));
    
} else {
    // If we don't care about the client_id we run a wider query
    $exists = $modx->getCount('ZapierSubscriptions', array('target_url' => $request['target_url']));
}

// If we found a match we have to escape
if ($exists > 0) {
    $success['message'] = 'Subscription exists.';
    return $modx->toJSON($success);
}

// Otherwise, if this is a new subscription, create it
$subscription = $modx->newObject('ZapierSubscriptions');
if (empty($request['client_id'])) $request['client_id'] = $clientId;
if (!$subscription) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[zapierAddSubscription] could not create subscription object!');
    return;
} 
$subscription->fromArray($request);

// Attempt to save
if ($subscription->save()) {
    $success['success'] = true;
    $success['id'] = $subscription->get('id');
} else {
    $success['message'] = 'Unknown error. The subscription could not be saved.';
}

return $modx->toJSON($success);