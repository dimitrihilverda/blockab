BlockAB.panel.Home = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'blockab-panel-home',
        border: false,
        baseCls: 'modx-formpanel',
        items: [{
            html: BlockAB.renderPageHeader(),
            border: false,
            cls: 'modx-page-header'
        }, {
            xtype: 'modx-tabs',
            border: true,
            items: [{
                title: _('blockab.tests'),
                listeners: {
                    'activate': {fn: function() {
                        var grid = Ext.getCmp('blockab-grid-tests');
                        if (grid) { grid.refresh(); }
                    }}
                },
                items: [{
                    xtype: 'blockab-grid-tests',
                    preventRender: true
                }]
            }, {
                title: _('blockab.test.archived'),
                listeners: {
                    'activate': {fn: function() {
                        var grid = Ext.getCmp('blockab-grid-tests-archived');
                        if (grid) { grid.refresh(); }
                    }}
                },
                items: [{
                    xtype: 'blockab-grid-tests',
                    id: 'blockab-grid-tests-archived',
                    archived: 1,
                    preventRender: true
                }]
            }]
        }]
    });
    BlockAB.panel.Home.superclass.constructor.call(this, config);
};
Ext.extend(BlockAB.panel.Home, MODx.Panel);
Ext.reg('blockab-panel-home', BlockAB.panel.Home);
