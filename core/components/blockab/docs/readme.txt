--------------------
BlockAB
--------------------
Version: 1.1.0
Author: Moving-in.nl
License: GPLv2

A/B Testing for MIGX Blocks in MODX Revolution

BlockAB lets you run A/B tests on individual MIGX content blocks.
Instead of testing entire pages, you test specific sections — hero banners,
CTAs, pricing tables — while the rest of the page stays the same.

--------------------
FEATURES
--------------------

- Test individual MIGX blocks (not entire pages or templates)
- Consistent per-visitor variant assignment (session-based)
- Smart optimization: shift traffic to the winner after a threshold
- Weight-based random distribution per variant
- Resource filtering (individual IDs, ranges, or children)
- Conversion tracking with single-session deduplication
- Statistics dashboard with chi-square significance testing (95% / 99%)
- Winner detection with trophy highlight and confidence level
- Date range filtering on stats (7 / 30 / 60 / 90 days / all-time)
- Archived tests tab — preserve historical data without cluttering the active list
- Debug mode for managers on both BlockAB and BlockABConversion snippets
- English and Dutch translations

--------------------
INSTALLATION
--------------------

1. Install via MODX Package Manager (upload the .transport.zip)
2. Database tables are created automatically
3. Four snippets are installed: BlockAB, BlockABConversion,
   BlockABGetTestGroups, BlockABGetVariants
4. A "BlockAB" menu item appears under Components in the MODX Manager

Note: If the menu item does not appear after installation, manually clear
the core/cache/ folder on the server (namespace resolution is cached
separately from the regular MODX cache).

--------------------
QUICK START
--------------------

1. Go to Components > BlockAB in the MODX Manager
2. Create a test (e.g., "Homepage Hero", test group: homepage_hero)
3. Add variants (A, B — optionally C, D, ...)
4. In your MIGX content, add one block per variant with the same
   ab_test_group value and a different ab_test_variant value
5. Add [[!BlockABConversion]] (uncached!) to your thank-you page
6. Watch the Statistics tab fill up

--------------------
TEMPLATE INTEGRATION
--------------------

Replace your MIGX foreach loop with:

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

Blocks without ab_test_group are always shown (fail-safe).

--------------------
CONVERSION TRACKING
--------------------

Place on your thank-you / success page (MUST be uncached):

  [[!BlockABConversion]]

Registers a conversion for all tests the visitor participated in.
Each test is counted only once per browser session — page refreshes
do not create duplicate conversions.

Debug mode (visible to logged-in managers only):

  [[!BlockABConversion? &debug=`1`]]

--------------------
MIGX CONFIGURATION
--------------------

Add two fields to your MIGX configuration:

  Field: ab_test_group
  Caption: A/B Test Group
  Type: text (or listbox using BlockABGetTestGroups snippet)

  Field: ab_test_variant
  Caption: Variant Key
  Type: listbox, values: A||B||C||D||E

Dynamic dropdown (after tests exist in the database):
  inputOptionValues: @EVAL return $modx->runSnippet('BlockABGetTestGroups');

--------------------
READING RESULTS
--------------------

Open Components > BlockAB, click a test, open the Statistieken tab.

Green banner  = statistically significant result (95% or 99% confidence)
Yellow banner = test running, not yet conclusive
Grey banner   = insufficient data (< 100 views or < 5 conversions)

Use the date range buttons (7 / 30 / 60 / 90 / All) to filter by period.
Click "Test Stoppen" when you have a clear winner.

--------------------
SUPPORT & BUGS
--------------------

https://github.com/moving-in-nl/blockab
