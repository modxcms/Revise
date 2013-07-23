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
        ,refreshCache: config.refreshCache || false
        ,removeDraft: config.removeDraft || true
        ,autoExpandColumn: 'message'
        ,sm: this.sm
        ,columns: [this.sm,{
            header: _('revise_source')
            ,dataIndex: 'source'
            ,editable: false
            ,width: 100
//            ,renderer: this.renderSource
        },{
            header: _('revise_user')
            ,dataIndex: 'user'
            ,editable: false
            ,width: 100
//            ,renderer: this.renderUser
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
            ,id: 'drafts-source-filter'
            ,emptyText: _('revise_filter_by_source')
            ,listeners: {
                'select': {fn:this.filterSource, scope:this}
            }
        },{
            xtype: 'textfield'
            ,name: 'user'
            ,id: 'drafts-user-filter'
            ,emptyText: _('revise_filter_by_user')
            ,listeners: {
                'select': {fn:this.filterUser, scope:this}
            }
        },{
            xtype: 'datefield',
            id: 'drafts-after-filter'
            ,listeners: {
                'select': {fn: this.filterDate}
            }
        },{
            xtype: 'datefield',
            id: 'drafts-before-filter'
            ,listeners: {
                'select': {fn: this.filterDate}
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
        Ext.getCmp('drafts-source-filter').reset();
        Ext.getCmp('drafts-user-filter').reset();
        Ext.getCmp('drafts-after-filter').reset();
        Ext.getCmp('drafts-before-filter').reset();
        this.getBottomToolbar().changePage(1);
        this.refresh();
    }
    ,filterSource: function() {}
    ,filterUser: function() {}
    ,filterDate: function() {
        var grid = Ext.getCmp('revise-grid-resource-drafts');
        var after = Ext.getCmp('drafts-after-filter').getValue();
        var before = Ext.getCmp('drafts-before-filter').getValue();
        var haveBothDates = after !== null && before !== null;
        // date sanity
        if(haveBothDates) {
            if(this.id == 'drafts-after-filter' && after > before) {
                Ext.getCmp('drafts-after-filter').setValue(before);
                after = before;
            }
            if(this.id == 'drafts-before-filter' && after > before) {
                Ext.getCmp('drafts-before-filter').setValue(after);
                before = after;
            }
        }
        if(after !== null) {
            grid.getStore().baseParams['after'] = after;
        }
        if(before !== null) {
            grid.getStore().baseParams['before'] = before;
        }
    }
    ,viewRevision: function() {
        var url = this.config.url + '?action=revise/resource/draft/view&id=' + this.menu.record.id + '&HTTP_MODAUTH=' + MODx.siteId;
        window.open(url);
    }
    ,applyRevision: function() {
        MODx.Ajax.request({
            url: this.config.url
            ,params: {
                action: 'revise/resource/draft/apply'
                ,id: this.menu.record.id
                ,refreshCache: this.config.refreshCache
                ,removeDraft: this.config.removeDraft
            }
            ,listeners: {
                'success': {fn:this.refresh,scope:this}
            }
        });
    }
});
Ext.reg('revise-grid-resource-drafts',Revise.grid.ResourceDrafts);
