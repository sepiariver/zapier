<?php
/**
 * zapierPostToFile
 * 
 * WIP creates file from POST data
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
 *
 * OPTIONS:
 *
 **/

// Config
// don't save GET parameters to file
$scriptProperties['potentialGetParams'] = '';

// Paths
$zapierCorePath = $modx->getOption('zapier.core_path', null, $modx->getOption('core_path') . 'components/zapier/');
$zapierModelPath = $zapierCorePath . 'model/zapier/';

// Get Classes
if (file_exists($zapierModelPath . 'zapier.class.php')) $zapier = $modx->getService('zapier', 'Zapier', $zapierModelPath, $scriptProperties);
if (!($zapier instanceof Zapier)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[zapierSaveFile] could not load the required Zapier class!');
    return;
}
// OPTIONS
$path = $modx->getOption('path', $scriptProperties, $zapierCorePath . 'saved-files/');
$extension = '.' . ltrim($modx->getOption('extension', $scriptProperties, 'json'), '.');
$postFieldForFilename = $modx->getOption('postFieldForFilename', $scriptProperties, '');
$defaultFilename = md5(time()) . $extension;
$scopeToNestedKey = $zapier->explodeAndClean($modx->getOption('scopeToNestedKey', $scriptProperties, ''));

// If directory exists but isn't writable we have a problem, Houston
if (file_exists($path) && !is_writable($path)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'The directory at ' . $path . 'is not writable!','','zapierSaveFile');
    return;
}

// Check if directory exists, if not, create it
if (!file_exists($path)) {
    if (mkdir($path, 0755, true)) {
        $modx->log(modX::LOG_LEVEL_INFO, 'Directory created at ' . $path, '', 'zapierSaveFile');
    } else {
        $modx->log(modX::LOG_LEVEL_ERROR, 'Directory could not be created at ' . $path, '', 'zapierSaveFile');
        return;
    }
}

// Initialize response
$response = array('success' => false);

// Process all POST data
$post = $zapier->getRequestVars('', false);
if (empty($post) || !is_array($post)) {
    http_response_code(400);
    return $modx->toJSON($response);
}

// Determine filename
$filename = (!empty($postFieldForFilename) && isset($post[$postFieldForFilename])) ? rtrim($post[$postFieldForFilename], $extension) . $extension : $defaultFilename;
$file = rtrim($path, '/') . '/' . $filename;

// Drill down to nested array element, if requested
if (!empty($scopeToNestedKey)) {
    
    foreach ($scopeToNestedKey as $key) {
        $post = $post[$key];
    }
    
}

// Write array as JSON
file_put_contents($file, $modx->toJSON($post));

// Check results
if (file_exists($file) && is_readable($file)) {
    $modx->log(modX::LOG_LEVEL_INFO, 'Success! POST data saved to file "' . $file . '"', '', 'zapierSaveFile');
    $response['success'] = true;
} else {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Error saving to file "' . $file . '"', '', 'zapierSaveFile');
    $response['message'] = 'Error saving to file.';
}

// Response
return $modx->toJSON($response);