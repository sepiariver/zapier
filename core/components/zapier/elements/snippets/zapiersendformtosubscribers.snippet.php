<?php
/**
 * zapierSendFormToSubscribers
 * 
 * Sends a FormIt form submission to Zapier Subscribers.
 * REQUIRES the FormIt Extra! Call this as a FormIt hook.
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

// Pass control to next hook even if this one fails?
$returnTrueOnFail = (bool) $modx->getOption('zapierReturnTrueOnFail', $formit->config, true);

// We need some things
if (!$hook) return $returnTrueOnFail;
$values = $hook->getValues();
$values = $modx->toJSON($values);
$eventName = $modx->getOption('zapierEventName', $formit->config, 'new_form');
if (empty($values) || empty($eventName)) return $returnTrueOnFail;

// Paths
$zapierPath = $modx->getOption('zapier.core_path', null, $modx->getOption('core_path') . 'components/zapier/');
$zapierPath .= 'model/zapier/';

// Get Classes
if (file_exists($zapierPath . 'zapier.class.php')) $zapier = $modx->getService('zapier', 'Zapier', $zapierPath);
if (!($zapier instanceof Zapier)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[zapierSendFormToSubscribers] could not load the required Zapier class!');
    return $returnTrueOnFail;
}

// Get Subscriptions
$subscriptions = $modx->getCollection('ZapierSubscriptions', array('event' => $eventName));

if (!$subscriptions) {
    $modx->log(modX::LOG_LEVEL_WARN, '[zapierSendFormToSubscribers] could not load any matching subscriptions');
    return $returnTrueOnFail;
}


// Do stuff
$successes = 0;
foreach ($subscriptions as $sub) {
    
    // If some some weird reason we don't have a target_url, it's a bad record and should be removed
    if (!$sub->get('target_url')) {
        if ($sub->remove()) {
            $noTargetError = '[zapierSendFormToSubscribers] discovered a ZapierSubscriptions object with no target_url, and removed it.';
        } else {
            $noTargetError = '[zapierSendFormToSubscribers] discovered a ZapierSubscriptions object ID: ' . $sub->get('id') . ' with no target_url, and failed to remove it.';
        }
        $modx->log(modX::LOG_LEVEL_ERROR, $noTargetError);
        continue;
    }
    
    // cURL request to post form data
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_HEADER, 1);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Content-Type: application/json'));
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 0);
    curl_setopt($ch, CURLOPT_URL, $sub->get('target_url'));
    curl_setopt($ch, CURLOPT_FOLLOWLOCATION, 1);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $values);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT_MS, 2500);
    curl_setopt($ch, CURLOPT_TIMEOUT_MS, 7500);
    
    $result = curl_exec($ch);
    if (!empty($result)) $response_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    
    // According to Zapier: https://zapier.com/developer/documentation/v2/rest-hooks/
    if ($response_code == '410') {
        $modx->log(modX::LOG_LEVEL_ERROR, '[zapierSendFormToSubscribers] received a 410 from Zapier and removed the subscription ID: ' . $sub->get('id'));
        $sub->remove();
    } elseif ($response_code == '200') {
        $successes++;
    }

}

// If we got this far, we didn't fail
if ($successes > 0) $returnTrueOnFail = true;
return $returnTrueOnFail;
