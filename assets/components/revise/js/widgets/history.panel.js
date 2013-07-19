Revise.panel.History = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'revise-panel-history'
        ,url: Revise.config.connector_url
        ,cls: 'container form-with-labels'
        ,baseParams: {}
        ,items: [{

        }]
        ,listeners: {

        }
    })
    Revise.panel.History.superclass.constructor.call(this,config);
}
Ext.extend(Revise.panel.History,MODx.FormPanel,{
    setup: function() {

    }
    ,beforeSubmit: function(o) {}
    ,success: function(o) {

    }
});
Ext.reg('revise-panel-history',Revise.panel.History);
