# BlockAB - A/B Testing for MIGX Blocks

A/B testing component for MODX Revolution that allows you to test individual MIGX blocks instead of entire pages.

## Features

- 🎯 **Block-level testing** - Test individual MIGX blocks, not entire pages
- 🧠 **Smart optimization** - Automatically show best-performing variants after threshold
- ⚖️ **Weight-based distribution** - Control how often each variant is shown
- 👤 **Session persistence** - Users see the same variant throughout their session
- 📊 **Conversion tracking** - Track which variants lead to conversions
- 🔧 **Easy integration** - Works seamlessly with existing MIGX workflows

## Installation

1. Install via MODX Package Manager (coming soon) or manually:
   - Build the package using `_build/build.transport.php`
   - Install the generated transport package

2. Add two fields to your MIGX configuration:
   - `ab_test_group` (text) - Identifier for the test group
   - `ab_test_variant` (text) - Variant key (A, B, C, etc.)

3. Update your template to use the BlockAB snippet (see Usage below)

## Usage

### 1. In Your Template

Replace your standard MIGX loop with:

```smarty
{foreach json_decode($_modx->resource.migx_holder, true) as $block index=$index}

    {* Check if block is part of A/B test *}
    {if $block.ab_test_group}
        {set $shouldShow = $_modx->runSnippet('BlockAB', [
            'testGroup' => $block.ab_test_group,
            'variant' => $block.ab_test_variant
        ])}

        {if !$shouldShow}
            {continue}
        {/if}
    {/if}

    {* Render block normally *}
    {include ('file:modules/' ~ $block.MIGX_formname ~ '/' ~ $block.MIGX_formname ~ '.tpl') block=$block}

{/foreach}
```

### 2. Create a Test

1. Go to **Components > BlockAB** in MODX manager
2. Create a new test
3. Set a unique Test Group identifier (e.g., `homepage_hero`)
4. Add variations with variant keys (A, B, C, etc.)
5. Configure optimization settings
6. Activate the test

### 3. Add Content Variants

When editing a page:
1. Create multiple MIGX blocks for the same position
2. Set the same `ab_test_group` value for all variants
3. Set different `ab_test_variant` values (A, B, C, etc.)
4. Only one variant will be shown to each visitor

### 4. Track Conversions

On your success/thank-you page:

```
[[!BlockABConversion]]
```

Or track specific tests:

```
[[!BlockABConversion? &tests=`1,2,3`]]
```

## Example

**Scenario:** Test different hero images on homepage

1. **Create test** in BlockAB manager:
   - Name: "Homepage Hero Test"
   - Test Group: `homepage_hero`
   - Add variation A (Variant Key: `A`)
   - Add variation B (Variant Key: `B`)

2. **Add content** on your homepage:
   - Hero block #1: `ab_test_group = "homepage_hero"`, `ab_test_variant = "A"`
   - Hero block #2: `ab_test_group = "homepage_hero"`, `ab_test_variant = "B"`

3. **Monitor results** in BlockAB manager

## Smart Optimization

BlockAB includes intelligent optimization:

- **Threshold**: Number of conversions before optimization starts (default: 100)
- **Randomize %**: After threshold, percentage that still sees random variant (default: 25%)
- **Weight**: Per-variation weight for random distribution (default: 100)

This ensures you collect enough data before optimizing, while continuing to explore alternatives.

## Requirements

- MODX Revolution 2.6+
- PHP 7.0+
- MIGX Extra

## Development

Built by [Moving-in.nl](https://moving-in.nl)

### Building from source

```bash
# Generate model classes
php _build/build.model.php

# Build transport package
php _build/build.transport.php
```

Package will be created in `core/packages/`

## License

GNU General Public License v2.0

## Support

For issues, questions, or feature requests:
- GitHub Issues: [moving-in-nl/blockab](https://github.com/dimitrihilverda/blockab)
