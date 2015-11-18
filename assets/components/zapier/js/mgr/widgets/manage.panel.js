zapier.panel.Manage = function(config) {
    config = config || {};
    Ext.apply(config,{
        border: false
        ,baseCls: 'modx-formpanel'
        ,cls: 'container'
        ,items: [{
            html: '<h2>'+_('zapier.menu.manage')+'</h2>'
            ,border: false
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: { border: false ,autoHeight: true }
            ,border: true
            ,activeTab: 0
            ,hideMode: 'offsets'
            ,items: [{
                title: _('zapier.subscriptions.subscriptions')
                ,items: [{
                    html: '<p>'+_('zapier.subscriptions.intro_msg')+'</p>'
                    ,border: false
                    ,bodyCssClass: 'panel-desc'
                },{
                    xtype: 'zapier-grid-subscriptions'
                    ,preventRender: true
                    ,cls: 'main-wrapper'
                }]
            }]
        }]
    });
    zapier.panel.Manage.superclass.constructor.call(this,config);
};
Ext.extend(zapier.panel.Manage, MODx.Panel);
Ext.reg('zapier-panel-manage', zapier.panel.Manage);
