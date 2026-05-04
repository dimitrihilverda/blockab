# BlockAB Variant Preview Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Manager-users kunnen een variant-combinatie kiezen en de resource met die combo openen via een knop op de resource-edit pagina, en de `BlockABMigxCascade` plugin wordt voortaan automatisch door het transport-pakket geïnstalleerd.

**Architecture:** GET-param override in `BlockAB::shouldShowBlock()` (gated op `view_unpublished`), plus een `preview-button.js` die door de `BlockABMigxCascade` plugin geladen wordt en een nieuwe `getvariantsforpreview` processor aanroept om de modal te vullen. Plugin wordt nu geregistreerd via een uitgebreide `_build/build.transport.php` met `modPlugin` + `modPluginEvent` als child-objects in de category vehicle.

**Tech Stack:** MODX Revolution 2.8.x · PHP 7.4 · ExtJS 3 · MIGX. Geen unit-test framework in dit pakket — verifiëren gebeurt via build + install op lokale Docker en handmatige browsercheck.

**Spec:** `docs/superpowers/specs/2026-05-04-blockab-variant-preview-design.md`

**Versie na deze release:** `1.1.3-pl`

**Build commando (vanaf project-root):**
```
MSYS_NO_PATHCONV=1 docker exec moving-innl-movingin_php-1 php /var/www/_packages/blockab/_build/build.transport.php /var/www
```

**Werkdirectory:** alle paden in dit plan zijn relatief aan `_packages/blockab/` tenzij anders aangegeven. Git-commits gebeuren binnen die nested repo (`origin = git@github.com:dimitrihilverda/blockab.git`, branch `main`).

---

## File Structure

| Bestand | Verantwoordelijkheid |
|---|---|
| `core/components/blockab/model/blockab/blockab.class.php` | Domain logic; `getPreviewOverride()` + integratie in `shouldShowBlock()` |
| `core/components/blockab/elements/plugins/blockabmigxcascade.plugin.php` | Plugin source; loadt `migx-cascade.js` en `preview-button.js` op `OnDocFormPrerender` |
| `core/components/blockab/processors/mgr/test/getvariantsforpreview.class.php` | Connector-processor; geeft variants per groep terug voor de preview-modal |
| `assets/components/blockab/js/mgr/preview-button.js` | Manager UI; injecteert knop, modal, bouwt preview-URL |
| `core/components/blockab/lexicon/{nl,en}/default.inc.php` | 7 nieuwe lexicon-strings voor de preview-UI |
| `_build/build.transport.php` | Registreert plugin + plugin-event in category vehicle, bumpt versie |
| `core/components/blockab/docs/changelog.txt` | 1.1.3-pl entry |
| `core/components/blockab/docs/readme.txt` | Versie-bump |
| `assets/components/blockab/js/mgr/blockab.js` | Versie-string in About-window |

---

## Task 1: Server-side preview override

Implementeert de kern: een GET-param override in het BlockAB model die het variant-pick-mechanisme bypasst voor managers.

**Files:**
- Modify: `core/components/blockab/model/blockab/blockab.class.php`

- [ ] **Step 1: Voeg `getPreviewOverride()` method toe**

Open `core/components/blockab/model/blockab/blockab.class.php`. Plaats deze method direct ná de constructor (na `__construct()`, vóór `getUserData()`) — rond regel 81:

```php
/**
 * Get preview override variant from GET parameter (manager-only).
 *
 * Returns the variant_key from $_GET['ab_<test_group>'] when the current
 * user has 'view_unpublished' permission. Returns null otherwise.
 * Sudo users always pass MODX permission checks.
 *
 * @param string $testGroup
 * @return string|null
 */
public function getPreviewOverride($testGroup) {
    if (empty($testGroup)) {
        return null;
    }
    if (!$this->modx->hasPermission('view_unpublished')) {
        return null;
    }
    $key = 'ab_' . $testGroup;
    if (!isset($_GET[$key]) || $_GET[$key] === '') {
        return null;
    }
    return (string)$_GET[$key];
}
```

- [ ] **Step 2: Integreer override-check als eerste in `shouldShowBlock()`**

In `shouldShowBlock()` (rond regel 104), direct ná de `if (empty($testGroup)) { return true; }` check, voeg dit toe:

```php
        // Manager preview override — bypasses random pick + session
        $override = $this->getPreviewOverride($testGroup);
        if ($override !== null) {
            $this->lastPickDetails = array(
                'test'       => null,
                'mode'       => 'preview',
                'pick'       => null,
                'variation'  => null,
                'variations' => array(),
            );
            return ((string)$override === (string)$variantKey);
        }
```

De check zit hierboven `getActiveTest()` zodat geen DB-lookup en geen `pickOne()`-call (en dus geen `babPick`-write) plaatsvindt tijdens een preview.

- [ ] **Step 3: Verifieer met PHP syntax check**

```
docker exec moving-innl-movingin_php-1 php -l /var/www/_packages/blockab/core/components/blockab/model/blockab/blockab.class.php
```

Verwacht: `No syntax errors detected`.

- [ ] **Step 4: Commit**

```
cd /c/Projecten/moving-in.nl/_packages/blockab
git add core/components/blockab/model/blockab/blockab.class.php
git commit -m "blockab model: add manager preview override in shouldShowBlock

Adds getPreviewOverride() that reads \$_GET['ab_<test_group>'] when the
current user has view_unpublished permission. The override returns
deterministic show/hide without writing a babPick or session entry, so
manager previews don't pollute test stats."
```

---

## Task 2: Lexicon strings (NL + EN)

7 strings die door de preview-UI gebruikt worden. Toevoegen aan beide lexicon-files.

**Files:**
- Modify: `core/components/blockab/lexicon/nl/default.inc.php`
- Modify: `core/components/blockab/lexicon/en/default.inc.php`

- [ ] **Step 1: Append NL strings**

Open `core/components/blockab/lexicon/nl/default.inc.php`, voeg onderaan toe (vóór de eventuele closing `?>` als die er staat):

```php

// Preview varianten (manager)
$_lang['blockab.preview_button'] = 'Preview varianten';
$_lang['blockab.preview_modal_title'] = 'Variant-combinatie kiezen';
$_lang['blockab.preview_open'] = 'Preview openen';
$_lang['blockab.preview_cancel'] = 'Annuleren';
$_lang['blockab.preview_no_groups'] = 'Geen A/B-test groepen gevonden in deze resource';
$_lang['blockab.preview_dirty_warning'] = 'Sla de resource eerst op om nieuwe blokken in de preview te zien';
$_lang['blockab.preview_site_default'] = 'Site default (geen override)';
```

- [ ] **Step 2: Append EN strings**

Open `core/components/blockab/lexicon/en/default.inc.php`, voeg onderaan toe:

```php

// Preview variants (manager)
$_lang['blockab.preview_button'] = 'Preview variants';
$_lang['blockab.preview_modal_title'] = 'Choose variant combination';
$_lang['blockab.preview_open'] = 'Open preview';
$_lang['blockab.preview_cancel'] = 'Cancel';
$_lang['blockab.preview_no_groups'] = 'No A/B test groups found in this resource';
$_lang['blockab.preview_dirty_warning'] = 'Save the resource first to include new blocks in the preview';
$_lang['blockab.preview_site_default'] = 'Site default (no override)';
```

- [ ] **Step 3: Verifieer beide bestanden parsen schoon**

```
docker exec moving-innl-movingin_php-1 php -l /var/www/_packages/blockab/core/components/blockab/lexicon/nl/default.inc.php
docker exec moving-innl-movingin_php-1 php -l /var/www/_packages/blockab/core/components/blockab/lexicon/en/default.inc.php
```

Verwacht: `No syntax errors detected` voor beide.

- [ ] **Step 4: Commit**

```
git add core/components/blockab/lexicon/nl/default.inc.php core/components/blockab/lexicon/en/default.inc.php
git commit -m "lexicon: add preview-button strings (NL + EN)

7 nieuwe strings voor de variant-preview modal: button label,
modal title, open/cancel buttons, no-groups en dirty-warning
boodschappen, en site-default optie in de combobox."
```

---

## Task 3: Plugin source bestand

Maakt het PHP-bestand dat als snippet-style code in de DB komt te staan (geen leading `<?php` na strip in build script). Bevat het bestaande migx-cascade gedrag plus de extra `regClientStartupScript` voor `preview-button.js`.

**Files:**
- Create: `core/components/blockab/elements/plugins/blockabmigxcascade.plugin.php`

- [ ] **Step 1: Verifieer dat de plugin-directory bestaat**

```
ls /c/Projecten/moving-in.nl/_packages/blockab/core/components/blockab/elements/
```

Als er geen `plugins/` is, maken we 'm in de volgende stap automatisch via Write. (`elements/snippets/` bestaat al.)

- [ ] **Step 2: Maak het plugin-bronbestand aan**

Schrijf naar `core/components/blockab/elements/plugins/blockabmigxcascade.plugin.php`:

```php
<?php
/**
 * BlockAB MIGX Cascade Plugin
 *
 * Loads the variant cascade dropdown JS and the preview-button JS
 * on the resource edit page (event: OnDocFormPrerender).
 *
 * @package blockab
 */
switch ($modx->event->name) {
    case 'OnDocFormPrerender':
        $assetsUrl = $modx->getOption('blockab.assets_url', null,
            $modx->getOption('assets_url') . 'components/blockab/');
        $modx->regClientStartupScript($assetsUrl . 'js/mgr/migx-cascade.js');
        $modx->regClientStartupScript($assetsUrl . 'js/mgr/preview-button.js');
        break;
}
return '';
```

- [ ] **Step 3: Verifieer parse**

```
docker exec moving-innl-movingin_php-1 php -l /var/www/_packages/blockab/core/components/blockab/elements/plugins/blockabmigxcascade.plugin.php
```

Verwacht: `No syntax errors detected`.

- [ ] **Step 4: Commit**

```
git add core/components/blockab/elements/plugins/blockabmigxcascade.plugin.php
git commit -m "plugin: add BlockABMigxCascade source file

Source code for the BlockABMigxCascade plugin. Loads migx-cascade.js
(existing variant cascade dropdown) and preview-button.js (new manager
variant preview UI) on OnDocFormPrerender. Was previously only present
in staging DB; will be registered by the transport package in Task 6."
```

---

## Task 4: `getvariantsforpreview` processor

Connector-processor die voor een lijst test_group keys de actieve variants teruggeeft. Wordt door de modal gebruikt om de dropdowns te vullen.

**Files:**
- Create: `core/components/blockab/processors/mgr/test/getvariantsforpreview.class.php`

- [ ] **Step 1: Maak de processor aan**

Schrijf naar `core/components/blockab/processors/mgr/test/getvariantsforpreview.class.php`:

```php
<?php
/**
 * Get variants per test group for the manager preview button.
 *
 * Input:
 *   - groups: comma-separated list of test_group keys
 *
 * Output:
 *   {
 *     "success": true,
 *     "object": {
 *       "groups": {
 *         "homepage_hero": [
 *           {"key": "A", "name": "Control"},
 *           {"key": "B", "name": "Treatment"}
 *         ],
 *         "header": [...]
 *       }
 *     }
 *   }
 *
 * Only returns variants from active, non-archived tests.
 *
 * @package blockab
 * @subpackage processors
 */
class babTestGetVariantsForPreviewProcessor extends modProcessor {

    public function checkPermissions() {
        return $this->modx->hasPermission('view_unpublished');
    }

    public function getLanguageTopics() {
        return array('blockab:default');
    }

    public function process() {
        $groupsCsv = (string)$this->getProperty('groups', '');
        $groups = array_filter(array_map('trim', explode(',', $groupsCsv)));

        $result = array();
        foreach ($groups as $group) {
            $result[$group] = array();
            $test = $this->modx->getObject('babTest', array(
                'test_group' => $group,
                'active'     => 1,
                'archived'   => 0,
            ));
            if (!$test) {
                continue;
            }
            $variations = $this->modx->getCollection('babVariation', array(
                'test'   => $test->get('id'),
                'active' => 1,
            ));
            foreach ($variations as $v) {
                $result[$group][] = array(
                    'key'  => $v->get('variant_key'),
                    'name' => $v->get('name'),
                );
            }
        }

        return $this->success('', array('groups' => $result));
    }
}

return 'babTestGetVariantsForPreviewProcessor';
```

- [ ] **Step 2: Verifieer parse**

```
docker exec moving-innl-movingin_php-1 php -l /var/www/_packages/blockab/core/components/blockab/processors/mgr/test/getvariantsforpreview.class.php
```

Verwacht: `No syntax errors detected`.

- [ ] **Step 3: Commit**

```
git add core/components/blockab/processors/mgr/test/getvariantsforpreview.class.php
git commit -m "processor: add getvariantsforpreview for manager UI

Returns variants per test_group for the preview-button modal. Input
is a CSV of test_group keys, output is keyed by group with [{key,name}]
arrays of active variants. Restricted to view_unpublished permission."
```

---

## Task 5: `preview-button.js` UI

JavaScript dat de preview-knop boven de eerste MIGX TV op de resource-edit pagina injecteert, op click de modal opent met dropdowns per testgroup, en een preview-URL bouwt om in een nieuw tabblad te openen.

**Files:**
- Create: `assets/components/blockab/js/mgr/preview-button.js`

- [ ] **Step 1: Maak `preview-button.js` aan**

Schrijf naar `assets/components/blockab/js/mgr/preview-button.js`:

```javascript
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
        // Walk up to the form-item container that wraps the TV
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
```

- [ ] **Step 2: Quick syntax sanity**

Open het bestand in de IDE — geen rode squiggles in de eerste 5 regels. Of via node als geïnstalleerd:

```
node --check /c/Projecten/moving-in.nl/_packages/blockab/assets/components/blockab/js/mgr/preview-button.js
```

(Als `node` niet beschikbaar is, sla deze stap over — de browser-test in Task 8 vangt het.)

- [ ] **Step 3: Commit**

```
git add assets/components/blockab/js/mgr/preview-button.js
git commit -m "ui: preview-button.js — manager variant combo picker

Reads MIGX state (live, on click) from textarea[id^=tv...] elements,
collects unique ab_test_group values, fetches variants via the
getvariantsforpreview processor, opens an Ext.Window with one combo
per group plus a dirty-state warning when applicable, and builds an
\\?ab_<group>=<variant> preview URL on submit."
```

---

## Task 6: Build script — register plugin + bump versie

Voegt `BlockABMigxCascade` toe als `modPlugin` met `modPluginEvent` voor `OnDocFormPrerender`, registreert die als child-objects in de category vehicle, en bumpt versie naar `1.1.3`.

**Files:**
- Modify: `_build/build.transport.php`

- [ ] **Step 1: Bump versie**

In `_build/build.transport.php`, regel 18:

```php
define('PKG_VERSION', '1.1.3');
```

(was `'1.1.2'`)

- [ ] **Step 2: Voeg plugin-registratie toe**

In `_build/build.transport.php`, direct ná het snippets-blok (na regel 122, vlak vóór `/* Create category vehicle */`), voeg toe:

```php
/* Add plugin */
$plugins = array();

$plugins[0] = $modx->newObject('modPlugin');
$plugins[0]->fromArray(array(
    'id'          => 1,
    'name'        => 'BlockABMigxCascade',
    'description' => 'Loads MIGX cascade dropdown and preview-button JS on resource edit pages',
    'plugincode'  => blockab_load_snippet($sources['source_core'] . '/elements/plugins/blockabmigxcascade.plugin.php'),
), '', true, true);

$pluginEvent = $modx->newObject('modPluginEvent');
$pluginEvent->fromArray(array(
    'pluginid'    => 1,
    'event'       => 'OnDocFormPrerender',
    'priority'    => 0,
    'propertyset' => 0,
), '', true, true);
$plugins[0]->addMany(array($pluginEvent));

if (count($plugins) > 0) {
    $category->addMany($plugins);
    $modx->log(modX::LOG_LEVEL_INFO, 'Added ' . count($plugins) . ' plugins.');
}
```

- [ ] **Step 3: Update category vehicle config met `Plugins` related-object spec**

Vervang het hele `$attr = array(...)` blok (rond regel 125-137) door:

```php
/* Create category vehicle */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
        'Plugins' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
            xPDOTransport::RELATED_OBJECTS => true,
            xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
                'PluginEvents' => array(
                    xPDOTransport::PRESERVE_KEYS => true,
                    xPDOTransport::UPDATE_OBJECT => false,
                    xPDOTransport::UNIQUE_KEY => array('pluginid', 'event'),
                ),
            ),
        ),
    ),
);
```

- [ ] **Step 4: Verifieer parse**

```
docker exec moving-innl-movingin_php-1 php -l /var/www/_packages/blockab/_build/build.transport.php
```

Verwacht: `No syntax errors detected`.

- [ ] **Step 5: Commit**

```
git add _build/build.transport.php
git commit -m "build: register BlockABMigxCascade plugin in transport package

Adds modPlugin + modPluginEvent (OnDocFormPrerender) to the category
vehicle so the plugin is automatically created on install/upgrade.
Plugin was previously only created manually in staging — prod installs
of 1.1.0/1.1.1/1.1.2 did not have it. Bumps PKG_VERSION to 1.1.3."
```

---

## Task 7: Versie-referenties bijwerken

Drie kleine bestanden aanpassen voor consistentie.

**Files:**
- Modify: `assets/components/blockab/js/mgr/blockab.js`
- Modify: `core/components/blockab/docs/readme.txt`
- Modify: `core/components/blockab/docs/changelog.txt`

- [ ] **Step 1: Update About-window versie-string**

In `assets/components/blockab/js/mgr/blockab.js`, regel 34:

```javascript
+     '<span class="blockab-about-version">v1.1.3</span>'
```

(was `v1.1.2`)

- [ ] **Step 2: Update readme.txt**

In `core/components/blockab/docs/readme.txt`, regel 4:

```
Version: 1.1.3
```

(was `1.1.2`)

- [ ] **Step 3: Voeg changelog entry toe**

In `core/components/blockab/docs/changelog.txt`, voeg toe direct ná `Changelog for BlockAB`:

```
BlockAB 1.1.3
====================================
- Manager variant preview: knop op resource-edit pagina (boven MIGX-grid) opent modal met dropdown per ab_test_group, "Preview openen" opent de resource met ?ab_<group>=<variant> overrides in nieuw tabblad
- BlockAB::shouldShowBlock() respecteert GET-overrides voor users met view_unpublished permission, zonder babPick of session-write
- Nieuwe processor mgr/test/getvariantsforpreview voor de modal
- Fix: BlockABMigxCascade plugin wordt voortaan door het transport-pakket geinstalleerd (was in 1.1.0/1.1.1/1.1.2 niet meegenomen)
- Plugin laadt zowel de bestaande migx-cascade.js als de nieuwe preview-button.js op OnDocFormPrerender
- 7 nieuwe lexicon-strings (NL + EN)

```

- [ ] **Step 4: Commit**

```
git add assets/components/blockab/js/mgr/blockab.js core/components/blockab/docs/readme.txt core/components/blockab/docs/changelog.txt
git commit -m "docs: bump version to 1.1.3 + changelog entry"
```

---

## Task 8: Build, install op local, manual smoke test

Bouwt het pakket, installeert 't via de lokale Docker MODX, en loopt door de manueel-test scenarios uit de spec heen.

**Files:** geen wijzigingen — alleen build + verify.

- [ ] **Step 1: Build het transport-pakket**

```
cd /c/Projecten/moving-in.nl
MSYS_NO_PATHCONV=1 docker exec moving-innl-movingin_php-1 php /var/www/_packages/blockab/_build/build.transport.php /var/www
```

Verwacht: `Package Built.` aan het einde, en `core/packages/blockab-1.1.3-pl.transport.zip` bestaat.

- [ ] **Step 2: Verifieer plugin-registratie in vehicle**

```
unzip -p core/packages/blockab-1.1.3-pl.transport.zip 'blockab-1.1.3-pl/modCategory/*.vehicle' | grep -o 'BlockABMigxCascade'
```

Verwacht: één regel `BlockABMigxCascade` in de output (bewijst dat plugin in vehicle zit).

- [ ] **Step 3: Verifieer de plugin-source in vehicle bevat geen leading `<?php`**

```
unzip -p core/packages/blockab-1.1.3-pl.transport.zip 'blockab-1.1.3-pl/modCategory/*.vehicle' | grep -oE '"plugincode":"[^"]{0,40}'
```

Verwacht: iets als `"plugincode":"\\/**\\n * BlockAB MIGX Casca` — zonder `<?php` aan het begin.

- [ ] **Step 4: Installeer via de lokale manager**

Open `http://staging.moving-in.nl.local/manager/?a=workspaces` (of via menu Extras → Installer). Klik **Add new package → Upload package**. Selecteer `blockab-1.1.3-pl.transport.zip`. Klik **Install**. Verwacht: install-log toont "Package installed". Daarna: leeg de cache map handmatig (`rm -rf core/cache/*` op de server, of via Docker: `docker exec moving-innl-movingin_php-1 rm -rf /var/www/core/cache/*`).

- [ ] **Step 5: Verifieer plugin in DB**

```
MSYS_NO_PATHCONV=1 docker exec moving-innl-movingin_php-1 php -r '
$pdo = new PDO("mysql:host=movingin_mysql;dbname=staging;charset=utf8", "root", "Movingin0546!");
$st = $pdo->query("SELECT p.id, p.name, p.disabled, e.event FROM mdx_site_plugins p LEFT JOIN mdx_site_plugin_events e ON e.pluginid=p.id WHERE p.name=\"BlockABMigxCascade\"");
while($r = $st->fetch(PDO::FETCH_ASSOC)) print_r($r);
'
```

Verwacht: één rij met `name=BlockABMigxCascade`, `disabled=0`, `event=OnDocFormPrerender`.

- [ ] **Step 6: Smoke test — preview-knop verschijnt op resource met A/B-blokken**

Open in browser: `http://staging.moving-in.nl.local/manager/?a=resource/update&id=73` (resource met `homepage_hero` test). Verwacht: blauwe "Preview varianten" knop boven het `migx_holder` veld.

- [ ] **Step 7: Smoke test — modal toont actuele variants**

Klik de knop. Verwacht: modal opent met titel "Variant-combinatie kiezen", dropdown(s) voor de aanwezige ab_test_groups, opties `Site default (geen override)` + de actuele variants uit de `babTest`/`babVariation` tabellen voor die groep.

- [ ] **Step 8: Smoke test — preview-URL werkt**

Kies in de dropdown variant `B`, klik "Preview openen". Verwacht: nieuw tabblad opent op `<site_url>index.php?id=73&ab_homepage_hero=B`. Op de pagina zie je de B-variant.

- [ ] **Step 9: Smoke test — geen babPick gemaakt**

```
MSYS_NO_PATHCONV=1 docker exec moving-innl-movingin_php-1 php -r '
$pdo = new PDO("mysql:host=movingin_mysql;dbname=staging;charset=utf8", "root", "Movingin0546!");
$today = date("Ymd");
echo "picks vandaag voor homepage_hero variant B: ";
echo $pdo->query("SELECT COUNT(*) FROM mdx_blockab_pick p JOIN mdx_blockab_variation v ON v.id=p.variation JOIN mdx_blockab_test t ON t.id=p.test WHERE t.test_group=\"homepage_hero\" AND v.variant_key=\"B\" AND p.date=\"$today\"")->fetchColumn();
echo \"\n\";
'
```

Note de count vóór de preview, doe stap 8, run dezelfde query opnieuw. Verwacht: count is **niet** verhoogd (preview registreert geen pick).

- [ ] **Step 10: Smoke test — non-manager respect**

Open de preview-URL in incognito of na uitloggen. Verwacht: de override wordt genegeerd, normale random pick gebruikt (afhankelijk van session: kan A of B zijn).

- [ ] **Step 11: Smoke test — geen groups in resource**

Open een resource zonder A/B-blokken. Klik knop. Verwacht: alert "Geen A/B-test groepen gevonden in deze resource".

- [ ] **Step 12: Smoke test — dirty warning**

Edit een resource met A/B-blokken, voeg een blok toe of wijzig zonder save. Klik preview-knop. Verwacht: in de modal staat de waarschuwing "Sla de resource eerst op om nieuwe blokken in de preview te zien" boven de dropdowns.

- [ ] **Step 13: Smoke test — cascade werkt nog steeds**

Edit een MIGX-blok, kies een test_group in de cascade-dropdown. Verwacht: variant-dropdown filtert automatisch op nog beschikbare varianten (bestaand 1.1.x gedrag, regression check).

- [ ] **Step 14: Stats dashboard ongewijzigd**

Open BlockAB CMP → Statistieken-tab. Verwacht: aantallen niet veranderd door de preview-clicks.

Als alle 14 checks passen, ga door naar Task 9. Als één faalt: fix de oorzaak, opnieuw builden (Task 8 stap 1), opnieuw installeren (stap 4), opnieuw testen vanaf de stap die faalde.

---

## Task 9: Push + samenvatting

- [ ] **Step 1: Push alle commits naar GitHub**

```
cd /c/Projecten/moving-in.nl/_packages/blockab
git push origin main
```

Verwacht: alle commits van Tasks 1-7 worden gepusht naar `github.com/dimitrihilverda/blockab`, branch `main`.

- [ ] **Step 2: Maak optioneel een release-tag**

```
git tag -a v1.1.3 -m "v1.1.3 — manager variant preview + plugin installer fix"
git push origin v1.1.3
```

- [ ] **Step 3: Bevestig zip-locatie**

```
ls -la /c/Projecten/moving-in.nl/core/packages/blockab-1.1.3-pl.transport.zip
```

Verwacht: bestand bestaat. Pad: `C:\Projecten\moving-in.nl\core\packages\blockab-1.1.3-pl.transport.zip`. Klaar voor upload naar prod via Package Manager → upgrade van 1.1.2.

---

## Self-Review Checklist (na implementatie)

Loop deze door voordat je het werk als klaar markeert:

- [ ] Spec sectie "Doelen" → alle 4 doelen gerealiseerd?
  1. Manager kan combo kiezen + openen → Task 5 + 8 (stap 6-8)
  2. Geen `babPick` of session-write tijdens preview → Task 1 + 8 (stap 9)
  3. `BlockABMigxCascade` plugin auto-installed → Task 6 + 8 (stap 5)
  4. Geen wijziging voor niet-ingelogde bezoekers → Task 1 + 8 (stap 10)
- [ ] Alle edge cases uit spec sectie "Edge cases" handled (Tasks 1, 4, 5, 8 stap 11-12)?
- [ ] Lexicon entries gebruikt door JS bestaan? (`grep '_(' assets/components/blockab/js/mgr/preview-button.js` vs `grep blockab.preview lexicon/nl/default.inc.php`)
- [ ] Geen orphaned snippet-bodies met leading `<?php` in vehicle (Task 8 stap 3)?
