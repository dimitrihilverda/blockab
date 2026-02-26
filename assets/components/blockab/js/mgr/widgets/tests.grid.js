BlockAB.grid.Tests = function(config) {
    config = config || {};

    // Set url before Ext.applyIf to ensure it's available
    if (!config.url && BlockAB.config && BlockAB.config.connectorUrl) {
        config.url = BlockAB.config.connectorUrl;
    }

    var archived = config.archived || 0;
    this.searchFieldId = archived ? 'blockab-test-search-archived' : 'blockab-test-search';

    var tbar = [];
    if (!archived) {
        tbar.push({
            text: _('blockab.test_create'),
            handler: this.createTest,
            scope: this
        });
    }
    tbar.push('->');
    tbar.push({
        xtype: 'textfield',
        id: this.searchFieldId,
        emptyText: _('search') + '...',
        listeners: {
            'change': {fn: this.search, scope: this},
            'render': {fn: function(cmp) {
                new Ext.KeyMap(cmp.getEl(), {
                    key: Ext.EventObject.ENTER,
                    fn: function() {
                        this.fireEvent('change', this);
                        this.blur();
                        return true;
                    },
                    scope: cmp
                });
            }, scope: this}
        }
    });
    tbar.push({
        xtype: 'button',
        text: _('search'),
        handler: this.search,
        scope: this
    });

    Ext.applyIf(config, {
        id: 'blockab-grid-tests',
        baseParams: {
            action: 'mgr/test/getlist',
            archived: archived
        },
        fields: ['id', 'name', 'test_group', 'description', 'active', 'archived', 'smartoptimize', 'threshold', 'randomize', 'variation_count', 'picks', 'conversions', 'conversion_rate'],
        paging: true,
        remoteSort: true,
        pageSize: 20,
        viewConfig: {
            forceFit: true,
            getRowClass: function(record) {
                if (!record.data.active || record.data.active === '0' || record.data.active === 0 || record.data.active === false) {
                    return 'blockab-row-inactive';
                }
                return '';
            }
        },
        columns: [{
            header: _('id'),
            dataIndex: 'id',
            sortable: true,
            width: 50
        }, {
            header: _('blockab.test.name'),
            dataIndex: 'name',
            sortable: true,
            width: 200,
            renderer: function(value, metaData, record) {
                return '<a href="?a=test&namespace=blockab&id=' + record.data.id + '">' + value + '</a>';
            }
        }, {
            header: _('blockab.test.test_group'),
            dataIndex: 'test_group',
            sortable: true,
            width: 150
        }, {
            header: _('blockab.variations'),
            dataIndex: 'variation_count',
            sortable: false,
            width: 80
        }, {
            header: _('blockab.stats.picks'),
            dataIndex: 'picks',
            sortable: false,
            width: 80
        }, {
            header: _('blockab.stats.conversions'),
            dataIndex: 'conversions',
            sortable: false,
            width: 80
        }, {
            header: _('blockab.stats.conversion_rate'),
            dataIndex: 'conversion_rate',
            sortable: false,
            width: 80,
            renderer: function(value) {
                return value + '%';
            }
        }, {
            header: _('blockab.test.active'),
            dataIndex: 'active',
            sortable: true,
            width: 80,
            renderer: function(value) {
                var isActive = value && value !== '0' && value !== 0 && value !== false;
                if (isActive) {
                    return '<span class="blockab-badge blockab-badge-active">' + (_('yes') || 'Ja') + '</span>';
                }
                return '<span class="blockab-badge blockab-badge-inactive">' + (_('no') || 'Nee') + '</span>';
            }
        }],
        tbar: tbar
    });
    BlockAB.grid.Tests.superclass.constructor.call(this, config);
};

Ext.extend(BlockAB.grid.Tests, MODx.grid.Grid, {
    getMenu: function() {
        var m = [];
        m.push({
            text: _('blockab.test_update'),
            handler: this.updateTest
        });
        if (!this.initialConfig.archived) {
            m.push({
                text: _('blockab.test_duplicate'),
                handler: this.duplicateTest
            });
        }
        m.push('-');
        if (this.initialConfig.archived) {
            m.push({
                text: _('blockab.test_unarchive') || 'Test Terughalen',
                handler: this.unarchiveTest
            });
        } else {
            m.push({
                text: _('blockab.test_archive') || 'Test Archiveren',
                handler: this.archiveTest
            });
        }
        m.push('-');
        m.push({
            text: _('blockab.test_remove'),
            handler: this.removeTest
        });
        return m;
    },

    createTest: function(btn, e) {
        if (!this.createTestWindow) {
            this.createTestWindow = MODx.load({
                xtype: 'blockab-window-test-create',
                listeners: {
                    'success': {fn: function() { this.refresh(); }, scope: this}
                }
            });
        }
        this.createTestWindow.show(e.target);
    },

    updateTest: function(btn, e) {
        if (!this.updateTestWindow) {
            this.updateTestWindow = MODx.load({
                xtype: 'blockab-window-test-update',
                listeners: {
                    'success': {fn: function() { this.refresh(); }, scope: this}
                }
            });
        }
        this.updateTestWindow.setValues(this.menu.record);
        this.updateTestWindow.show(e.target);
    },

    duplicateTest: function(btn, e) {
        var duplicateWindow = MODx.load({
            xtype: 'blockab-window-test-duplicate',
            record: this.menu.record,
            listeners: {
                'success': {fn: function() { this.refresh(); }, scope: this}
            }
        });
        duplicateWindow.show(e.target);
    },

    archiveTest: function() {
        var record = this.menu.record;
        MODx.msg.confirm({
            title: _('blockab.test_archive') || 'Test Archiveren',
            text: _('blockab.test_archive_confirm') || 'Weet je zeker dat je deze test wilt archiveren? De test wordt gedeactiveerd.',
            url: this.config.url,
            params: {
                action: 'mgr/test/update',
                id: record.id,
                name: record.name,
                test_group: record.test_group,
                active: 0,
                archived: 1
            },
            listeners: {
                'success': {fn: function() { this.refresh(); }, scope: this}
            }
        });
    },

    unarchiveTest: function() {
        var record = this.menu.record;
        MODx.msg.confirm({
            title: _('blockab.test_unarchive') || 'Test Terughalen',
            text: _('blockab.test_unarchive_confirm') || 'Weet je zeker dat je deze test wilt terughalen uit het archief?',
            url: this.config.url,
            params: {
                action: 'mgr/test/update',
                id: record.id,
                name: record.name,
                test_group: record.test_group,
                archived: 0
            },
            listeners: {
                'success': {fn: function() { this.refresh(); }, scope: this}
            }
        });
    },

    removeTest: function() {
        var record = this.menu.record;
        var grid = this;
        Ext.Msg.show({
            title: _('blockab.test_remove') || 'Test Verwijderen',
            msg: '<b>' + Ext.util.Format.htmlEncode(record.name || '') + '</b><br><br>' +
                 (_('blockab.test_remove_dialog_msg') ||
                     'Wil je deze test archiveren of permanent verwijderen?<br><br>' +
                     '<b>Archiveren</b> &mdash; test en statistieken blijven bewaard, de test wordt gedeactiveerd.<br>' +
                     '<b>Permanent verwijderen</b> &mdash; de test &eacute;n alle statistieken worden definitief verwijderd en zijn niet meer terug te halen.'),
            buttons: {
                yes: _('blockab.test_archive') || 'Archiveren',
                no: _('blockab.test_remove_permanent') || 'Permanent Verwijderen',
                cancel: _('cancel') || 'Annuleren'
            },
            icon: Ext.Msg.WARNING,
            fn: function(btn) {
                if (btn === 'yes') {
                    MODx.Ajax.request({
                        url: grid.config.url,
                        params: {
                            action: 'mgr/test/update',
                            id: record.id,
                            name: record.name,
                            test_group: record.test_group,
                            active: 0,
                            archived: 1
                        },
                        listeners: {
                            'success': {fn: function() { grid.refresh(); }}
                        }
                    });
                } else if (btn === 'no') {
                    MODx.Ajax.request({
                        url: grid.config.url,
                        params: {
                            action: 'mgr/test/remove',
                            id: record.id
                        },
                        listeners: {
                            'success': {fn: function() { grid.refresh(); }}
                        }
                    });
                }
            }
        });
    },

    search: function() {
        var s = Ext.getCmp(this.searchFieldId);
        this.getStore().baseParams.query = s.getValue();
        this.getBottomToolbar().changePage(1);
    }
});

Ext.reg('blockab-grid-tests', BlockAB.grid.Tests);
