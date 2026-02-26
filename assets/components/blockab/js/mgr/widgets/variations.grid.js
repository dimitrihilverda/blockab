BlockAB.grid.Variations = function(config) {
    config = config || {};

    // Set url before Ext.applyIf to ensure it's available
    if (!config.url && BlockAB.config && BlockAB.config.connectorUrl) {
        config.url = BlockAB.config.connectorUrl;
    }

    Ext.applyIf(config, {
        id: 'blockab-grid-variations',
        baseParams: {
            action: 'mgr/variation/getlist',
            test: config.test || BlockAB.config.test_id
        },
        fields: ['id', 'test', 'name', 'variant_key', 'description', 'active', 'weight', 'picks', 'conversions', 'conversion_rate'],
        paging: true,
        remoteSort: true,
        pageSize: 20,
        viewConfig: {
            forceFit: true,
            getRowClass: function(record) {
                if (BlockAB.currentWinner && record.data.variant_key === BlockAB.currentWinner) {
                    return 'blockab-winner-row';
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
            header: _('blockab.variation.variant_key'),
            dataIndex: 'variant_key',
            sortable: true,
            width: 80,
            renderer: function(value, meta, record) {
                if (BlockAB.currentWinner && record.data.variant_key === BlockAB.currentWinner) {
                    return '<strong>' + value + '</strong> &#127942;';
                }
                return value;
            }
        }, {
            header: _('blockab.variation.name'),
            dataIndex: 'name',
            sortable: true,
            width: 200
        }, {
            header: _('blockab.variation.weight'),
            dataIndex: 'weight',
            sortable: true,
            width: 80
        }, {
            header: _('blockab.stats.picks'),
            dataIndex: 'picks',
            sortable: false,
            width: 100
        }, {
            header: _('blockab.stats.conversions'),
            dataIndex: 'conversions',
            sortable: false,
            width: 100
        }, {
            header: _('blockab.stats.conversion_rate'),
            dataIndex: 'conversion_rate',
            sortable: false,
            width: 100,
            renderer: function(value) {
                return value + '%';
            }
        }, {
            header: _('blockab.variation.active'),
            dataIndex: 'active',
            sortable: true,
            width: 60,
            renderer: function(value) {
                return (value == "1" || value === 1 || value === true) ? _('yes') : _('no');
            }
        }],
        tbar: [{
            text: _('blockab.variation_create'),
            handler: this.createVariation,
            scope: this
        }]
    });
    BlockAB.grid.Variations.superclass.constructor.call(this, config);
};

Ext.extend(BlockAB.grid.Variations, MODx.grid.Grid, {
    getMenu: function() {
        var m = [];
        m.push({
            text: _('blockab.variation_update'),
            handler: this.updateVariation
        });
        m.push({
            text: _('blockab.variation_duplicate'),
            handler: this.duplicateVariation
        });
        m.push('-');
        m.push({
            text: _('blockab.variation_remove'),
            handler: this.removeVariation
        });
        return m;
    },

    createVariation: function(btn, e) {
        if (!this.createVariationWindow) {
            this.createVariationWindow = MODx.load({
                xtype: 'blockab-window-variation-create',
                test: this.config.baseParams.test,
                listeners: {
                    'success': {fn: function() { this.refresh(); }, scope: this}
                }
            });
        }
        this.createVariationWindow.show(e.target);
    },

    updateVariation: function(btn, e) {
        if (!this.updateVariationWindow) {
            this.updateVariationWindow = MODx.load({
                xtype: 'blockab-window-variation-update',
                listeners: {
                    'success': {fn: function() { this.refresh(); }, scope: this}
                }
            });
        }
        this.updateVariationWindow.setValues(this.menu.record);
        this.updateVariationWindow.show(e.target);
    },

    duplicateVariation: function(btn, e) {
        var duplicateWindow = MODx.load({
            xtype: 'blockab-window-variation-duplicate',
            record: this.menu.record,
            listeners: {
                'success': {fn: function() { this.refresh(); }, scope: this}
            }
        });
        duplicateWindow.show(e.target);
    },

    removeVariation: function() {
        MODx.msg.confirm({
            title: _('blockab.variation_remove'),
            text: _('blockab.variation_remove_confirm'),
            url: this.config.url,
            params: {
                action: 'mgr/variation/remove',
                id: this.menu.record.id
            },
            listeners: {
                'success': {fn: function() { this.refresh(); }, scope: this}
            }
        });
    }
});

Ext.reg('blockab-grid-variations', BlockAB.grid.Variations);
