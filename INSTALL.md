# BlockAB — Installation Guide

## What is BlockAB?

BlockAB is an A/B testing module for MODX CMS. It lets you assign MIGX content blocks to test groups, shows one variant per visitor (consistent across the session), and tracks conversions. A built-in manager page gives you a statistical dashboard with chi-square significance testing.

---

## Requirements

- MODX Revolution 2.3+
- PHP 7.2+
- MySQL 5.7+ / MariaDB 10.2+

---

## Installation

### 1. Copy Files

**Option A — CLI script:**
```bash
cd _build
php build.simple.php /path/to/your/modx
```

The script auto-detects whether assets live in `public_html/assets/` or directly in `assets/`.

**Option B — Manual copy:**

Copy these two directories to your MODX installation:

| Source (in this package) | Destination |
|---|---|
| `core/components/blockab/` | `{modx_root}/core/components/blockab/` |
| `assets/components/blockab/` | `{modx_root}/assets/components/blockab/` |

---

### 2. Create Database Tables

Open `_build/install.sql` in your database client (phpMyAdmin, TablePlus, etc.) and run it.

> **Important:** The SQL uses the prefix `mdx_`. Replace it with your actual MODX table prefix if different (e.g., `modx_`).

**Alternative — via CLI:**
```bash
cd _build
php install.tables.php /path/to/your/modx
```

---

### 3. Create Snippets

Create these snippets in **Elements > Snippets** in the MODX Manager.

#### Required

**BlockAB** — determines which variant to show for a block
- Name: `BlockAB`
- Code: copy from `core/components/blockab/elements/snippets/blockab.snippet.php`

**BlockABConversion** — registers a conversion on your thank-you / success page
- Name: `BlockABConversion`
- Code: copy from `core/components/blockab/elements/snippets/blockab.conversion.snippet.php`

#### Optional (for dynamic MIGX dropdowns)

**BlockABGetTestGroups** — populates a MIGX listbox with available test groups
- Name: `BlockABGetTestGroups`
- Code: copy from `core/components/blockab/elements/snippets/blockab.gettestgroups.snippet.php`

**BlockABGetVariants** — populates a MIGX listbox with variant keys for a given test group
- Name: `BlockABGetVariants`
- Code: copy from `core/components/blockab/elements/snippets/blockab.getvariants.snippet.php`

> **Tip:** You can also run `_build/install.snippets.sql` to install the optional snippets via SQL (remember to replace the table prefix).

---

### 4. Register the Manager Page (CMP)

Run `_build/install.menu.sql` in your database.

> Replace `mdx_` with your actual prefix before running.

This creates:
- The `blockab` namespace
- The menu action
- The "BlockAB" entry under **Components** in the MODX Manager

Then go to **Site > Clear Cache** and reload the Manager. **BlockAB** will appear in the top menu under Components.

> **Note on cache:** Namespace resolution is cached separately. If the menu item doesn't appear after clearing cache, manually empty the `core/cache/` folder on the server.

---

### 5. Add MIGX Fields

Add these two fields to your MIGX configuration (e.g., in an "A/B Settings" tab):

```json
[
    {
        "field": "ab_test_group",
        "caption": "A/B Test Group",
        "description": "Leave empty for no test",
        "inputTVtype": "text"
    },
    {
        "field": "ab_test_variant",
        "caption": "Variant Key",
        "inputTVtype": "listbox",
        "inputOptionValues": "A||B||C||D||E"
    }
]
```

**Dynamic dropdowns (after creating tests in the CMP):**

```json
{
    "field": "ab_test_group",
    "inputTVtype": "listbox",
    "inputOptionValues": "@EVAL return $modx->runSnippet('BlockABGetTestGroups');"
}
```

---

### 6. Update Your Template

Wrap your MIGX foreach loop with the BlockAB check:

```smarty
{foreach json_decode($_modx->resource.migx_holder, true) as $block index=$index}

    {if $block.ab_test_group}
        {set $shouldShow = $_modx->runSnippet('BlockAB', [
            'testGroup' => $block.ab_test_group,
            'variant'   => $block.ab_test_variant
        ])}
        {if !$shouldShow}{continue}{/if}
    {/if}

    {include ('file:modules/' ~ $block.MIGX_formname ~ '/' ~ $block.MIGX_formname ~ '.tpl') block=$block}

{/foreach}
```

Blocks without `ab_test_group` are always shown. Blocks with an unknown or empty group are also shown (fail-safe).

---

## Creating Your First Test

### 1. Open the CMP

Go to **Components > BlockAB** in the MODX Manager.

### 2. Create a Test

Click **Create Test** and fill in:

| Field | Example | Notes |
|---|---|---|
| Name | `Homepage Hero Test` | Display name |
| Test Group | `homepage_hero` | Unique key, used in MIGX. Lowercase, underscores only. |
| Active | ✓ | Enable immediately |
| Smart Optimize | ✓ | Auto-shifts traffic to the winning variant after threshold |
| Threshold | `100` | Conversions before optimization starts |
| Randomize % | `25` | % random traffic kept after threshold (to keep testing) |

### 3. Add Variants

Click the test name to open it, then go to the **Varianten** tab and add at least two variants:

| Variant Key | Name | Weight |
|---|---|---|
| `A` | Control | 100 |
| `B` | Treatment | 100 |

Equal weights = equal distribution. Higher weight = more traffic.

### 4. Add Content in MIGX

In a MODX page, add your MIGX blocks. For each variant of the same section, use the same `ab_test_group` and a different `ab_test_variant`.

**Example — testing two hero sections:**

| Block | ab_test_group | ab_test_variant |
|---|---|---|
| Hero — blue CTA | `homepage_hero` | `A` |
| Hero — red CTA | `homepage_hero` | `B` |

You can put as many blocks as you like in the same group. All blocks with the picked variant are shown; the rest are hidden.

### 5. Track Conversions

On your thank-you / success / confirmation page, place (uncached!):

```
[[!BlockABConversion]]
```

This registers a conversion for all tests the visitor participated in during this browser session. Each test is counted only once per session (page refreshes do not create duplicates).

For specific tests only:
```
[[!BlockABConversion? &tests=`1,2`]]
```

**Debug mode** (only visible to logged-in managers):
```
[[!BlockABConversion? &debug=`1`]]
```

---

## Reading Results

Open **Components > BlockAB**, click a test name, and go to the **Statistieken** tab.

| Indicator | Meaning |
|---|---|
| Green banner | Statistically significant result (95% or 99% confidence) |
| Yellow banner | Test running, not yet conclusive |
| Grey banner | Insufficient data (< 100 views or < 5 conversions) |
| 🏆 trophy | Current winning variant |

Use the **7 / 30 / 60 / 90 / All** buttons to filter by period.

Click **Test Stoppen** in the green banner to deactivate the test once you have a winner.

---

## Archiving vs Deleting Tests

- **Archiveren** — moves the test to the Archived tab. Data is preserved. Test is deactivated.
- **Permanent Verwijderen** — removes the test and all pick/conversion data. Cannot be undone.

The delete dialog always offers both options.

---

## Troubleshooting

**Menu item doesn't appear after installation**
→ Manually empty the `core/cache/` folder on the server (namespace cache is not cleared by MODX's built-in cache clear).

**Always seeing the same variant**
→ Correct. BlockAB uses session persistence so each visitor consistently sees one variant. Open an incognito window or clear cookies to see a different variant.

**Conversion not registering**
→ Make sure the snippet is called **uncached**: `[[!BlockABConversion]]` (note the `!`). Also verify the visitor went through the A/B page first in the same browser session.

**Blocks not showing at all**
→ Check that the test is Active and not Archived. Verify the `test_group` matches exactly (case-sensitive).

**Debug the BlockAB snippet**
→ Pass `'debug' => 1` in the template call (visible only to managers).

---

## Uninstallation

1. Remove snippets from MODX Manager
2. Delete `core/components/blockab/` and `assets/components/blockab/`
3. Drop the tables:
   ```sql
   DROP TABLE IF EXISTS mdx_blockab_conversion;
   DROP TABLE IF EXISTS mdx_blockab_pick;
   DROP TABLE IF EXISTS mdx_blockab_variation;
   DROP TABLE IF EXISTS mdx_blockab_test;
   ```
4. Remove the BlockAB menu item and namespace from MODX Manager
5. Remove `ab_test_group` / `ab_test_variant` from your MIGX configuration
6. Restore the original MIGX foreach loop in your template
