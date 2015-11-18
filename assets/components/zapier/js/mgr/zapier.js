var Zapier = function(config) {
    config = config || {};
Zapier.superclass.constructor.call(this,config);
};
Ext.extend(Zapier,Ext.Component,{
    page:{},window:{},grid:{},tree:{},panel:{},combo:{},config: {}
});
Ext.reg('zapier',Zapier);
zapier = new Zapier();