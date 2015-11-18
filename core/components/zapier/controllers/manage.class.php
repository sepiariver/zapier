<?php
require_once dirname(dirname(__FILE__)) . '/index.class.php';
/**
 * Loads the home page.
 *
 * @package Zapier
 * @subpackage controllers
 */
class ZapierManageManagerController extends ZapierBaseManagerController {
    public function process(array $scriptProperties = array()) {

    }
    public function getPageTitle() { return $this->modx->lexicon('zapier'); }
    public function loadCustomCssJs() {
        
        $this->addJavascript($this->zapier->getOption('jsUrl').'mgr/widgets/subscriptions.grid.js');
        
        $this->addJavascript($this->zapier->getOption('jsUrl').'mgr/widgets/manage.panel.js');
        $this->addLastJavascript($this->zapier->getOption('jsUrl').'mgr/sections/manage.js');
    
        $this->addHtml("<script>
            Ext.onReady(function() {
                MODx.load({ xtype: 'zapier-page-manage'});
            });
        </script>");
        
    }

    public function getTemplateFile() { return $this->zapier->getOption('templatesPath') . 'manage.tpl'; }
}