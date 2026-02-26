# BlockAB — Marketing Guide

**For:** Marketing staff who want to set up, monitor and close A/B tests.
**Assumption:** BlockAB is installed and all pages are ready for use.

---

## What is an A/B test?

You show visitors different versions of the same block (e.g. a different headline, button text or image). BlockAB tracks which version generates the most conversions and automatically calculates whether the difference is statistically reliable.

---

## 1. Navigating to BlockAB

In the MODX admin panel, go to **Components → BlockAB**.

You will see two tabs:
- **Tests** — all active and running tests
- **Archived** — closed tests

---

## 2. Creating a new test

Click **Create Test** (or right-click → Create in the grid).

![Create test form](docs/screenshots/stap2-test-aanmaken.png)

Fill in:

| Field | What to enter |
|---|---|
| **Name** | A clear description, e.g. `Sign-up Form CTA` |
| **Test Group** | A unique code without spaces, e.g. `signup_cta`. You will use this code when linking MIGX blocks to the test. |
| **Description** | Optional: your hypothesis or goal |
| **Active** | Leave **off** until you have linked the blocks on the page |
| **Smart Optimize** | Leave **on** — BlockAB will automatically send more traffic to the best-performing variant |
| **Threshold** | Number of conversions before smart optimisation starts (default: 100). Do not change unless you have a reason to. |
| **Randomize %** | Percentage of visitors who still receive a random variant after the threshold (default: 25). Do not change. |

Click **Save**.

---

## 3. Adding variants

After saving, a **Variants** overview appears at the bottom of the screen.

Click **Create Variant** and fill in per variant:

![Create variant form](docs/screenshots/stap3-variant-aanmaken.png)

| Field | What to enter |
|---|---|
| **Variant Key** | The letter for this variant: `A`, `B`, `C`, … The first available letter is suggested automatically. |
| **Name** | A descriptive name for the variant, e.g. `Sign up`, `Start for free` or `Try now` |
| **Description** | Optional: briefly note what you are testing in this variant, e.g. `Emphasis on free entry point` |
| **Weight** | How often this variant is shown relative to others. Leave at `100` for equal distribution. |
| **Active** | Leave **on** to include this variant in the test |

Create at least **2 variants**. More is possible, but with low website traffic it takes longer to get reliable results.

---

## 4. Linking blocks on the page

Now create a MIGX block for each variant (or edit an existing block) on the page you want to test.

1. Go to the page in the MODX manager
2. Click the MIGX field (e.g. "Assemble page")
3. Click **Add item** and choose the block type, or open an existing block via **Edit**
4. Go to the **A/B settings** tab
5. Select your test under **A/B Test Group** — the variant dropdown fills automatically
6. Select the correct **Variant** (A, B, C) — the first available variant is selected automatically
7. Fill in the rest of the block content (text, image, etc.) to match the variant
8. Save the block
9. Repeat for each variant

> **Tip:** Create one block per variant. Make sure all blocks use the same block type and that only the element you are testing differs.

---

## 5. Activating the test

Once all blocks have been linked:

1. Right-click the test in the grid → **Edit**
2. Set **Active** to on
3. Save

The test is now running. Visitors will automatically be shown a random variant.

---

## 6. Viewing statistics

Click on a test to open the statistics and go to the **Statistics** tab.

![Statistics page](docs/screenshots/stap5-statistieken.png)

### Period

At the top you can choose the period for which you want to see the statistics: **7 days**, **30 days**, **60 days** or **90 days**. The active period is highlighted in blue.

### The three summary numbers

| Number | Meaning |
|---|---|
| **Total Views** | How many times a variant has been shown to unique visitors |
| **Conversions** | How many of those visitors then completed a conversion |
| **Best Rate** | The highest conversion rate across all variants |

### The status banner

Directly below the cards you see a banner showing how the test is progressing:

| Banner | Meaning |
|---|---|
| 🟢 **Green** — *✓ Winner: X — Statistically significant (95% / 99% confidence)* | There is a reliable winner. The **Stop Test** button appears here. |
| 🟡 **Yellow** — *Test running — not yet conclusive* | The test is active but does not yet have enough data. Wait. |
| ⬜ **Grey** — *Test stopped — Winner: X* | The test has been stopped. The winner is shown. |
| ⬜ **Grey** — *No data* | The test has not yet started or has no views yet. |

The **green banner** also shows the reliability level: **95% confidence** or **99% confidence**. The higher, the more certain the result.

### The variants table

Per variant you see:

| Column | Meaning |
|---|---|
| **Variant** | The letter (A, B, C) and a 🏆 for the winner |
| **Name** | The name you entered when creating the variant |
| **Views** | Number of unique impressions |
| **Conversions** | Number of conversions |
| **Rate %** | Conversion rate — the higher the better |
| **Progress bar** | Visual comparison against the best-performing variant |
| **Status** | *Winner* for the winning variant |

The **winning row has a green background** and a green progress bar. Variants with fewer than 30 views get a warning icon — that data is not yet reliable.

### Date filter

Use the **7 / 30 / 60 / 90 days** buttons to see how the test performed in a specific period.

---

## 7. Stopping the test

When the green banner appears and you are convinced of the winner:

![Green banner with Stop Test button](docs/screenshots/stap6-winnaar-banner.png)

1. Click **Stop Test** in the green banner
2. Confirm the action

The test is set to inactive. All variants remain visible on the website.

**Then clean up the losing blocks yourself:**
1. Go to the page in the MODX manager
2. Open the MIGX field
3. Delete all blocks except the winning block
4. Clear the A/B settings on the winning block (empty the A/B Test Group field) so it is always shown
5. Save the page

---

## 8. Archiving a test

Closed tests can be archived to keep the Tests overview tidy:

1. Right-click the test → **Archive**
2. Confirm

Archived tests can be found in the **Archived** tab and can also be restored from there if needed.

> ⚠ **Note:** If you choose **Delete** (permanent), all statistics are gone forever. Use **Archive** instead.

---

## 9. When is a result reliable?

BlockAB does this automatically via a statistical test (chi-square). But here are practical guidelines:

| Situation | What to do |
|---|---|
| Green banner, 95% confidence | You may draw conclusions. Stop the test when you are ready. |
| Green banner, 99% confidence | Excellent reliability. Winner is clear. |
| Yellow banner, fewer than 100 views | Wait. Too little data for conclusions. |
| Yellow banner, more than 100 views | Test is running well. Wait for more conversions. |
| Active for weeks, no winner | Consider more radical variants or test a different element. |

**Rule of thumb:** Let a test run for at least **2 weeks**, even if a winner appears quickly.

---

## 10. Tips for good tests

**Test one thing at a time**
Only change the headline, or the button text, or the image. Not multiple things at once — then you won't know what made the difference.

**Formulate a hypothesis first**
_"I expect that mentioning a price in the headline will generate more quote requests, because visitors immediately know what to expect."_

**Choose high-impact elements**
Hero blocks, call-to-action buttons and headlines have the most impact on conversion.

**Be patient**
A test with little traffic needs weeks. Don't force conclusions based on too little data — the banner tells you when the time is right.

**Document the outcomes**
Note what you tested and what you learned. That helps with future tests.

---

## Quick overview: the workflow

```
1. Create test in BlockAB (name + test group key)
          ↓
2. Create variants (A, B, C with names and keys)
          ↓
3. Link blocks on the page (MIGX → A/B settings tab)
          ↓
4. Set test to Active in BlockAB
          ↓
5. Monitor test via statistics (daily or weekly)
          ↓
6. Green banner → determine winner → stop test
          ↓
7. Remove losing blocks → archive test
```

---

*Technical questions about installation or templates? Contact your developer.*
