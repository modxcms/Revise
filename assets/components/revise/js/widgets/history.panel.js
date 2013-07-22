Revise.panel.History = function(config) {
    config = config || {};
    Ext.applyIf(config,{
        id: 'revise-panel-history'
        ,url: Revise.config.connectorUrl
        ,cls: 'container form-with-labels'
        ,labelAlign: top
        ,baseParams: {}
        ,items: [{
            html: '<h2>'+_('revise.history')+'</h2>'
            ,border: false
            ,id: 'revise-history'
            ,cls: 'modx-page-header'
        },{
            xtype: 'modx-tabs'
            ,defaults: {border:false, autoHeight:true}
            ,border: true
            ,stateful: true
            ,stateId: 'revise-home-tabpanel'
            ,stateEvents: ['tabchange']
            ,getState:function() {
                return this.items.indexOf(this.getActiveTab());
            }
            ,items: [{
                title: 'History'
                ,xtype: 'revise-grid-resource-history'
                ,preventRender: true
                ,cls: 'revise-grid main-wrapper'
                ,width: '98%'
           },{
                xtype: 'revise-grid-resource-drafts'
                ,cls: 'revise-grid main-wrapper'
                ,preventRender: true
                ,width: '98%'
            }]
        }]
        ,listeners: {
        }
    })
    Revise.panel.History.superclass.constructor.call(this,config);
}
Ext.extend(Revise.panel.History,MODx.FormPanel,{

});
Ext.reg('revise-panel-history',Revise.panel.History);
