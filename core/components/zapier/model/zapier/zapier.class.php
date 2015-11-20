<?php

/**
 * Zapier class for MODX integration with...whatever.
 * @package Zapier
 *
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
 
class Zapier
{
    public $modx = null;
    public $namespace = 'zapier';
    public $options = array();

    public function __construct(modX &$modx, array $options = array())
    {
        $this->modx =& $modx;
        $this->namespace = $this->getOption('namespace', $options, 'zapier');

        $corePath = $this->getOption('core_path', $options, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/zapier/');
        $assetsPath = $this->getOption('assets_path', $options, $this->modx->getOption('assets_path', null, MODX_ASSETS_PATH) . 'components/zapier/');
        $assetsUrl = $this->getOption('assets_url', $options, $this->modx->getOption('assets_url', null, MODX_ASSETS_URL) . 'components/zapier/');
        $dbPrefix = $this->getOption('table_prefix', $options, $this->modx->getOption('table_prefix', null, 'modx_'));
        $oauth2Path = $this->getOption('oauth2server.core_path', $this->modx->config, $this->modx->getOption('core_path', null, MODX_CORE_PATH) . 'components/oauth2server/');
        $oauth2Path .= 'model/oauth2server/';
        
        
        /* load config defaults */
        $this->options = array_merge(array(
            'namespace' => $this->namespace,
            'corePath' => $corePath,
            'modelPath' => $corePath . 'model/',
            'chunksPath' => $corePath . 'elements/chunks/',
            'snippetsPath' => $corePath . 'elements/snippets/',
            'templatesPath' => $corePath . 'templates/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php',
            'oauth2Path' => $oauth2Path,
            'expectedPostFields' => 'target_url,event,access_token,client_id',
            'potentialGetParams' => 'access_token,client_id',
        ), $options);
        
     
        $this->modx->addPackage('zapier', $this->options['modelPath'], $this->modx->config['table_prefix']);
        $this->modx->lexicon->load('zapier:default');
               
    }
    
    
    /**
     * getRequestVars
     *
     * build an array of request variables
     * @param string $action Adds ID to returned array if set to 'remove'
     * return array Merged get and post
     *
     **/
    public function getRequestVars($action = '') {
        
        // Expected vars
        $expectedPostFields = array_flip($this->explodeAndClean($this->options['expectedPostFields']));
        $potentialGetParams = array_flip($this->explodeAndClean($this->options['potentialGetParams']));
        
        if ($action === 'remove') $expectedPostFields['id'] = '';
        
        $get = modX::sanitize($_GET, $this->modx->sanitizePatterns);
        $get = array_intersect_key($get, $potentialGetParams);
        
        if (empty($_POST) || $_SERVER['CONTENT_TYPE'] === 'application/json') {
            // we may have raw post data as JSON string
            $post = file_get_contents('php://input');
            if (empty($post)) return false;
            $post = $this->modx->fromJSON($post);
        } else {
            $post = $_POST;
        }
        $post = modX::sanitize($post, $this->modx->sanitizePatterns);
        $post = array_intersect_key($post, $expectedPostFields);
        
        return array_merge($get, $post);
        
    }
    
    /**
     * getClientId
     *
     * Calls OAuth2Server class to fetch client_id from OAuth2ServerAccessTokens object
     * @param array $config optional
     * @param string $access_token access token value
     * @return string clientId
     *
     **/
    public function getClientId($access_token, $config = array()) {
      
        // Get Class
        if (file_exists($this->options['oauth2Path'] . 'oauth2server.class.php')) $oauth2 = $this->modx->getService('oauth2server', 'OAuth2Server', $this->options['oauth2Path'], $config);
        if (!($oauth2 instanceof OAuth2Server)) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[Zapier] getClientId method could not load the OAuth2Server class!');
            return '';
        }

        // Get client_id from token, cause that's all we have
        $token = $this->modx->getObject('OAuth2ServerAccessTokens', array('access_token' => $access_token));
        if (!$token) return '';
    
        // We can finally check for client_id
        return $token->get('client_id');
        
    }
     
    /* UTILITY METHODS (@theboxer) */
    /**
     * Get a local configuration option or a namespaced system setting by key.
     *
     * @param string $key The option key to search for.
     * @param array $options An array of options that override local options.
     * @param mixed $default The default value returned if the option is not found locally or as a
     * namespaced system setting; by default this value is null.
     * @return mixed The option value or the default value specified.
     */
    public function getOption($key, $options = array(), $default = null)
    {
        $option = $default;
        if (!empty($key) && is_string($key)) {
            if ($options != null && array_key_exists($key, $options)) {
                $option = $options[$key];
            } elseif (array_key_exists($key, $this->options)) {
                $option = $this->options[$key];
            } elseif (array_key_exists("{$this->namespace}.{$key}", $this->modx->config)) {
                $option = $this->modx->getOption("{$this->namespace}.{$key}");
            }
        }
        return $option;
    }

    public function explodeAndClean($array, $delimiter = ',')
    {
        $array = explode($delimiter, $array);     // Explode fields to array
        $array = array_map('trim', $array);       // Trim array's values
        $array = array_keys(array_flip($array));  // Remove duplicate fields
        $array = array_filter($array);            // Remove empty values from array

        return $array;
    }
    public function getChunk($tpl, $phs)
    {
        if (strpos($tpl, '@INLINE ') !== false) {
            $content = str_replace('@INLINE', '', $tpl);
            /** @var \modChunk $chunk */
            $chunk = $this->modx->newObject('modChunk', array('name' => 'inline-' . uniqid()));
            $chunk->setCacheable(false);
            
            return $chunk->process($phs, $content);
        }
        
        return $this->modx->getChunk($tpl, $phs);
    }
}