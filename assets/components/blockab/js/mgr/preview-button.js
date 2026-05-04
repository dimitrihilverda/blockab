/**
 * BlockAB Preview Button
 *
 * Adds a "Preview varianten" Ext.Button to the resource edit page's
 * top toolbar (modx-action-buttons). On hover, the button opens a
 * menu with one submenu per ab_test_group; clicking a variant opens
 * the resource in a new tab with ?ab_<group>=<variant> applied.
 * The "Combineer varianten..." item opens a modal that lets the
 * manager pick a combination of variants across multiple groups.
 *
 * Same toolbar-integration pattern as Babel's language switcher.
 */
(function () {
    'use strict';

    var connectorUrl = (MODx.config && MODx.config.assets_url
        ? MODx.config.assets_url
        : '/assets/') + 'components/blockab/connector.php';

    var checkScheduled = false;
    var fetchInFlight = false;

    function _t(key, fallback) {
        var v = (typeof _ === 'function') ? _(key) : null;
        if (v === undefined || v === null || v === '' || v === key) return fallback;
        return v;
    }

    /** Find all MIGX TV textareas. Recognised by JSON value that is an
     *  array whose first element has a MIGX_id field. */
    function findMigxTextareas() {
        var nodes = document.querySelectorAll('textarea[id^="tv"]');
        var migx = [];
        Array.prototype.forEach.call(nodes, function (n) {
            if (!n.value) return;
            try {
                var parsed = JSON.parse(n.value);
                if (Array.isArray(parsed) && parsed.length > 0
                    && typeof parsed[0] === 'object' && parsed[0] !== null
                    && parsed[0].MIGX_id !== undefined) {
                    migx.push(n);
                }
            } catch (e) { /* not JSON, skip */ }
        });
        return migx;
    }

    /** Walk all MIGX TVs and return per-group which variant keys are
     *  actually placed in the resource. Blocks without an ab_test_variant
     *  are ignored — they are incomplete configurations and there is
     *  nothing to preview for them.
     *  @returns {Object<string, string[]>} { group_key: [variant_keys...] } */
    function collectGroupVariantUsage(textareas) {
        var usage = {};
        textareas.forEach(function (ta) {
            try {
                var items = JSON.parse(ta.value);
                items.forEach(function (item) {
                    if (!item || !item.ab_test_group || !item.ab_test_variant) return;
                    var g = item.ab_test_group;
                    var v = String(item.ab_test_variant);
                    if (!usage[g]) usage[g] = {};
                    usage[g][v] = true;
                });
            } catch (e) { /* ignore */ }
        });
        var result = {};
        Object.keys(usage).forEach(function (g) {
            result[g] = Object.keys(usage[g]);
        });
        return result;
    }

    function isFormDirty() {
        var fp = Ext.getCmp('modx-panel-resource');
        return !!(fp && fp.isDirty && fp.isDirty());
    }

    function getResourceUri() {
        var resourceId = (MODx.request && MODx.request.id)
            ? MODx.request.id : null;
        if (!resourceId) return null;
        var siteUrl = MODx.config.site_url;
        if (siteUrl.charAt(siteUrl.length - 1) !== '/') siteUrl += '/';
        return siteUrl + 'index.php?id=' + resourceId;
    }

    function buildPreviewUrl(resourceUri, choices) {
        var params = [];
        Object.keys(choices).forEach(function (group) {
            if (choices[group]) {
                params.push('ab_' + encodeURIComponent(group)
                    + '=' + encodeURIComponent(choices[group]));
            }
        });
        if (!params.length) return resourceUri;
        var sep = resourceUri.indexOf('?') >= 0 ? '&' : '?';
        return resourceUri + sep + params.join('&');
    }

    function fetchVariants(groups, callback) {
        Ext.Ajax.request({
            url: connectorUrl,
            method: 'POST',
            params: {
                action: 'mgr/test/getvariantsforpreview',
                groups: groups.join(',')
            },
            success: function (resp) {
                var data = null;
                try { data = Ext.decode(resp.responseText); } catch (e) {}
                callback((data && data.object && data.object.groups) || {});
            },
            failure: function () { callback({}); }
        });
    }

    function buildMenuItems(groups, variantsByGroup, usage, resourceUri) {
        var items = [];

        // Subtle dirty-state notice — same info as the modal warning, but
        // compact enough to fit a menu item. Hover for the full sentence.
        if (isFormDirty()) {
            items.push({
                text: '⚠ ' + _t('blockab.preview_dirty_short', 'Resource niet opgeslagen'),
                disabled: true,
                qtip: _t('blockab.preview_dirty_warning',
                    'Sla de resource eerst op om nieuwe blokken in de preview te zien')
            });
            items.push('-');
        }

        groups.forEach(function (g) {
            var allVariants = variantsByGroup[g] || [];
            var usedKeys = usage[g] || [];
            // Only show variants that are both defined in the test AND
            // actually placed in this resource's MIGX blocks.
            var available = allVariants.filter(function (v) {
                return usedKeys.indexOf(String(v.key)) >= 0;
            });
            if (!available.length) {
                items.push({
                    text: g,
                    disabled: true,
                    qtip: _t('blockab.preview_no_variants', 'Geen actieve test voor deze groep')
                });
                return;
            }
            var subItems = available.map(function (v) {
                return {
                    text: v.key + ' — ' + v.name,
                    handler: function () {
                        var choices = {};
                        choices[g] = v.key;
                        window.open(buildPreviewUrl(resourceUri, choices), '_blank');
                    }
                };
            });
            items.push({
                text: g,
                menu: { items: subItems }
            });
        });

        // Combine option only makes sense with 2+ groups
        if (groups.length > 1) {
            items.push('-');
            items.push({
                text: _t('blockab.preview_combine', 'Combineer varianten...'),
                handler: function () {
                    openCombineModal(groups, variantsByGroup, usage, resourceUri);
                }
            });
        }

        return items;
    }

    function openCombineModal(groups, variantsByGroup, usage, resourceUri) {
        var formItems = [];
        if (isFormDirty()) {
            formItems.push({
                xtype: 'displayfield',
                hideLabel: true,
                value: _t('blockab.preview_dirty_warning',
                    'Sla de resource eerst op om nieuwe blokken in de preview te zien'),
                style: 'color:#b6862e; padding:4px 0 8px 0; font-style:italic;'
            });
        }

        var choices = {};
        groups.forEach(function (g) {
            var data = [['', _t('blockab.preview_site_default', 'Site default (geen override)')]];
            var usedKeys = usage[g] || [];
            (variantsByGroup[g] || []).forEach(function (v) {
                if (usedKeys.indexOf(String(v.key)) < 0) return;
                data.push([v.key, v.key + ' — ' + v.name]);
            });
            formItems.push({
                xtype: 'combo',
                fieldLabel: g,
                store: new Ext.data.SimpleStore({
                    fields: ['key', 'display'],
                    data: data
                }),
                valueField: 'key',
                displayField: 'display',
                mode: 'local',
                value: '',
                editable: false,
                triggerAction: 'all',
                width: 280,
                listeners: {
                    select: function (combo, record) {
                        choices[g] = record.get('key');
                    }
                }
            });
        });

        var win = new Ext.Window({
            title: _t('blockab.preview_modal_title', 'Variant-combinatie kiezen'),
            modal: true,
            width: 620,
            autoHeight: true,
            layout: 'form',
            padding: 16,
            labelWidth: 240,
            labelStyle: 'padding-right:12px;',
            items: formItems,
            buttons: [{
                text: _t('blockab.preview_cancel', 'Annuleren'),
                handler: function () { win.close(); }
            }, {
                text: _t('blockab.preview_open', 'Preview openen'),
                handler: function () {
                    window.open(buildPreviewUrl(resourceUri, choices), '_blank');
                    win.close();
                }
            }],
            buttonAlign: 'right'
        });
        win.show();
    }

    /** True when our button is actually attached to the current toolbar.
     *  We don't trust a sticky boolean — MODX re-renders the toolbar after
     *  save, so the button can vanish even though we 'injected' it earlier. */
    function buttonInToolbar() {
        var toolbar = Ext.getCmp('modx-action-buttons');
        if (!toolbar || !toolbar.items) return false;
        var found = false;
        toolbar.items.each(function (item) {
            if (item && item.id === 'blockab-preview-button') {
                found = true;
                return false; // stop iteration
            }
        });
        return found;
    }

    /** Cached variants per group, so we don't re-fetch on every hover.
     *  Reset (set to null) when the toolbar is rebuilt and a new button
     *  is injected. Keeps a stale entry around for previously-known groups
     *  even after MIGX changes — fresh menu builds will simply skip groups
     *  that don't have cached variants until the next page load. */
    var cachedVariants = null;

    function applyMenu(btn, resourceUri) {
        if (!btn || !btn.menu) return;
        var freshUsage = collectGroupVariantUsage(findMigxTextareas());
        var freshGroups = Object.keys(freshUsage);
        var newItems = buildMenuItems(freshGroups, cachedVariants || {}, freshUsage, resourceUri);
        // Empty menu would close immediately on hover — ensure we always
        // have at least one item.
        if (!newItems.length) {
            newItems = [{ text: _t('blockab.preview_loading', 'Laden...'), disabled: true }];
        }
        btn.menu.removeAll();
        newItems.forEach(function (item) { btn.menu.add(item); });
    }

    function injectButton() {
        // No double-inject if we're already in the toolbar
        if (buttonInToolbar()) return;

        var textareas = findMigxTextareas();
        if (!textareas.length) return;

        var usage = collectGroupVariantUsage(textareas);
        var groups = Object.keys(usage);
        if (!groups.length) return;

        var toolbar = Ext.getCmp('modx-action-buttons');
        if (!toolbar) return;

        // Stale instance from a previous render — clean up so insertButton
        // doesn't trip on a duplicate id.
        var orphan = Ext.getCmp('blockab-preview-button');
        if (orphan) { orphan.destroy(); }

        var resourceUri = getResourceUri();
        if (!resourceUri) return;

        // Inject with a "loading" stub menu, then replace items once
        // the variants AJAX returns. Pattern: same as Babel's switcher.
        var btn = new Ext.Button({
            id: 'blockab-preview-button',
            text: _t('blockab.preview_button', 'Preview varianten'),
            menu: new Ext.menu.Menu({
                items: [{ text: _t('blockab.preview_loading', 'Laden...'), disabled: true }],
                listeners: {
                    // Re-build items on every hover so dirty state, MIGX
                    // additions, and variant placement updates are always
                    // current — without re-fetching variants from the DB.
                    beforeshow: function () { applyMenu(btn, resourceUri); }
                }
            }),
            listeners: {
                mouseover: function (b) { b.showMenu(); }
            }
        });

        toolbar.insertButton(0, [btn]);
        toolbar.doLayout();

        if (fetchInFlight) return;
        fetchInFlight = true;
        fetchVariants(groups, function (variantsByGroup) {
            fetchInFlight = false;
            cachedVariants = variantsByGroup;
            var liveBtn = Ext.getCmp('blockab-preview-button');
            if (!liveBtn) return; // toolbar got rebuilt while AJAX was in flight
            applyMenu(liveBtn, resourceUri);
        });
    }

    /** Throttle DOM-mutation reactions to at most one per 200ms. */
    function scheduleCheck() {
        if (checkScheduled) return;
        checkScheduled = true;
        setTimeout(function () {
            checkScheduled = false;
            injectButton();
        }, 200);
    }

    function init() {
        injectButton();
        var observer = new MutationObserver(scheduleCheck);
        observer.observe(document.body, { childList: true, subtree: true });
        // Observer keeps running for the lifetime of the page so the button
        // re-appears when MODX rebuilds the toolbar (e.g. after save).
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
