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

// We need some things
if (!$hook) return false;
$config = $formit->config;
$values = $hook->getValues();
$eventName = $getOption('zapierEventName', $config, 'new_form');

if (empty($values) || empty($eventName)) return;

// Paths
$zapierPath = $modx->getOption('zapier.core_path', null, $modx->getOption('core_path') . 'components/zapier/');
$zapierPath .= 'model/zapier/';

// Get Classes
if (file_exists($zapierPath . 'zapier.class.php')) $zapier = $modx->getService('zapier', 'Zapier', $zapierPath, $scriptProperties);
if (!($zapier instanceof Zapier)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[zapierAddSubscription] could not load the required Zapier class!');
    return;
}

// Get Subscriptions
$subscriptions = $modx->getCollection('ZapierSubscriptions', array('event' => $eventName));

// Do stuff
foreach ($subscriptions as $sub) {
    
    // If some some weird reason we don't have a target_url we gotta get out
    if (!$sub->get('target_url')) continue;
    
    
}


