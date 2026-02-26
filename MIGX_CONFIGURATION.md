# MIGX Configuration for BlockAB

This document explains how to configure your MIGX fields to use BlockAB test groups and variants with dropdown selectors.

## Required MIGX Fields

Add two fields to your MIGX configuration:

### 1. Test Group Field (`ab_test_group`)

```json
{
    "caption": "A/B Test Group",
    "description": "Select which A/B test this block belongs to",
    "inputTV": "",
    "inputTVtype": "listbox",
    "configs": "",
    "restrictive_condition": "",
    "restrictive_action": "hide",
    "default": "",
    "sourceFrom": "config",
    "sources": "",
    "inputOptionValues": "@EVAL return $modx->runSnippet('BlockABGetTestGroups');",
    "columnsValueFalse": "",
    "validationtype": "",
    "validationrule": ""
}
```

### 2. Variant Field (`ab_test_variant`)

```json
{
    "caption": "A/B Test Variant",
    "description": "Select which variant of the test this block represents",
    "inputTV": "",
    "inputTVtype": "listbox",
    "configs": "",
    "restrictive_condition": "",
    "restrictive_action": "hide",
    "default": "",
    "sourceFrom": "config",
    "sources": "",
    "inputOptionValues": "@EVAL return $modx->runSnippet('BlockABGetVariants', array('testGroup' => $scriptProperties['ab_test_group']));",
    "columnsValueFalse": "",
    "validationtype": "",
    "validationrule": ""
}
```

## How It Works

1. **Test Group Dropdown**: Shows all active, non-archived tests from BlockAB
   - Format: "Test Name (test_group_key)"
   - Only active tests are shown
   - Empty option: "-- Select Test Group --"

2. **Variant Dropdown**: Shows variants for the selected test group
   - Format: "A - Variant Name" (shows key with title)
   - Dynamically loads based on `ab_test_group` value
   - Falls back to "A||B||C||D||E" if no test group selected

## Important Notes

### Dynamic Variant Loading

The variant dropdown updates dynamically when you select a test group — no save and reload required. BlockAB uses MIGX Cascade logic to fetch the available variants via AJAX as soon as the test group changes.

If no test group is selected yet, the dropdown falls back to the default options (A–E).

### Alternative: Static Variant Keys

If you prefer simpler configuration without dependencies:

```json
{
    "caption": "A/B Test Variant",
    "inputTVtype": "listbox",
    "inputOptionValues": "A||B||C||D||E||F||G||H||I||J"
}
```

This approach:
- ✅ Works immediately without dependencies
- ✅ No need to reload after selecting test group
- ❌ Doesn't show variant names from BlockAB
- ❌ Shows all keys even if not defined in test

## Complete MIGX JSON Example

```json
{
    "formtabs": [
        {
            "caption": "Content",
            "fields": [
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
                    "inputOptionValues": "@EVAL return $modx->runSnippet('BlockABGetVariants', array('testGroup' => $scriptProperties['ab_test_group']));"
                }
            ]
        }
    ]
}
```

## Testing the Configuration

1. Create a test in BlockAB manager:
   - Name: "Homepage Hero Test"
   - Test Group: `homepage_hero`
   - Add variations: A (Control), B (New Design)

2. Edit a page with MIGX field:
   - Select "Homepage Hero Test (homepage_hero)" from test group dropdown
   - Save the MIGX item
   - Reopen the MIGX item
   - Variant dropdown should show: "A - Control" and "B - New Design"

3. Create duplicate blocks:
   - Add another MIGX item
   - Select same test group
   - Select different variant
   - Both blocks will now A/B test against each other

## Snippets Reference

Both snippets support additional parameters:

### BlockABGetTestGroups

```php
[[BlockABGetTestGroups?
    &format=`migx`              // Output format: "migx" or "json"
    &includeInactive=`0`        // Include inactive tests
    &includeArchived=`0`        // Include archived tests
]]
```

### BlockABGetVariants

```php
[[BlockABGetVariants?
    &testGroup=`homepage_hero`  // Test group to get variants for
    &format=`detailed`          // "detailed" (A - Name) or "simple" (A)
    &includeInactive=`0`        // Include inactive variations
]]
```

## Troubleshooting

**Q: Variant dropdown is empty**
- Make sure test group is selected and saved
- Verify the test exists in BlockAB manager
- Check that variations are active
- Try reopening the MIGX item

**Q: Variant dropdown shows A||B||C instead of names**
- This means test group isn't set yet or test doesn't exist
- Save the item with test group selected, then reopen

**Q: Want real-time dependent dropdowns**
- Not possible with standard MIGX
- Use static variant keys as alternative
- Or accept the save/reload workflow
