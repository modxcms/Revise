Revise.grid.ResourceHistory = function(config) {
    config = config || {};
    this.sm = new Ext.grid.CheckboxSelectionModel();
    this.ident = config.ident || 'revise-'+Ext.id();
    Ext.applyIf(config,{
        url: Revise.config.connectorUrl
        ,baseParams: {
            action: 'revise/resource/history/getList'
            ,source: config.source || null
            ,user: config.user || null
            ,after: config.after || null
            ,before: config.before || null
        }
        ,fields: ['id','source','pagetitle','user','username','time','message','menu']
        ,paging: true
        ,autosave: false
        ,remoteSort: true
        ,refreshCache: config.refreshCache || false
        ,autoExpandColumn: 'message'
        ,sm: this.sm
        ,columns: [this.sm,{
            header: _('revise_source')
            ,dataIndex: 'source'
            ,editable: false
            ,width: 100
            ,renderer: this.renderSource
        },{
            header: _('revise_user')
            ,dataIndex: 'user'
            ,editable: false
            ,width: 100
            ,renderer: this.renderUser
        },{
            header: _('revise_time')
            ,dataIndex: 'time'
            ,editable: false
            ,width: 100
        },{
            header: _('revise_message')
            ,dataIndex: 'message'
            ,width: 250
        }]
        ,viewConfig: {
            forceFit:true
            ,enableRowBody:true
            ,showPreview:true
        }
        ,tbar: [{
            text: _('revise_bulk_actions')
            ,menu: [{
                text: _('revise_remove_selected')
                ,handler: this.removeSelected
                ,scope: this
            }]
        },'->',{
            xtype: 'textfield'
            ,name: 'source'
            ,id: 'history-source-filter'
            ,emptyText: _('revise_filter_by_source')
            ,listeners: {
                'change': {fn:this.filterSource, scope:this}
            }
        },{
            xtype: 'textfield'
            ,name: 'user'
            ,id: 'history-user-filter'
            ,emptyText: _('revise_filter_by_user')
            ,listeners: {
                'change': {fn:this.filterUser, scope:this}
            }
        },{
            xtype: 'datefield',
            id: 'history-after-filter'
            ,emptyText: _('revise_filter_by_time_after')
            ,listeners: {
                'select': {fn:this.filterAfter, scope:this}
            }
        },{
            xtype: 'datefield',
            id: 'history-before-filter'
            ,emptyText: _('revise_filter_by_time_before')
            ,listeners: {
                'select': {fn:this.filterBefore, scope:this}
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
    Revise.grid.ResourceHistory.superclass.constructor.call(this,config);
};
Ext.extend(Revise.grid.ResourceHistory,MODx.grid.Grid,{
    clearFilter: function() {
        this.getStore().baseParams = {
            action: 'revise/resource/history/getList'
        };
        Ext.getCmp('history-source-filter').reset();
        Ext.getCmp('history-user-filter').reset();
        Ext.getCmp('history-after-filter').reset();
        Ext.getCmp('history-before-filter').reset();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,renderUser: function(v,md,rec) {
        return String.format(
            '<span class="revise-author"><b>{0}</b>&mdash;{1}</span>',
            v,
            rec.data.username
        );
    }
    ,renderSource: function(v,md,rec) {
        return String.format(
            '<span class="revise-source"><b>{0}</b>&mdash;{1}</span>',
            v,
            rec.data.pagetitle
        );
    }
    ,filterSource: function(cb,nv,ov) {
        this.getStore().setBaseParam('source',cb.getValue());
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,filterUser: function(cb,nv,ov) {
        this.getStore().setBaseParam('user',cb.getValue());
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,filterAfter: function(cb,nv,ov) {
        var before = Ext.getCmp('history-before-filter');
        this.getStore().setBaseParam('after',cb.getValue());
        if (before.getValue() < cb.getValue()) {
            this.getStore().setBaseParam('before', null);
            before.reset();
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,filterBefore: function(cb,nv,ov) {
        var after = Ext.getCmp('history-after-filter');
        this.getStore().setBaseParam('before',cb.getValue());
        if (after.getValue() > cb.getValue()) {
            this.getStore().setBaseParam('after', null);
            after.reset();
        }
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,viewRevision: function() {
        var url = this.config.url + '?action=revise/resource/history/view&id=' + this.menu.record.id + '&HTTP_MODAUTH=' + MODx.siteId;
        window.open(url);
    }
    ,applyRevision: function() {
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'revise/resource/history/apply'
                ,id: this.menu.record.id
                ,refreshCache: this.config.refreshCache
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('revise-grid-resource-history',Revise.grid.ResourceHistory);
