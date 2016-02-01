<?php
/**
 * ZapierSendResourcesToSubscribers
 * 
 * Sends a MODX Resource's field values to Zapier Subscribers.
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

// Only fire in the mgr OnDocFormSave
if ($modx->context->get('key') !== 'mgr' || $modx->event->name !== 'OnDocFormSave') return;

// We need this
if (!$resource || empty($mode)) {
    
    $modx->log(modX::LOG_LEVEL_ERROR, '[ZapierSendResourcesToSubscribers] plugin missing required input on line: ' . __LINE__);
    return;
    
}

// Paths
$zapierPath = $modx->getOption('zapier.core_path', null, $modx->getOption('core_path') . 'components/zapier/');
$zapierPath .= 'model/zapier/';

// Get Classes
if (file_exists($zapierPath . 'zapier.class.php')) $zapier = $modx->getService('zapier', 'Zapier', $zapierPath);
if (!($zapier instanceof Zapier)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[ZapierSendResourcesToSubscribers] could not load the required Zapier class!');
    return;
}

// OPTIONS
$sendOnModes = $modx->getOption('send_on_modes', $scriptProperties, 'upd,new');
$sendOnModes = $zapier->explodeAndClean($sendOnModes);
$eventName = $modx->getOption('zapier_event_name', $scriptProperties, 'resource_save');
$excludeFields = $modx->getOption('exclude_fields', $scriptProperties, '');
$excludeFields = $zapier->explodeAndClean($excludeFields);
$sendUnpublished = $modx->getOption('send_unpublished', $scriptProperties, 0);
$limitToParents = $modx->getOption('limit_to_parents', $scriptProperties, '');
$limitToParents = $zapier->explodeAndClean($limitToParents);

// Abide by choices
if (!in_array($mode, $sendOnModes)) return;
if (!$sendUnpublished && !$resource->get('published')) return;
if (!empty($limitToParents) && !in_array($resource->get('parent'), $limitToParents)) return;

// Get field values
$values = $resource->toArray('',true,true);
$values = array_diff_key($values, $excludeFields);
foreach ($fields as $key => $val) {
    $values[$key] = str_replace(array('[',']'), array('&#91;','&#92;'), $val);
}
$values = $modx->toJSON($values);

// These are also required to do anything
if (empty($values) || empty($eventName)) return;

// Get Subscriptions
$subscriptions = $modx->getCollection('ZapierSubscriptions', array('event' => $eventName));

if (!$subscriptions) {
    $modx->log(modX::LOG_LEVEL_WARN, '[ZapierSendResourcesToSubscribers] could not load any matching subscriptions');
    return;
}

// Do stuff
$successes = 0;
foreach ($subscriptions as $sub) {
    
    // If some some weird reason we don't have a target_url, it's a bad record and should be removed
    if (!$sub->get('target_url')) {
        if ($sub->remove()) {
            $noTargetError = '[ZapierSendResourcesToSubscribers] discovered a ZapierSubscriptions object with no target_url, and removed it.';
        } else {
            $noTargetError = '[ZapierSendResourcesToSubscribers] discovered a ZapierSubscriptions object ID: ' . $sub->get('id') . ' with no target_url, and failed to remove it.';
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
        $modx->log(modX::LOG_LEVEL_ERROR, '[ZapierSendResourcesToSubscribers] received a 410 from Zapier and removed the subscription ID: ' . $sub->get('id'));
        $sub->remove();
    } elseif ($response_code == '200') {
        $successes++;
    }

}

// TODO: log manager action if success
/*if ($successes > 0)*/