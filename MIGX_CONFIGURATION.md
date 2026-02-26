# MIGX Configuration for BlockAB

This document explains how to add the two BlockAB fields to your MIGX configuration.

---

## How it works

BlockAB uses two fields on each MIGX block:

- **`ab_test_group`** — a listbox that shows all active tests from BlockAB
- **`ab_test_variant`** — a listbox that is populated automatically via JavaScript (AJAX) the moment you select a test group

The variant dropdown updates in real-time when you change the test group — no save and reload required. BlockAB includes a cascade script (`migx-cascade.js`) that fires an AJAX call to the connector and fills the variant combo on the fly. It also reads which variants are already in use by other blocks of the same type and automatically selects the first free variant.

---

## Required MIGX fields

Add both fields to the relevant tab in your MIGX configuration.

### 1. Test Group Field (`ab_test_group`)

| Property | Value |
|---|---|
| Field | `ab_test_group` |
| Caption | `A/B Test Group` |
| Input TV Type | `listbox` |
| Input Option Values | `@EVAL return $modx->runSnippet('BlockABGetTestGroups');` |

### 2. Variant Field (`ab_test_variant`)

| Property | Value |
|---|---|
| Field | `ab_test_variant` |
| Caption | `A/B Test Variant` |
| Input TV Type | `listbox` |
| Input Option Values | *(leave empty — filled automatically by cascade script)* |

---

## Complete MIGX JSON example

Below is the minimal JSON for the two A/B fields. Add them to the appropriate tab inside your existing `formtabs` configuration.

```json
{
    "field": "ab_test_group",
    "caption": "A/B Test Group",
    "inputTVtype": "listbox",
    "inputOptionValues": "@EVAL return $modx->runSnippet('BlockABGetTestGroups');"
},
{
    "field": "ab_test_variant",
    "caption": "A/B Test Variant",
    "inputTVtype": "listbox",
    "inputOptionValues": ""
}
```

The variant field has no static options — the cascade script populates it automatically when a test group is selected.

---

## How the cascade script works

`migx-cascade.js` is loaded by BlockAB and runs in the MODX manager. It:

1. Detects when a MIGX item form opens (via `MODx.FormPanel` hook + `MutationObserver` fallback)
2. Finds the `ab_test_group` and `ab_test_variant` fields
3. Listens for changes on `ab_test_group`
4. Fires an AJAX call to `web/variation/getbytestgroup` when the test group changes
5. Fills the variant combo with the returned variants
6. Reads all other blocks in the same MIGX TV and **excludes variants already in use** (same block type only)
7. Automatically selects the first free variant

This means: when you add a second block to the same test group, the variant that was already taken by the first block will not appear in the dropdown — you automatically land on the next free one.

---

## Testing the configuration

1. Create a test in BlockAB: **Components → BlockAB → Create Test**
   - Name: `Homepage Hero Test`
   - Test Group: `homepage_hero`
   - Add variants: A (Control), B (New Design)
   - Set Active: on

2. Edit a page with your MIGX TV and add a new block:
   - Select `Homepage Hero Test (homepage_hero)` from the test group dropdown
   - The variant dropdown fills immediately — no save required
   - Variant A is selected automatically

3. Add a second block with the same test group:
   - The cascade detects that A is already taken
   - Variant B is selected automatically

---

## Snippets reference

### BlockABGetTestGroups

Returns all active, non-archived tests formatted for use in a MIGX listbox.

```
[[BlockABGetTestGroups]]
```

Optional parameters:

| Parameter | Default | Description |
|---|---|---|
| `format` | `migx` | Output format: `migx` (key==Label) or `json` |
| `includeInactive` | `0` | Include inactive tests |
| `includeArchived` | `0` | Include archived tests |

### BlockABGetVariants

Returns variants for a given test group. Used internally by the cascade script — you do not normally call this directly in MIGX.

```
[[BlockABGetVariants? &testGroup=`homepage_hero`]]
```

Optional parameters:

| Parameter | Default | Description |
|---|---|---|
| `testGroup` | *(required)* | Test group key |
| `format` | `detailed` | `detailed` (A — Name) or `simple` (A) |
| `includeInactive` | `0` | Include inactive variants |

---

## Troubleshooting

**Variant dropdown stays empty after selecting a test group**
- Check that the test exists in BlockAB and is set to Active
- Make sure the test has at least one active variant
- Open the browser console and look for `[BlockAB]` warnings
- Verify that `migx-cascade.js` is loading (check Network tab)

**Variant dropdown shows old/wrong options**
- The cascade fires on every `select` event — if the dropdown seems stale, click the test group again to force a reload

**All variants are greyed out / none available**
- All variants for this test group are already taken by other blocks of the same type on this page
- Add more variants in BlockAB, or check whether duplicate blocks exist
