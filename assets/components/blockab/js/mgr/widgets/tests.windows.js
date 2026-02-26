BlockAB.window.CreateTest = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('blockab.test_create'),
        url: BlockAB.config.connectorUrl,
        baseParams: {
            action: 'mgr/test/create'
        },
        fields: [{
            xtype: 'textfield',
            fieldLabel: _('blockab.test.name') + ' <span style="color:red;">*</span>',
            name: 'name',
            anchor: '100%',
            allowBlank: false,
            id: 'blockab-test-name-field',
            listeners: {
                'change': {
                    fn: function(field, newValue) {
                        // Auto-fill test_group field if it's empty
                        var testGroupField = Ext.getCmp('blockab-test-group-field');
                        if (testGroupField && !testGroupField.getValue()) {
                            var formatted = newValue.toLowerCase()
                                .replace(/\s+/g, '_')
                                .replace(/[^a-z0-9_]/g, '');
                            testGroupField.setValue(formatted);
                        }
                    }
                }
            }
        }, {
            xtype: 'textfield',
            fieldLabel: _('blockab.test.test_group') + ' <span style="color:red;">*</span>',
            name: 'test_group',
            anchor: '100%',
            allowBlank: false,
            id: 'blockab-test-group-field',
            description: _('blockab.test.test_group_desc'),
            listeners: {
                'change': {
                    fn: function(field, newValue) {
                        // Auto-format: lowercase, replace spaces with underscores, remove special chars
                        var formatted = newValue.toLowerCase()
                            .replace(/\s+/g, '_')
                            .replace(/[^a-z0-9_]/g, '');
                        if (formatted !== newValue) {
                            field.setValue(formatted);
                        }
                    }
                }
            }
        }, {
            xtype: 'textarea',
            fieldLabel: _('blockab.test.description'),
            name: 'description',
            anchor: '100%',
            height: 100
        }, {
            xtype: 'xcheckbox',
            fieldLabel: _('blockab.test.active'),
            name: 'active',
            inputValue: 1,
            checked: true
        }, {
            xtype: 'xcheckbox',
            fieldLabel: _('blockab.test.smartoptimize'),
            name: 'smartoptimize',
            inputValue: 1,
            checked: true,
            description: _('blockab.test.smartoptimize_desc')
        }, {
            xtype: 'numberfield',
            fieldLabel: _('blockab.test.threshold'),
            name: 'threshold',
            anchor: '100%',
            value: 100,
            description: _('blockab.test.threshold_desc')
        }, {
            xtype: 'numberfield',
            fieldLabel: _('blockab.test.randomize'),
            name: 'randomize',
            anchor: '100%',
            value: 25,
            minValue: 0,
            maxValue: 100,
            description: _('blockab.test.randomize_desc')
        }]
    });
    BlockAB.window.CreateTest.superclass.constructor.call(this, config);
};
Ext.extend(BlockAB.window.CreateTest, MODx.Window);
Ext.reg('blockab-window-test-create', BlockAB.window.CreateTest);

BlockAB.window.UpdateTest = function(config) {
    config = config || {};
    Ext.applyIf(config, {
        title: _('blockab.test_update'),
        url: BlockAB.config.connectorUrl,
        baseParams: {
            action: 'mgr/test/update'
        },
        fields: [{
            xtype: 'hidden',
            name: 'id'
        }, {
            xtype: 'textfield',
            fieldLabel: _('blockab.test.name') + ' <span style="color:red;">*</span>',
            name: 'name',
            anchor: '100%',
            allowBlank: false
        }, {
            xtype: 'textfield',
            fieldLabel: _('blockab.test.test_group') + ' <span style="color:red;">*</span>',
            name: 'test_group',
            anchor: '100%',
            allowBlank: false,
            description: _('blockab.test.test_group_desc'),
            listeners: {
                'change': {
                    fn: function(field, newValue) {
                        // Auto-format: lowercase, replace spaces with underscores, remove special chars
                        var formatted = newValue.toLowerCase()
                            .replace(/\s+/g, '_')
                            .replace(/[^a-z0-9_]/g, '');
                        if (formatted !== newValue) {
                            field.setValue(formatted);
                        }
                    }
                }
            }
        }, {
            xtype: 'textarea',
            fieldLabel: _('blockab.test.description'),
            name: 'description',
            anchor: '100%',
            height: 100
        }, {
            xtype: 'xcheckbox',
            fieldLabel: _('blockab.test.active'),
            name: 'active',
            inputValue: 1,
            uncheckedValue: 0
        }, {
            xtype: 'xcheckbox',
            fieldLabel: _('blockab.test.smartoptimize'),
            name: 'smartoptimize',
            inputValue: 1,
            uncheckedValue: 0,
            description: _('blockab.test.smartoptimize_desc')
        }, {
            xtype: 'numberfield',
            fieldLabel: _('blockab.test.threshold'),
            name: 'threshold',
            anchor: '100%',
            description: _('blockab.test.threshold_desc')
        }, {
            xtype: 'numberfield',
            fieldLabel: _('blockab.test.randomize'),
            name: 'randomize',
            anchor: '100%',
            minValue: 0,
            maxValue: 100,
            description: _('blockab.test.randomize_desc')
        }]
    });
    BlockAB.window.UpdateTest.superclass.constructor.call(this, config);
};
Ext.extend(BlockAB.window.UpdateTest, MODx.Window, {
    // Override setValues to convert string "1"/"0" to boolean for checkboxes
    setValues: function(values) {
        if (values) {
            if (values.active !== undefined) {
                values.active = (values.active == "1" || values.active === 1 || values.active === true);
            }
            if (values.smartoptimize !== undefined) {
                values.smartoptimize = (values.smartoptimize == "1" || values.smartoptimize === 1 || values.smartoptimize === true);
            }
        }
        BlockAB.window.UpdateTest.superclass.setValues.call(this, values);
    }
});
Ext.reg('blockab-window-test-update', BlockAB.window.UpdateTest);

BlockAB.window.DuplicateTest = function(config) {
    config = config || {};

    // Generate unique IDs for this window instance
    var uniqueId = Ext.id();
    var nameFieldId = 'blockab-duplicate-test-name-field-' + uniqueId;
    var groupFieldId = 'blockab-duplicate-test-group-field-' + uniqueId;

    // Track if test_group was manually edited
    this.testGroupManuallyEdited = false;

    Ext.applyIf(config, {
        title: _('blockab.test_duplicate'),
        url: BlockAB.config.connectorUrl,
        baseParams: {
            action: 'mgr/test/duplicate',
            id: config.record.id
        },
        fields: [{
            xtype: 'hidden',
            name: 'id',
            value: config.record.id
        }, {
            xtype: 'textfield',
            fieldLabel: _('blockab.test.name') + ' <span style="color:red;">*</span>',
            name: 'name',
            anchor: '100%',
            allowBlank: false,
            value: config.record.name + ' (Copy)',
            id: nameFieldId,
            listeners: {
                'change': {
                    fn: function(field, newValue) {
                        var win = field.ownerCt.ownerCt;
                        var testGroupField = Ext.getCmp(groupFieldId);

                        // Only auto-fill if test_group wasn't manually edited
                        if (testGroupField && !win.testGroupManuallyEdited) {
                            var formatted = newValue.toLowerCase()
                                .replace(/\s+/g, '_')
                                .replace(/[^a-z0-9_]/g, '');
                            testGroupField.setValue(formatted);
                        }
                    }
                }
            }
        }, {
            xtype: 'textfield',
            fieldLabel: _('blockab.test.test_group') + ' <span style="color:red;">*</span>',
            name: 'test_group',
            anchor: '100%',
            allowBlank: false,
            value: config.record.test_group + '_copy',
            id: groupFieldId,
            description: _('blockab.test.test_group_desc'),
            listeners: {
                'change': {
                    fn: function(field, newValue) {
                        var win = field.ownerCt.ownerCt;

                        // Auto-format: lowercase, replace spaces with underscores, remove special chars
                        var formatted = newValue.toLowerCase()
                            .replace(/\s+/g, '_')
                            .replace(/[^a-z0-9_]/g, '');

                        if (formatted !== newValue) {
                            field.setValue(formatted);
                        }
                    }
                },
                'focus': {
                    fn: function() {
                        var win = this.ownerCt.ownerCt;
                        // Mark as manually edited when user focuses on the field
                        win.testGroupManuallyEdited = true;
                    }
                }
            }
        }]
    });
    BlockAB.window.DuplicateTest.superclass.constructor.call(this, config);
};
Ext.extend(BlockAB.window.DuplicateTest, MODx.Window);
Ext.reg('blockab-window-test-duplicate', BlockAB.window.DuplicateTest);
