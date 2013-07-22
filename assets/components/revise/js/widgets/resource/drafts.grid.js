Revise.grid.ResourceDrafts = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    this.ident = config.ident || 'revise-'+Ext.id();
    Ext.applyIf(config,{
        url: Revise.config.connectorUrl
        ,baseParams: {
            action: 'revise/resource/draft/getList'
            ,source: config.source || null
            ,user: config.user || null
            ,after: config.after || null
            ,before: config.before || null
        }
        ,fields: ['id','source','user','time','message']
        ,paging: true
        ,autosave: false
        ,remoteSort: true
        ,autoExpandColumn: 'message'
        ,sm: this.sm
        ,columns: [this.sm,{
            header: _('revise.source')
            ,dataIndex: 'source'
            ,editable: false
            ,width: 100
//            ,renderer: this.renderSource
        },{
            header: _('revise.user')
            ,dataIndex: 'user'
            ,editable: false
            ,width: 100
//            ,renderer: this.renderUser
        },{
            header: _('revise.time')
            ,dataIndex: 'time'
            ,editable: false
            ,width: 100
        },{
            header: _('revise.message')
            ,dataIndex: 'message'
            ,width: 250
        }]
        ,viewConfig: {
            forceFit:true
            ,enableRowBody:true
            ,showPreview:true
        }
        ,tbar: [{
            text: _('revise.bulk_actions')
            ,menu: [{
                text: _('revise.remove_selected')
                ,handler: this.removeSelected
                ,scope: this
            }]
        },'->',{
            xtype: 'textfield'
            ,name: 'source'
            ,id: 'revise-source-filter'
            ,emptyText: _('revise.filter_by_source')
            ,listeners: {
                'select': {fn:this.filterSource, scope:this}
            }
        },{
            xtype: 'textfield'
            ,name: 'user'
            ,id: 'revise-user-filter'
            ,emptyText: _('revise.filter_by_user')
            ,listeners: {
                'select': {fn:this.filterUser, scope:this}
            }
        },{
            xtype: 'datefield',
            id: 'revise-after-filter'
            ,listeners: {
                'select': {fn: this.filterDate, scope: this}
            }
        },{
            xtype: 'datefield',
            id: 'revise-before-filter'
            ,listeners: {
                'select': {fn: this.filterDate, scope: this}
            }
        },{
            xtype: 'button'
            ,id: this.ident+'-filter-clear'
            ,text: _('filter_clear')
            ,listeners: {
                'click': {fn: this.clearFilter, scope: this}
            }
        }]
    });
    Revise.grid.ResourceDrafts.superclass.constructor.call(this,config);
};
Ext.extend(Revise.grid.ResourceDrafts,MODx.grid.Grid,{
    clearFilter: function() {
        this.getStore().baseParams = {
            action: 'revise/resource/draft/getList'
        };
        Ext.getCmp('revise-source-filter').reset();
        Ext.getCmp('revise-user-filter').reset();
        Ext.getCmp('revise-after-filter').reset();
        Ext.getCmp('revise-before-filter').reset();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,filterSource: function() {}
    ,filterUser: function() {}
    ,filterDate: function() {
        var after = Ext.getCmp('revise-after-filter').getValue();
        var before = Ext.getCmp('revise-before-filter').getValue();
        var haveBothDates = after !== null && before !== null;
        // date sanity
        if(haveBothDates) {
            if(picker.id == 'revise-after-filter' && after > before) {
                Ext.getCmp('revise-after-filter').setValue(before);
                after = before;
            }
            if(picker.id == 'revise-before-filter' && after > before) {
                Ext.getCmp('revise-before-filter').setValue(after);
                before = after;
            }
        }
        if(after !== null) {
            this.getStore().baseParams['after'] = after;
        }
        if(before !== null) {
            this.getStore().baseParams['before'] = before;
        }
    }
});
Ext.reg('revise-grid-resource-drafts',Revise.grid.ResourceDrafts);
