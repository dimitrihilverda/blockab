BlockAB.window.CreateVariation = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('blockab.variation_create'),
        url: BlockAB.config.connectorUrl,
        baseParams: {
            action: 'mgr/variation/create',
            test: config.test || BlockAB.config.test_id
        },
        fields: [{
            xtype: 'hidden',
            name: 'test',
            value: config.test || BlockAB.config.test_id
        }, {
            xtype: 'modx-combo',
            fieldLabel: _('blockab.variation.variant_key') + ' <span style="color:red;">*</span>',
            name: 'variant_key',
            hiddenName: 'variant_key',
            anchor: '100%',
            allowBlank: false,
            editable: false,
            forceSelection: true,
            description: _('blockab.variation.variant_key_desc'),
            url: BlockAB.config.connectorUrl,
            baseParams: {
                action: 'mgr/variation/getavailablekeys',
                test: config.test || BlockAB.config.test_id
            },
            fields: ['key', 'display'],
            displayField: 'display',
            valueField: 'key',
            store: new Ext.data.JsonStore({
                url: BlockAB.config.connectorUrl,
                baseParams: {
                    action: 'mgr/variation/getavailablekeys',
                    test: config.test || BlockAB.config.test_id
                },
                fields: ['key', 'display'],
                root: 'results',
                autoLoad: true
            })
        }, {
            xtype: 'textfield',
            fieldLabel: _('blockab.variation.name') + ' <span style="color:red;">*</span>',
            name: 'name',
            anchor: '100%',
            allowBlank: false
        }, {
            xtype: 'textarea',
            fieldLabel: _('blockab.variation.description'),
            name: 'description',
            anchor: '100%',
            height: 100
        }, {
            xtype: 'numberfield',
            fieldLabel: _('blockab.variation.weight'),
            name: 'weight',
            anchor: '100%',
            value: 100,
            description: _('blockab.variation.weight_desc')
        }, {
            xtype: 'xcheckbox',
            fieldLabel: _('blockab.variation.active'),
            name: 'active',
            inputValue: 1,
            checked: true
        }]
    });
    BlockAB.window.CreateVariation.superclass.constructor.call(this, config);
};
Ext.extend(BlockAB.window.CreateVariation, MODx.Window);
Ext.reg('blockab-window-variation-create', BlockAB.window.CreateVariation);

BlockAB.window.UpdateVariation = function(config) {
    config = config || {};

    // Generate unique ID for this window instance
    var uniqueId = Ext.id();
    var comboId = 'blockab-update-variant-key-combo-' + uniqueId;

    // Store combo ID on window for use in setValues
    this.variantKeyComboId = comboId;

    Ext.applyIf(config, {
        title: _('blockab.variation_update'),
        url: BlockAB.config.connectorUrl,
        baseParams: {
            action: 'mgr/variation/update'
        },
        fields: [{
            xtype: 'hidden',
            name: 'id'
        }, {
            xtype: 'hidden',
            name: 'test'
        }, {
            xtype: 'modx-combo',
            fieldLabel: _('blockab.variation.variant_key') + ' <span style="color:red;">*</span>',
            name: 'variant_key',
            hiddenName: 'variant_key',
            id: comboId,
            anchor: '100%',
            allowBlank: false,
            editable: false,
            forceSelection: true,
            description: _('blockab.variation.variant_key_desc'),
            url: BlockAB.config.connectorUrl,
            fields: ['key', 'display'],
            displayField: 'display',
            valueField: 'key',
            store: new Ext.data.JsonStore({
                url: BlockAB.config.connectorUrl,
                baseParams: {
                    action: 'mgr/variation/getavailablekeys'
                },
                fields: ['key', 'display'],
                root: 'results',
                autoLoad: false
            })
        }, {
            xtype: 'textfield',
            fieldLabel: _('blockab.variation.name'),
            name: 'name',
            anchor: '100%',
            allowBlank: false
        }, {
            xtype: 'textarea',
            fieldLabel: _('blockab.variation.description'),
            name: 'description',
            anchor: '100%',
            height: 100
        }, {
            xtype: 'numberfield',
            fieldLabel: _('blockab.variation.weight'),
            name: 'weight',
            anchor: '100%',
            description: _('blockab.variation.weight_desc')
        }, {
            xtype: 'xcheckbox',
            fieldLabel: _('blockab.variation.active'),
            name: 'active',
            inputValue: 1,
            uncheckedValue: 0
        }]
    });
    BlockAB.window.UpdateVariation.superclass.constructor.call(this, config);
};
Ext.extend(BlockAB.window.UpdateVariation, MODx.Window, {
    // Override setValues to convert string "1"/"0" to boolean for checkbox and load available keys
    setValues: function(values) {
        if (values && values.active !== undefined) {
            // Convert string "1" or number 1 to true, anything else to false
            values.active = (values.active == "1" || values.active === 1 || values.active === true);
        }

        // Load available keys for this variation's test, including current key
        if (values && values.test && values.variant_key) {
            var combo = Ext.getCmp(this.variantKeyComboId);
            var currentKey = values.variant_key;

            if (combo && combo.store) {
                combo.store.baseParams.test = values.test;
                combo.store.load({
                    callback: function(records) {
                        // Add current key to the list if it's not already there
                        var keyExists = false;
                        for (var i = 0; i < records.length; i++) {
                            if (records[i].data.key === currentKey) {
                                keyExists = true;
                                break;
                            }
                        }
                        if (!keyExists) {
                            combo.store.add(new combo.store.recordType({
                                key: currentKey,
                                display: currentKey
                            }));
                        }
                        combo.setValue(currentKey);
                    }
                });
            }
        }

        BlockAB.window.UpdateVariation.superclass.setValues.call(this, values);
    }
});
Ext.reg('blockab-window-variation-update', BlockAB.window.UpdateVariation);

BlockAB.window.DuplicateVariation = function(config) {
    config = config || {};
    var testId = config.record.test || BlockAB.config.test_id;

    // Generate unique ID for this window instance
    var uniqueId = Ext.id();
    var comboId = 'blockab-duplicate-variant-key-combo-' + uniqueId;

    Ext.applyIf(config, {
        title: _('blockab.variation_duplicate'),
        url: BlockAB.config.connectorUrl,
        baseParams: {
            action: 'mgr/variation/duplicate',
            id: config.record.id
        },
        fields: [{
            xtype: 'hidden',
            name: 'id',
            value: config.record.id
        }, {
            xtype: 'modx-combo',
            fieldLabel: _('blockab.variation.variant_key') + ' <span style="color:red;">*</span>',
            name: 'variant_key',
            hiddenName: 'variant_key',
            id: comboId,
            anchor: '100%',
            allowBlank: false,
            editable: false,
            forceSelection: true,
            description: _('blockab.variation.variant_key_desc'),
            url: BlockAB.config.connectorUrl,
            baseParams: {
                action: 'mgr/variation/getavailablekeys',
                test: testId
            },
            fields: ['key', 'display'],
            displayField: 'display',
            valueField: 'key',
            store: new Ext.data.JsonStore({
                url: BlockAB.config.connectorUrl,
                baseParams: {
                    action: 'mgr/variation/getavailablekeys',
                    test: testId
                },
                fields: ['key', 'display'],
                root: 'results',
                autoLoad: true,
                listeners: {
                    'load': function(store, records) {
                        // Auto-select first available key
                        if (records && records.length > 0) {
                            var combo = Ext.getCmp(comboId);
                            if (combo) {
                                combo.setValue(records[0].data.key);
                            }
                        }
                    }
                }
            })
        }, {
            xtype: 'textfield',
            fieldLabel: _('blockab.variation.name') + ' <span style="color:red;">*</span>',
            name: 'name',
            anchor: '100%',
            allowBlank: false,
            value: config.record.name + ' (Copy)'
        }]
    });
    BlockAB.window.DuplicateVariation.superclass.constructor.call(this, config);
};
Ext.extend(BlockAB.window.DuplicateVariation, MODx.Window);
Ext.reg('blockab-window-variation-duplicate', BlockAB.window.DuplicateVariation);
