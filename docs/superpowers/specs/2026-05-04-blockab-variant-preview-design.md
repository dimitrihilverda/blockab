# BlockAB — Variant Preview voor Managers (1.1.3)

**Datum:** 2026-05-04
**Status:** Goedgekeurd, klaar voor implementatieplan
**Versie:** 1.1.3-pl

## Overzicht

Manager-users moeten elke specifieke variant van een lopende A/B-test op een resource kunnen bekijken, zonder hun sessie te legen of de stats te vervuilen. Plus: de bestaande `BlockABMigxCascade` plugin (die de variant-cascade dropdown in MIGX al regelt) wordt voortaan correct door het transport-pakket geïnstalleerd — die was per ongeluk niet meegenomen in eerdere releases.

## Doelen

1. Manager kan op een resource-edit pagina een combinatie van (testgroup, variant) kiezen en de resource met die combinatie in een nieuw tabblad openen.
2. Preview registreert geen `babPick` en schrijft niet naar `$_SESSION['_blockab']['_picked']`.
3. `BlockABMigxCascade` plugin wordt automatisch geïnstalleerd als onderdeel van het transport-pakket.
4. Geen wijziging in gedrag voor niet-ingelogde bezoekers.

## Non-doelen

- Cookie- of session-gebaseerde "blijvende" preview (nu via GET-param per pageload).
- Preview-tokens voor externe stakeholders (alleen ingelogde managers).
- Tonen van alle varianten naast elkaar op één pagina.
- Wijzigingen aan `babPick` / `babConversion` schemas of het stats-dashboard.

## Architectuur

Twee onafhankelijke componenten plus een installer-fix:

**Server-side override.** `BlockAB::shouldShowBlock()` krijgt een nieuwe eerste check: als `$_GET['ab_<test_group>']` aanwezig is **én** de huidige user heeft `view_unpublished` permission, dan wordt het block getoond als `variant_key` matcht met de override-waarde. Geen pick-write, geen session-write, geen DB-write. De rest van `shouldShowBlock` (random pick, smartoptimize, fail-safes) blijft ongewijzigd.

**Manager UI.** De `BlockABMigxCascade` plugin laadt — naast de bestaande `migx-cascade.js` — ook een nieuwe `preview-button.js`. Die voegt boven de MIGX-grid op de resource-edit pagina een "Preview varianten" knop toe. Click → leest live de actuele `migx_holder` textarea → AJAX naar nieuwe processor `mgr/test/getvariantsforpreview` → modal met dropdowns per testgroup → "Preview openen" bouwt frontend-URL en opent in nieuw tabblad.

**Installer fix.** `_build/build.transport.php` registreert voortaan ook de `BlockABMigxCascade` plugin (via een nieuw `modPlugin` object plus `modPluginEvent` voor `OnDocFormPrerender`), in dezelfde category vehicle als snippets.

## Bestanden

| Bestand | Actie |
|---|---|
| `core/components/blockab/model/blockab/blockab.class.php` | `getPreviewOverride($testGroup)` toevoegen; integreren als eerste check in `shouldShowBlock()` |
| `core/components/blockab/elements/plugins/blockabmigxcascade.plugin.php` | nieuw — broncode van bestaande staging-plugin, uitgebreid met `regClientStartupScript` voor `preview-button.js` |
| `assets/components/blockab/js/mgr/preview-button.js` | nieuw — knop, modal, URL-builder |
| `core/components/blockab/processors/mgr/test/getvariantsforpreview.class.php` | nieuw — input: `groups` (csv string van test_group keys), output: `{group_key: [{variant_key, name}, ...], ...}` voor actieve, niet-archived tests |
| `_build/build.transport.php` | plugin + plugin-event registreren in category vehicle |
| `core/components/blockab/lexicon/nl/default.inc.php` | nieuwe strings (zie sectie Lexicon) |
| `core/components/blockab/lexicon/en/default.inc.php` | dezelfde strings, EN-vertaling |
| `core/components/blockab/docs/changelog.txt` | 1.1.3-pl entry |
| `core/components/blockab/docs/readme.txt` | versie naar 1.1.3 |
| `assets/components/blockab/js/mgr/blockab.js` | versie-string in About-window naar v1.1.3 |

## Data flow

1. Manager opent resource-edit pagina met een MIGX TV (bv. `migx_holder`)
2. MODX vuurt `OnDocFormPrerender` → `BlockABMigxCascade` plugin laadt twee scripts:
   - `migx-cascade.js` (bestaand, regelt cascade-dropdown bij MIGX-veldselectie)
   - `preview-button.js` (nieuw)
3. `preview-button.js` wacht op DOM-ready, detecteert MIGX TV, voegt knop "Preview varianten" toe boven de grid. **Geen** state-read op dit moment.
4. Manager klikt op knop:
   - Script leest live de `migx_holder` hidden textarea (JSON), verzamelt unieke niet-lege `ab_test_group` waarden
   - Als `$migx_holder` niet-bewaarde wijzigingen bevat (manager-form is dirty) → kleine notitie in modal: "Sla de resource eerst op om nieuwe blokken in de preview te zien"
   - AJAX POST naar `assets/components/blockab/connector.php` met `action=mgr/test/getvariantsforpreview` en `groups=<csv>`
   - Response: `{group_key: [{key: 'A', name: 'Control'}, {key: 'B', name: 'Treatment'}, ...], ...}`
5. Modal opent als `Ext.Window` (consistent met de bestaande About/Manual windows in `blockab.js`) met één `<select>` per groep. Opties: "Site default (geen override)" + alle variants van die test.
6. Manager kiest combo, klikt "Preview openen":
   - Script bouwt URL: `<resource_uri>?ab_<group1>=<key1>&ab_<group2>=<key2>` (alleen voor groepen waar een variant gekozen is, "site default" wordt weggelaten)
   - `window.open(url, '_blank')`
7. Frontend pageload van die URL:
   - Per A/B-blok roept template `BlockAB` snippet aan
   - `shouldShowBlock($testGroup, $variantKey, $resourceId)` → `getPreviewOverride($testGroup)`:
     - Permission-check: `$this->modx->hasPermission('view_unpublished')` — false → return null (geen override)
     - GET-check: `$_GET['ab_' . $testGroup]` — leeg → return null
     - Match: return de override-waarde
   - Met override: return `(strval($override) === strval($variantKey))` — geen verdere pick of session-write
   - Zonder override: bestaande logica

## Edge cases

- **Geen testgroups in resource**: knop blijft zichtbaar (we weten 't pas bij click). Click → modal toont "Geen A/B-test groepen gevonden in deze resource". Sluit-knop.
- **Manager niet ingelogd of mist `view_unpublished`**: GET-parameters worden volledig genegeerd, normale random pick + session-write loopt zoals altijd. (Bezoeker met geknutselde URL kan dus niets forceren.)
- **Override-variant bestaat niet in test**: `shouldShowBlock` returnt `false` voor alle blocks van die groep. Documentatie-detail; in praktijk niet bereikbaar via de UI omdat dropdown alleen bestaande variants toont.
- **Test inactief / archived**: in `shouldShowBlock` valt 't door op `getActiveTest() === null` → return `true` (huidige fail-safe blijft).
- **Dirty resource (niet-bewaarde wijzigingen)**: knop werkt op live `migx_holder` (live JSON in de textarea), maar frontend rendert nog de DB-staat. Modal toont waarschuwing.
- **Meerdere identieke `ab_test_group` waarden in MIGX**: dedup in JS, één entry per groep in modal.
- **MIGX TV heeft een andere naam dan `migx_holder`**: detecteer alle TV-textareas via `textarea[id^="tv"]` op de pagina, JSON.parse de waarde, herken MIGX-formaat aan `Array.isArray(...) && items[0].MIGX_id` aanwezigheid. Werkt voor elke MIGX TV-id. Bij meerdere MIGX-TVs op één resource: alle ab_test_groups uit alle TV's samenvoegen en dedup'pen.
- **Backwards compat met 1.1.2**: snippets, processors, schemas onveranderd → `UPDATE_OBJECT=true` in transport zorgt voor een schone upgrade.

## Lexicon strings

Nieuw in `default.inc.php`:

| Key | NL | EN |
|---|---|---|
| `blockab.preview_button` | Preview varianten | Preview variants |
| `blockab.preview_modal_title` | Variant-combinatie kiezen | Choose variant combination |
| `blockab.preview_open` | Preview openen | Open preview |
| `blockab.preview_cancel` | Annuleren | Cancel |
| `blockab.preview_no_groups` | Geen A/B-test groepen gevonden in deze resource | No A/B test groups found in this resource |
| `blockab.preview_dirty_warning` | Sla de resource eerst op om nieuwe blokken in de preview te zien | Save the resource first to include new blocks in the preview |
| `blockab.preview_site_default` | Site default (geen override) | Site default (no override) |

## Permission gate

Hergebruik bestaande `view_unpublished` MODX 2.x permission. Sudo-users passen `hasPermission()` altijd ongeacht welke permission (MODX-default), dus die zitten automatisch goed. Editors/admins met `view_unpublished` óók. Niet-ingelogde bezoekers nooit. Dezelfde gate als bij debug-mode in `blockab.snippet.php`.

## Testing (manueel)

1. **Install over 1.1.2**: upload zip, klik upgrade. Verifieer in DB:
   - `mdx_site_plugins` heeft een rij `BlockABMigxCascade`
   - `mdx_site_plugin_events` heeft een rij voor die plugin met `event=OnDocFormPrerender`
2. **Preview-knop verschijnt**: open een resource met A/B-blokken. Knop staat boven MIGX-grid.
3. **Modal gevuld**: klik knop. Modal toont één dropdown per ab_test_group, met de juiste actuele variants (niet de statische A||B||C||D fallback).
4. **Preview-URL werkt**: kies variant B voor `homepage_hero`, klik "Preview openen". Nieuwe tab toont alleen de B-variant van die groep.
5. **Geen pick/conversion in DB**: na preview, query `mdx_blockab_pick` — geen nieuwe rij voor die test/variant in vandaag's date.
6. **Permission-gate**: log uit, open dezelfde URL. Override genegeerd, normale random pick gebruikt (verifieer via session of door site default te zien).
7. **Dirty state**: voeg ongesaved een blok toe met groep `homepage_hero`, klik knop. Modal toont waarschuwing.
8. **Geen groups**: open resource zonder A/B-blokken, klik knop. Modal toont "geen groups gevonden".
9. **Stats dashboard**: open BlockAB CMP. Aantallen ongewijzigd na preview-sessies.
10. **Cascade-dropdown blijft werken**: open resource, edit een MIGX-blok, kies een test_group → variant-dropdown filtert automatisch (bestaand gedrag, regression check).

## Versie en deploy

- `1.1.3-pl`
- Bevat: GET-override, plugin-installer-fix, preview-button UI, getvariantsforpreview processor, lexicon-strings.
- Build via `docker exec moving-innl-movingin_php-1 php /var/www/_packages/blockab/_build/build.transport.php /var/www`
- Deploy: upload zip via Package Manager → upgrade → cache map handmatig legen.

## Risico's

- **Plugin-event registratie via transport package**: niet-triviaal, vereist het toevoegen van een `modPluginEvent` als child object van `modPlugin` in de category vehicle. Bij implementatie even valideren tegen werkende voorbeelden uit andere MODX packages (bv. Collections, FormIt).
- **MIGX TV-naam variatie**: huidige spec leest uit alle MIGX-textareas op de pagina. Als een resource meerdere MIGX-TVs heeft moet dat correct gededupliceerd worden.
- **Permission-gate semantiek**: `view_unpublished` is breed en omvat meer dan strikt manager-only. Acceptabel: dezelfde gate als de bestaande debug-mode in `blockab.snippet.php`.
