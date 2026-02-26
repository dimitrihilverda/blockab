# BlockAB User Guide

A complete guide to setting up and using A/B tests for MIGX blocks.

---

## What is A/B Testing?

A/B testing (also known as split testing) is a method of comparing two or more versions of a web page element to see which performs better.

**Example:** You want to know whether a green or blue button on your homepage encourages more visitors to fill in a form. With A/B testing you show 50% of visitors the green button (variant A) and 50% the blue button (variant B). After a while you can see which button generates more conversions.

---

## How does BlockAB work?

BlockAB is specifically designed for testing **individual MIGX blocks** on your pages. Instead of duplicating entire pages, you can test different versions of a single block.

### What is tracked?

BlockAB tracks two key statistics:

#### 1. **Views (Picks)**
Every time a visitor is shown a variant of your block, this counts as 1 view.

**Important:**
- If the same visitor revisits the page (within the same browser session), they will see the **same variant** and this is **not counted again**
- If the visitor closes their browser and comes back later (new session), this does count as a new view
- This ensures you get reliable data without duplicate counts

#### 2. **Conversions**
A conversion is a desired action that a visitor performs after seeing a variant. For example:
- Filling in a contact form
- Downloading a brochure
- Requesting a quote

You decide what counts as a conversion by placing the BlockAB conversion tracking on the appropriate page (usually the "thank you" page).

### How does variant selection work?

BlockAB works intelligently:

1. **Start phase (Random)**: Initially, visitors are shown a variant at random, respecting the weight you have set per variant
2. **Optimisation phase**: Once enough conversions have been collected (default: 100), BlockAB automatically shows the best-performing variant more often
3. **Continuous exploration**: Even after reaching the threshold, a percentage of visitors (default: 25%) still sees a random variant, so BlockAB keeps learning

This is called **Smart Optimization**.

---

## Step-by-step: Setting up your first A/B test

### Step 1: Create a new test

1. In the MODX menu go to **Components > BlockAB**
2. Click the **Create Test** button
3. Fill in the following fields:

**Basic information:**
- **Name**: Give your test a clear name, e.g. "Homepage Hero Test — Green vs Blue CTA"
- **Description** (optional): Notes about what you are testing and why
- **Test Group**: The unique identifier for this test, e.g. `homepage_hero_cta`. This is created automatically based on the name, but you can adjust it
- **Active**: Check to activate the test immediately (you can also do this later)

**Smart Optimization settings:**
- **Smart Optimize**: Leave checked to automatically show the best-performing variant more often
- **Conversion Threshold**: Number of conversions before optimisation starts (default: 100)
- **Randomize Percentage**: Percentage of visitors who still see a random variant after the threshold (default: 25%)

4. Click **Save**

### Step 2: Add variants

After saving your test, a **Variations** grid appears at the bottom of the screen.

1. Click **Create Variation**
2. Fill in the following fields:

- **Name**: Descriptive name for this variant, e.g. "Green CTA button"
- **Variant Key**: The letter used in MIGX (e.g. A, B, C). The first available key is suggested automatically
- **Description** (optional): Extra notes about this variant
- **Active**: Check to make this variant available
- **Weight**: Relative weight for random distribution (default: 100). If all variants have weight 100, they each get an equal chance

3. Click **Save**
4. Repeat this for each variant you want to test (minimum 2 variants)

**Example variants for a hero block test:**
- Variant A (Key: A) — "Control — Original blue button" — Weight: 100
- Variant B (Key: B) — "Green button with urgency text" — Weight: 100
- Variant C (Key: C) — "Red button without text" — Weight: 100

### Step 3: Create the content in MIGX

Now you create the actual content for each variant.

1. Go to the page where you want to run the test
2. Open the MIGX field containing your blocks
3. **Create a separate MIGX block for each variant** with the same content, but with small variations

For each MIGX item:
1. Fill in the normal content (text, image, etc.)
2. Go to the **A/B settings** tab
3. Select your **Test Group** from the dropdown (e.g. "Homepage Hero Test — Green vs Blue CTA (homepage_hero_cta)")
4. Select the **Variant** (A, B, or C)
5. Adjust the content to represent the variant (e.g. change the button colour)

**Example:**
- MIGX block 1: Test Group = `homepage_hero_cta`, Variant = `A — Control — Original blue button`, blue button
- MIGX block 2: Test Group = `homepage_hero_cta`, Variant = `B — Green button with urgency text`, green button
- MIGX block 3: Test Group = `homepage_hero_cta`, Variant = `C — Red button without text`, red button

4. Save the page

**Important:** Make sure all other content (texts, images, positions) is identical, except for the element you are testing. This ensures that differences in results are caused by the variant and not by other factors.

### Step 4: Setting up tracking (conversion measurement)

To measure which variant performs better, you need to track conversions.

1. Decide what you want to measure as a conversion (e.g. form submitted)
2. Go to the page where the conversion takes place (usually the "thank you" or "success" page)
3. Place the following code in the template:

```
[[!BlockABConversion]]
```

This registers a conversion for all active tests the visitor has seen.

**Or** if you only want to measure specific tests:

```
[[!BlockABConversion? &tests=`1,2,3`]]
```

(Replace 1,2,3 with the IDs of your tests)

### Step 5: Test and monitor

1. **Test your setup:**
   - Visit the page in an incognito window
   - Refresh a few times and check whether you see different variants
   - Go through the conversion process to check that tracking works

2. **Monitor the results:**
   - Go to **Components > BlockAB**
   - Click on your test to see the statistics
   - In the Statistics tab you can see for each variant:
     - **Views**: Number of times shown
     - **Conversions**: Number of conversions
     - **Rate %**: Conversion rate (the higher the better)

3. **Let the test run:**
   - Wait until you have enough data (minimum 100 conversions is recommended)
   - Look at the conversion rate differences
   - Are the differences significant enough? (10%+ difference is usually meaningful)

---

## Interpreting results

### Statistics explained

In the Statistics tab of your test you see:

- **Views**: Total number of times this variant was shown
- **Conversions**: Total number of conversions for this variant
- **Rate %**: `(Conversions / Views) × 100%`

BlockAB automatically calculates statistical significance using a chi-square test. When the green banner appears, the result is reliable.

### Which variant is the winner?

A variant is usually a clear winner when:

1. **Significant difference**: At least 10–20% better than other variants
2. **Enough data**: At least 100 conversions per variant for reliable results
3. **Consistent**: The difference persists over multiple days/weeks

**Example:**
```
Variant A: 1,250 views, 45 conversions = 3.6% conversion rate
Variant B: 1,310 views, 68 conversions = 5.2% conversion rate ← WINNER!
Variant C: 1,190 views, 38 conversions = 3.2% conversion rate
```

Variant B performs 44% better than the original variant A!

### What if there is no clear winner?

If all variants perform approximately the same:
- Let the test run longer for more data
- Or accept that the element has little impact and focus on other tests
- Consider testing more radically different variants

---

## Managing tests

### Pausing a test

1. Right-click the test → **Edit**
2. Uncheck **Active**
3. Save

The test is no longer running and all variants are simply shown (without selection).

### Archiving a test

When you are done with a test:

1. Right-click the test → **Archive**
2. Confirm

Archived tests no longer appear in the overview but remain available for reporting. They can be restored at any time from the **Archived** tab.

> ⚠ **Note:** If you choose **Delete** (permanent), all statistics are gone forever. Use **Archive** instead.

### Duplicating a test

Useful for reusing a test setup:

1. Right-click the test → **Duplicate**
2. Give it a new name and test group
3. All variants are copied (without data)

---

## Best Practices

### Planning

✅ **Test one thing at a time**: Only change the button colour OR the text, not both
✅ **Formulate a hypothesis**: "I think a green button converts better because..."
✅ **Test high-impact elements**: Hero blocks, CTAs, headlines usually have the most impact
✅ **Enough traffic**: Make sure you have enough visitors (minimum 1,000 per week recommended)

### During the test

✅ **Be patient**: Let tests run for at least 1–2 weeks
✅ **Change nothing**: No changes to variants during the test
✅ **Monitor regularly**: Check every few days that everything is running correctly
✅ **Watch for seasonality**: Take holidays, promotions, etc. into account

### After the test

✅ **Implement the winner**: Replace all variants with the winning version
✅ **Document**: Note what you learned for future tests
✅ **Next test**: Use the winner as the new baseline for a new test
✅ **Share knowledge**: Discuss results with the team

### Common mistakes

❌ **Stopping too soon**: Minimum 100 conversions per variant is needed
❌ **Testing too many things at once**: Focus on one test per page
❌ **Drawing conclusions from small differences**: 2–3% difference is usually not significant
❌ **Forgetting to track**: Always check that conversion tracking is working
❌ **Forgetting tests**: Make a schedule and keep track

---

## Practical examples

### Example 1: Hero block headline test

**Goal:** Determine which headline generates more leads

**Setup:**
- Test Group: `homepage_hero_headline`
- Variant A: "Professional moving service" (original)
- Variant B: "Stress-free moving from €99"
- Variant C: "4.8★ rating — 10,000+ satisfied customers"

**Hypothesis:** A specific price or social proof in the headline will convert better

**Conversion:** Quote request form filled in

**Result after 3 weeks:**
- Variant A: 3,245 views, 89 conversions (2.7%)
- Variant B: 3,198 views, 142 conversions (4.4%) ← WINNER
- Variant C: 3,287 views, 98 conversions (3.0%)

**Conclusion:** Mentioning the price in the headline increases conversion by 63%!

### Example 2: CTA button colour test

**Goal:** Optimal button colour for brochure download

**Setup:**
- Test Group: `services_brochure_cta`
- Variant A: Blue button (#0066CC) (original)
- Variant B: Green button (#00CC66)
- Variant C: Orange button (#FF6600)

**Hypothesis:** A more striking colour (green or orange) will perform better

**Conversion:** Brochure download started

**Result after 2 weeks:**
- Variant A: 1,876 views, 156 conversions (8.3%)
- Variant B: 1,923 views, 182 conversions (9.5%)
- Variant C: 1,845 views, 197 conversions (10.7%) ← WINNER

**Conclusion:** Orange button performs 29% better than the original!

**Follow-up test:** Now test the orange button with different texts ("Download brochure" vs "Free brochure")

---

## Frequently asked questions

### How many tests can I run simultaneously?

Technically unlimited, but practically:
- **1 test per page** is ideal
- Maximum 3–4 tests simultaneously across the whole website
- Too many tests dilute your data and slow down results

### How long should a test run?

**Minimum:** 1–2 weeks
**Optimal:** Until you have 100+ conversions per variant
**Maximum:** 4–6 weeks (results are usually clear by then)

### What if I have few visitors?

- Only test the most high-impact elements
- Choose variants that really differ (not subtle changes)
- Be patient — it can take months
- Consider increasing traffic through marketing first

### What happens if I stop the test?

When you set a test to inactive:
- All variants are simply shown (no more selection)
- Statistics remain available
- Visitors can see all variants

### Can I delete a variant during a test?

Yes, but this is not recommended:
- Data for that variant remains available
- Active visitors who saw that variant will be shown a different one
- This can affect your results

**Better:** Set the variant to inactive instead of deleting it

### How do I know if my differences are significant?

BlockAB tells you automatically via the banner. As a rule of thumb:
- **<5% difference**: Probably not significant, let the test run longer
- **5–10% difference**: Possibly significant with lots of data (200+ conversions)
- **10–20% difference**: Probably significant
- **>20% difference**: Almost certainly significant, clear winner

---

## Technical details (for developers)

### Template integration

In your Fenom template:

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

### Database tables

BlockAB uses 4 tables:
- `mdx_blockab_test`: Test definitions
- `mdx_blockab_variation`: Variant definitions
- `mdx_blockab_pick`: View statistics (per day)
- `mdx_blockab_conversion`: Conversion statistics (per day)

---

## Support

Having problems or questions?

1. Check the **error log** in MODX: **Reports > Error Log**
2. Verify that the templates have been set up correctly
3. Verify that the conversion tracking snippet is on the correct page
4. Test in incognito mode to rule out session issues

For technical problems, contact your developer.

---

**Good luck with your A/B tests! 🚀**

*By systematically testing and optimising you can significantly increase your website's conversion rate.*
