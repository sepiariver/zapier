zapier.grid.ZapierSubscriptions = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        url: zapier.config.connectorUrl
        ,baseParams: {
            action: 'mgr/subscriptions/getlist'
        }
        ,fields: ['id', 'target_url', 'event', 'client_id']
        ,autoHeight: true
        ,paging: true
        ,remoteSort: true
        ,columns: [{
            header: _('zapier.subscriptions.id')
            ,dataIndex: 'id'
            ,sortable: true
            ,width: 60
        },{
            header: _('zapier.subscriptions.target_url')
            ,dataIndex: 'target_url'
            ,sortable: true
            ,width: 160
        },{
            header: _('zapier.subscriptions.event')
            ,dataIndex: 'event'
            ,sortable: true
            ,width: 60
        },{
            header: _('zapier.subscriptions.client_id')
            ,dataIndex: 'client_id'
            ,sortable: true
            ,width: 60
        }]
    });
    zapier.grid.ZapierSubscriptions.superclass.constructor.call(this,config);
};
Ext.extend(zapier.grid.ZapierSubscriptions,MODx.grid.Grid,{
    filters: []
    
    ,getMenu: function() {
        var m = [];
        m.push({
            text: _('zapier.subscriptions.remove')
            ,handler: this.removeSubscription
        });
        this.addContextMenuItem(m);
    }
    
    ,removeSubscription: function(btn,e) {
        if (!this.menu.record) return false;
        
        MODx.msg.confirm({
            title: _('zapier.subscriptions.remove')
            ,text: _('zapier.subscriptions.remove_confirm')
            ,url: this.config.url
            ,params: {
                action: 'mgr/subscriptions/remove'
                ,id: this.menu.record.id
            }
            ,listeners: {
                'success': {fn:function(r) { this.refresh(); },scope:this}
            }
        });
    }
});
Ext.reg('zapier-grid-subscriptions',zapier.grid.ZapierSubscriptions);
