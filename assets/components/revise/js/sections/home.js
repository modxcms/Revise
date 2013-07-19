Ext.onReady(function() {
    MODx.load({ xtype: 'revise-page-home'});
});

Revise.page.Home = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        components: [{
            xtype: 'revise-panel-history'
            ,renderTo: 'revise-panel-home-div'
        }]
    });
    Revise.page.Home.superclass.constructor.call(this,config);
};
Ext.extend(Revise.page.Home,MODx.Component);
Ext.reg('revise-page-home',Revise.page.Home);
