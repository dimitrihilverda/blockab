/**
 * BlockAB Preview Button
 *
 * Injects a "Preview varianten" button above the first MIGX TV on the
 * resource edit page. On click, reads live MIGX state, fetches variants
 * per ab_test_group, and opens a modal with one combobox per group.
 * "Open preview" builds a URL with ?ab_<group>=<variant> overrides and
 * opens it in a new tab.
 */
(function () {
    'use strict';

    var connectorUrl = (MODx.config && MODx.config.assets_url
        ? MODx.config.assets_url
        : '/assets/') + 'components/blockab/connector.php';

    var buttonInjected = false;

    /** Find all MIGX TV textareas on the page. Identified by JSON value
     *  that is an array whose first element has a MIGX_id field. */
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

    /** Collect unique non-empty ab_test_group values across all MIGX TVs. */
    function collectTestGroups(textareas) {
        var seen = {};
        textareas.forEach(function (ta) {
            try {
                var items = JSON.parse(ta.value);
                items.forEach(function (item) {
                    if (item && item.ab_test_group) {
                        seen[item.ab_test_group] = true;
                    }
                });
            } catch (e) { /* ignore */ }
        });
        return Object.keys(seen);
    }

    function isFormDirty() {
        var fp = Ext.getCmp('modx-panel-resource');
        return !!(fp && fp.isDirty && fp.isDirty());
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

    function openModal(groups, variantsByGroup, resourceUri) {
        var formItems = [];
        if (isFormDirty()) {
            formItems.push({
                xtype: 'displayfield',
                hideLabel: true,
                value: _('blockab.preview_dirty_warning'),
                style: 'color:#b6862e; padding:4px 0 8px 0; font-style:italic;'
            });
        }

        var choices = {};
        groups.forEach(function (g) {
            var data = [['', _('blockab.preview_site_default')]];
            (variantsByGroup[g] || []).forEach(function (v) {
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
                width: 220,
                listeners: {
                    select: function (combo, record) {
                        choices[g] = record.get('key');
                    }
                }
            });
        });

        var win = new Ext.Window({
            title: _('blockab.preview_modal_title'),
            modal: true,
            width: 440,
            autoHeight: true,
            layout: 'form',
            padding: 12,
            labelWidth: 160,
            items: formItems,
            buttons: [{
                text: _('blockab.preview_cancel'),
                handler: function () { win.close(); }
            }, {
                text: _('blockab.preview_open'),
                handler: function () {
                    var url = buildPreviewUrl(resourceUri, choices);
                    window.open(url, '_blank');
                    win.close();
                }
            }],
            buttonAlign: 'right'
        });
        win.show();
    }

    function onPreviewClick() {
        var textareas = findMigxTextareas();
        var groups = collectTestGroups(textareas);

        var resourceId = (MODx.request && MODx.request.id)
            ? MODx.request.id : null;
        if (!resourceId) {
            Ext.Msg.alert('BlockAB', 'Kon resource-ID niet bepalen.');
            return;
        }
        var siteUrl = MODx.config.site_url;
        if (siteUrl.charAt(siteUrl.length - 1) !== '/') siteUrl += '/';
        var resourceUri = siteUrl + 'index.php?id=' + resourceId;

        if (!groups.length) {
            Ext.Msg.alert(
                _('blockab.preview_modal_title'),
                _('blockab.preview_no_groups')
            );
            return;
        }

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
                var byGroup = (data && data.object && data.object.groups) || {};
                openModal(groups, byGroup, resourceUri);
            },
            failure: function () {
                Ext.Msg.alert('BlockAB', 'Kon variants niet laden (HTTP-fout).');
            }
        });
    }

    function injectButton() {
        if (buttonInjected) return;
        var textareas = findMigxTextareas();
        if (!textareas.length) return;

        var firstMigx = textareas[0];
        var formItem = firstMigx.closest
            ? firstMigx.closest('.x-form-item')
            : null;
        if (!formItem) {
            // Fallback: walk parents manually
            var p = firstMigx.parentNode;
            while (p && (!p.classList || !p.classList.contains('x-form-item'))) {
                p = p.parentNode;
            }
            formItem = p;
        }
        if (!formItem || !formItem.parentNode) return;

        var btnRow = document.createElement('div');
        btnRow.className = 'blockab-preview-button-row';
        btnRow.style.cssText = 'margin: 4px 0 8px 0;';
        btnRow.innerHTML = '<button type="button" '
            + 'style="background:#4a90e2;color:#fff;border:none;'
            + 'padding:6px 14px;border-radius:3px;cursor:pointer;'
            + 'font-size:12px;">'
            + _('blockab.preview_button')
            + '</button>';
        formItem.parentNode.insertBefore(btnRow, formItem);
        btnRow.querySelector('button').addEventListener('click', onPreviewClick);
        buttonInjected = true;
    }

    function init() {
        injectButton();
        // MIGX renders async — keep observing until we've injected
        var observer = new MutationObserver(function () {
            if (!buttonInjected) injectButton();
        });
        observer.observe(document.body, { childList: true, subtree: true });
        // Stop observing after 10s to avoid leaving observers attached forever
        setTimeout(function () { observer.disconnect(); }, 10000);
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
