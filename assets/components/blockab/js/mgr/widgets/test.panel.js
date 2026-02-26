BlockAB.panel.Test = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        id: 'blockab-panel-test',
        border: false,
        baseCls: 'modx-formpanel',
        items: [{
            html: BlockAB.renderPageHeader({
                subtitle: BlockAB.config.test_name || '',
                backUrl: '?a=index&namespace=blockab'
            }),
            border: false,
            cls: 'modx-page-header'
        }, {
            xtype: 'modx-tabs',
            border: true,
            items: [{
                title: _('blockab.stats'),
                items: [{
                    xtype: 'blockab-panel-stats',
                    preventRender: true
                }]
            }, {
                title: _('blockab.variations'),
                items: [{
                    xtype: 'blockab-grid-variations',
                    preventRender: true
                }]
            }]
        }]
    });
    BlockAB.panel.Test.superclass.constructor.call(this, config);
};
Ext.extend(BlockAB.panel.Test, MODx.Panel);
Ext.reg('blockab-panel-test', BlockAB.panel.Test);
