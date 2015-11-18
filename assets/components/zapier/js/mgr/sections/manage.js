zapier.page.Manage = function (config) {
    config = config || {};
    Ext.applyIf(config, {
        components: [{
            xtype: 'zapier-panel-manage'
            ,renderTo: 'zapier-panel-manage-div'
        }]
    });
    zapier.page.Manage.superclass.constructor.call(this, config);
};
Ext.extend(zapier.page.Manage, MODx.Component);
Ext.reg('zapier-page-manage', zapier.page.Manage);
