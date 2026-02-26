/**
 * BlockAB MIGX Cascade
 *
 * Maakt ab_test_variant dynamisch afhankelijk van ab_test_group.
 * Leest welke varianten al in gebruik zijn uit de MIGX-textarea (tv{id})
 * en sluit die uit. De eerste vrije variant wordt automatisch geselecteerd.
 */
(function () {
    'use strict';

    var connectorUrl = (MODx.config && MODx.config.assets_url
        ? MODx.config.assets_url
        : '/assets/') + 'components/blockab/connector.php';

    /**
     * Leest alle MIGX-items rechtstreeks uit de verborgen textarea (tv{tvId})
     * en geeft terug welke varianten voor de gegeven testgroep al bezet zijn.
     * Filtert op hetzelfde bloktype (MIGX_formname) zodat andere bloktypes
     * geen invloed hebben op de beschikbare varianten.
     *
     * @param  {string} testGroup        - De testgroepsleutel
     * @param  {string} currentMigxId    - MIGX_id van het huidige item (wordt overgeslagen)
     * @param  {string} tvNumericId      - Numeriek MODX TV-id (bijv. "699")
     * @param  {string} currentFormname  - MIGX_formname van het huidige item (bijv. "block_hero")
     * @returns {string[]}
     */
    function getUsedVariants(testGroup, currentMigxId, tvNumericId, currentFormname) {
        var used = [];

        // Lees de ruwe JSON uit de MIGX-textarea
        var textarea = document.getElementById('tv' + tvNumericId);
        if (!textarea || !textarea.value) return used;

        var allItems;
        try {
            allItems = Ext.decode(textarea.value);
        } catch (e) {
            console.warn('[BlockAB] Kon MIGX-textarea niet parsen voor tv' + tvNumericId);
            return used;
        }

        Ext.each(allItems, function (item) {
            if (String(item.MIGX_id) === String(currentMigxId)) return; // huidig item overslaan
            // Alleen hetzelfde bloktype meenemen
            if (currentFormname && item.MIGX_formname !== currentFormname) return;
            if (item.ab_test_group === testGroup && item.ab_test_variant) {
                used.push(item.ab_test_variant);
            }
        });

        return used;
    }

    /**
     * Laadt varianten voor de testgroep, filtert bezette eruit
     * en selecteert automatisch de eerste vrije.
     */
    function loadVariants(testGroup, variantCombo, currentMigxId, tvNumericId, currentFormname) {
        if (!testGroup) {
            variantCombo.store.removeAll();
            variantCombo.setValue('');
            return;
        }

        Ext.Ajax.request({
            url: connectorUrl,
            params: {
                action: 'web/variation/getbytestgroup',
                test_group: testGroup
            },
            success: function (response) {
                var data;
                try {
                    data = Ext.decode(response.responseText);
                } catch (e) {
                    return;
                }

                if (!data || !data.results) return;

                var store      = variantCombo.store;
                var valueField = variantCombo.valueField || 'value';
                var textField  = variantCombo.displayField || 'text';
                var oldValue   = variantCombo.getValue();

                var usedVariants = getUsedVariants(testGroup, currentMigxId, tvNumericId, currentFormname);

                store.removeAll();

                var firstFree = null;
                Ext.each(data.results, function (item) {
                    if (usedVariants.indexOf(item.variant_key) !== -1) return; // al in gebruik

                    var rec       = {};
                    rec[valueField] = item.variant_key;
                    rec[textField]  = item.name;
                    store.add(new store.recordType(rec));

                    if (firstFree === null) firstFree = item.variant_key;
                });

                // Huidige waarde bewaren als die nog vrij is, anders eerste vrije kiezen
                var currentStillFree = store.find(valueField, oldValue) !== -1;
                if (currentStillFree) {
                    variantCombo.setValue(oldValue);
                } else if (firstFree !== null) {
                    variantCombo.setValue(firstFree);
                } else {
                    variantCombo.setValue('');
                }
            }
        });
    }

    /**
     * Loopt omhoog door de DOM om het MIGX-vensterelement te vinden
     * (id="modx-window-mi-grid-update-{tvId}") en geeft het numerieke TV-id terug.
     *
     * @param  {Element} startEl
     * @returns {string|null}
     */
    function findTvIdFromWindow(startEl) {
        var el = startEl;
        while (el && el !== document.body) {
            if (el.id) {
                var match = el.id.match(/modx-window-mi-grid-update-(\d+)/);
                if (match) return match[1];
            }
            el = el.parentNode;
        }
        return null;
    }

    /**
     * Zoekt het veldmapping-element in een toegevoegde DOM-node.
     * Controleert de node zelf én zijn descendants.
     */
    function findFieldMapEl(node) {
        if (!node || node.nodeType !== 1) return null;
        if (node.tagName === 'INPUT' && node.getAttribute('name') === 'mulititems_grid_item_fields') {
            return node;
        }
        return node.querySelector
            ? node.querySelector('input[name="mulititems_grid_item_fields"]')
            : null;
    }

    /**
     * Stelt de cascade in voor een geladen MIGX-formulier.
     */
    function setupCascade(fieldMapEl) {
        var fields;
        try {
            fields = Ext.decode(fieldMapEl.value);
        } catch (e) {
            return;
        }

        var groupTvId   = null;
        var variantTvId = null;

        Ext.each(fields, function (field) {
            if (field.field === 'ab_test_group')   groupTvId   = field.tv_id;
            if (field.field === 'ab_test_variant') variantTvId = field.tv_id;
        });

        if (!groupTvId || !variantTvId) return;

        // Numeriek TV-id afleiden uit het MIGX-venster-ID (id="modx-window-mi-grid-update-{tvId}")
        var tvNumericId = findTvIdFromWindow(fieldMapEl);
        if (!tvNumericId) {
            console.warn('[BlockAB] Kon TV-id niet bepalen uit venster-ID.');
            return;
        }

        // MIGX_id van het huidige item
        var searchRoot    = fieldMapEl.parentNode || document.body;
        var migxIdEl      = searchRoot.querySelector
            ? searchRoot.querySelector('input[name="tvmigxid"]')
            : null;
        var currentMigxId = migxIdEl ? migxIdEl.value : null;

        // MIGX_formname van het huidige item afleiden uit de textarea
        var currentFormname = null;
        if (currentMigxId) {
            var textarea = document.getElementById('tv' + tvNumericId);
            if (textarea && textarea.value) {
                try {
                    var allItems = Ext.decode(textarea.value);
                    Ext.each(allItems, function (item) {
                        if (String(item.MIGX_id) === String(currentMigxId)) {
                            currentFormname = item.MIGX_formname || null;
                            return false; // stop iterating
                        }
                    });
                } catch (e) { /* ignore */ }
            }
        }


        setTimeout(function () {
            var groupCombo   = Ext.getCmp('tv' + groupTvId);
            var variantCombo = Ext.getCmp('tv' + variantTvId);

            if (!groupCombo || !variantCombo) {
                console.warn('[BlockAB] Combos niet gevonden. Verwachtte:', 'tv' + groupTvId, 'en', 'tv' + variantTvId);
                return;
            }

            if (groupCombo._blockabCascadeInit) return;
            groupCombo._blockabCascadeInit = true;

            groupCombo.on('select', function (combo) {
                loadVariants(combo.getValue(), variantCombo, currentMigxId, tvNumericId, currentFormname);
            });

            var currentGroup = groupCombo.getValue();
            if (currentGroup) {
                loadVariants(currentGroup, variantCombo, currentMigxId, tvNumericId, currentFormname);
            }

        }, 200);
    }

    /**
     * Zoekt het veldmapping-element binnen een panel body DOM-node.
     */
    function findFieldMapInPanel(bodyDom) {
        if (!bodyDom || !bodyDom.querySelector) return null;
        return bodyDom.querySelector('input[name="mulititems_grid_item_fields"]');
    }

    Ext.onReady(function () {
        // Primaire methode: hook in op MODx.FormPanel load-event.
        // Dit vuurt nadat de autoLoad-XHR klaar is en de HTML in de panel body is gezet.
        if (typeof MODx !== 'undefined' && MODx.FormPanel) {
            var origAfterRender = MODx.FormPanel.prototype.afterRender;
            MODx.FormPanel.prototype.afterRender = function () {
                origAfterRender.apply(this, arguments);
                var panel = this;
                panel.on('load', function () {
                    if (!panel.body || !panel.body.dom) return;
                    var fieldMapEl = findFieldMapInPanel(panel.body.dom);
                    if (fieldMapEl) {
                        setupCascade(fieldMapEl);
                    }
                });
            };
        }

        // Fallback: MutationObserver (vangt edge-cases op)
        var observer = new MutationObserver(function (mutations) {
            Ext.each(mutations, function (mutation) {
                Ext.each(mutation.addedNodes, function (node) {
                    var fieldMapEl = findFieldMapEl(node);
                    if (fieldMapEl) {
                        setupCascade(fieldMapEl);
                    }
                });
            });
        });

        observer.observe(document.body, {
            childList: true,
            subtree: true
        });
    });

}());
