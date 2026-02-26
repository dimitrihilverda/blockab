# BlockAB CMP Installation Guide

## What's Included

The CMP (Custom Manager Page) provides a visual interface for managing A/B tests:

✅ **Tests Management**
- Create, edit, and delete tests
- View test statistics
- Configure smart optimization settings

✅ **Variations Management**
- Add/edit/remove variations for each test
- Set variant keys (A, B, C, etc.)
- Configure weights for distribution

✅ **Statistics Dashboard**
- View picks and conversions per variant
- See conversion rates
- Compare performance

## Installation

### 1. Files Already Copied

The `build.simple.php` script has already copied:
- Controllers → `core/components/blockab/controllers/`
- Processors → `core/components/blockab/processors/mgr/`
- JavaScript → `assets/components/blockab/js/mgr/`
- CSS → `assets/components/blockab/css/`
- Templates → `core/components/blockab/templates/`

### 2. Add Menu Item to MODX

Run this SQL in your database (or use phpMyAdmin):

```bash
# Open the file:
_build/install.menu.sql
```

**Or manually via MODX Manager:**

1. Go to **System > Actions**
2. Create a new Namespace:
   - Name: `blockab`
   - Core Path: `{core_path}components/blockab/`
   - Assets Path: `{assets_path}components/blockab/`

3. Create an Action:
   - Namespace: `blockab`
   - Controller: `index`
   - Has Layout: Yes
   - Language Topics: `blockab:default`

4. Go to **System > Menus**
5. Create a new Menu:
   - Lexicon Key: `blockab`
   - Description: `blockab.menu_desc`
   - Action: Select the action you just created
   - Parent: `components`

### 3. Clear Cache

Go to **Site > Clear Cache** in MODX Manager

### 4. Access the CMP

1. Refresh your MODX Manager page
2. Look for **"BlockAB"** in the top menu under Components
3. Click it to open the CMP

## Using the CMP

### Create Your First Test

1. **Open BlockAB** from Components menu
2. Click **"Create Test"**
3. Fill in:
   - **Name**: "Homepage Hero Test"
   - **Test Group**: `homepage_hero` (this is what you'll use in MIGX)
   - **Description**: Optional notes
   - **Active**: Check this box
   - **Smart Optimize**: Enable for automatic optimization
   - **Threshold**: 100 (conversions before optimization starts)
   - **Randomize %**: 25 (% random picks after threshold)
4. Click **Save**

### Add Variations

1. **Click on the test name** to open test details
2. Go to **"Variations"** tab
3. Click **"Create Variation"**
4. Fill in:
   - **Variant Key**: `A` (must match MIGX dropdown!)
   - **Name**: "Variant A - Blue Background"
   - **Description**: Optional
   - **Weight**: 100 (higher = shown more often)
   - **Active**: Check this box
5. Click **Save**
6. **Repeat** to add Variant B, C, etc.

### View Statistics

1. Click on a test name
2. Go to **"Stats"** tab
3. View:
   - Total views (picks)
   - Total conversions
   - Overall conversion rate
   - Per-variant performance

## Connecting CMP to MIGX

Now that you have tests in the CMP, you can use dynamic dropdowns in MIGX!

### Update Your MIGX Configuration

Change the `ab_test_group` field to use a dynamic dropdown:

```json
{
    "field": "ab_test_group",
    "caption": "A/B Test Group",
    "description": "Select from existing test groups",
    "inputTVtype": "listbox",
    "inputOptionValues": "@EVAL return $modx->runSnippet('BlockABGetTestGroups');"
}
```

**Don't forget to create the `BlockABGetTestGroups` snippet first!**
(See main INSTALL.md, step 3 - Optional Snippets)

### Workflow

1. **CMP**: Create test "homepage_hero" with variants A, B
2. **MIGX**: Select "homepage_hero" from dropdown
3. **MIGX**: Select variant "A" or "B"
4. **Frontend**: Only one variant shows per user
5. **CMP**: View results in Stats tab

## Troubleshooting

**Menu item doesn't appear?**
- Clear cache
- Check if namespace and action were created correctly
- Make sure files are in the right location
- Check browser console for JavaScript errors

**CMP shows blank page?**
- Check browser console for errors
- Verify connector.php exists and is accessible
- Make sure BlockAB class is loaded (`blockab.class.php`)

**Can't create tests?**
- Check database tables exist (run `install.sql`)
- Verify connector URL is correct
- Check MODX error log

**Statistics not loading?**
- Make sure test has data (picks/conversions)
- Check processor is working: `mgr/stats/getstats`
- Verify BlockAB class has `getSum()` method

## Next Steps

### Enhance the CMP

Now that you have a working CMP, you can add:

1. **Charts** (later enhancement)
   - Line charts for trend over time
   - Bar charts for variant comparison
   - Use Chart.js or similar library

2. **Advanced Filtering**
   - Filter by date range
   - Filter by active/archived
   - Search functionality

3. **Batch Operations**
   - Archive multiple tests
   - Clear data for tests
   - Duplicate tests

4. **Export**
   - Export stats to CSV
   - Generate reports

5. **Quick Actions**
   - Toggle active/inactive
   - Quick edit variant weights
   - Preview mode

## File Structure

```
core/components/blockab/
├── controllers/
│   ├── home.class.php          # Main tests list
│   └── test.class.php           # Test detail page
├── processors/
│   └── mgr/
│       ├── test/                # CRUD for tests
│       ├── variation/           # CRUD for variations
│       └── stats/               # Statistics
├── templates/
│   ├── home.tpl
│   └── test.tpl

assets/components/blockab/
├── css/
│   └── mgr.css
├── js/
│   └── mgr/
│       ├── blockab.js           # Main JS class
│       ├── sections/            # Page loaders
│       └── widgets/             # ExtJS components
└── connector.php                # AJAX endpoint
```

## Support

Having issues? Check:
1. MODX error log: `core/cache/logs/error.log`
2. Browser console (F12)
3. Check processor responses in Network tab

Good luck with your A/B testing! 🎉
