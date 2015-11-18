<?php
require_once dirname(__FILE__) . '/model/zapier/zapier.class.php';
/**
 * @package Zapier
 */

abstract class ZapierBaseManagerController extends modExtraManagerController {
    /** @var Zapier $zapier */
    public $zapier;
    public function initialize() {
        $this->zapier = new Zapier($this->modx);

        $this->addCss($this->zapier->getOption('cssUrl').'mgr.css');
        $this->addJavascript($this->zapier->getOption('jsUrl').'mgr/zapier.js');
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            zapier.config = '.$this->modx->toJSON($this->zapier->options).';
            zapier.config.connector_url = "'.$this->zapier->getOption('connectorUrl').'";
        });
        </script>');
        
        parent::initialize();
    }
    public function getLanguageTopics() {
        return array('zapier:default');
    }
    public function checkPermissions() { return true;}
}