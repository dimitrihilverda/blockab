<?php
/**
 * BlockAB English Lexicon
 *
 * @package blockab
 * @subpackage lexicon
 */

$_lang['blockab'] = 'BlockAB';
$_lang['blockab.menu_desc'] = 'Manage A/B tests for MIGX blocks';
$_lang['blockab.back_to_overview'] = 'Back to Tests Overview';
$_lang['blockab.about'] = 'About BlockAB';
$_lang['blockab.about_text'] = "BlockAB — A/B testing module for MIGX blocks\n\nBlockAB lets you set up A/B tests for content blocks managed via MIGX. The system records views and conversions per variant and automatically calculates statistical significance using a chi-square test.\n\n© 2026 Moving-in.nl";
$_lang['blockab.manual'] = 'Manual';
$_lang['blockab.manual_text'] = 'BlockAB documentation is available on GitHub:\nhttps://github.com/moving-in-nl/blockab';

// Tests
$_lang['blockab.test'] = 'Test';
$_lang['blockab.tests'] = 'Tests';
$_lang['blockab.test_create'] = 'Create Test';
$_lang['blockab.test_update'] = 'Edit Test';
$_lang['blockab.test_duplicate'] = 'Duplicate Test';
$_lang['blockab.test_remove'] = 'Remove Test';
$_lang['blockab.test_remove_confirm'] = 'Are you sure you want to remove this test?';
$_lang['blockab.test_remove_permanent'] = 'Delete Permanently';
$_lang['blockab.test_remove_dialog_msg'] = 'Do you want to archive this test or delete it permanently?<br><br><b>Archive</b> &mdash; test and statistics are preserved, the test will be deactivated.<br><b>Delete permanently</b> &mdash; the test and all statistics will be permanently deleted and cannot be recovered.';
$_lang['blockab.test_archive'] = 'Archive Test';
$_lang['blockab.test_archive_confirm'] = 'Are you sure you want to archive this test? The test will be deactivated.';
$_lang['blockab.test_unarchive'] = 'Unarchive Test';
$_lang['blockab.test_unarchive_confirm'] = 'Are you sure you want to restore this test from the archive?';

// Test fields
$_lang['blockab.test.name'] = 'Name';
$_lang['blockab.test.description'] = 'Description';
$_lang['blockab.test.test_group'] = 'Test Group';
$_lang['blockab.test.test_group_desc'] = 'Unique identifier for this test (e.g., "homepage_hero")';
$_lang['blockab.test.active'] = 'Active';
$_lang['blockab.test.archived'] = 'Archived';
$_lang['blockab.test.smartoptimize'] = 'Smart Optimize';
$_lang['blockab.test.smartoptimize_desc'] = 'Automatically optimize to show best performing variant';
$_lang['blockab.test.threshold'] = 'Threshold';
$_lang['blockab.test.threshold_desc'] = 'Number of conversions before optimization starts';
$_lang['blockab.test.randomize'] = 'Randomize %';
$_lang['blockab.test.randomize_desc'] = 'Percentage of time to show random variant (after threshold)';
$_lang['blockab.test.resources'] = 'Resources';
$_lang['blockab.test.resources_desc'] = 'Comma-separated resource IDs (optional). Use 5> for children, 3-5 for range';
$_lang['blockab.test.contexts'] = 'Contexts';
$_lang['blockab.test.contexts_desc'] = 'Comma-separated context keys (optional)';

// Variations
$_lang['blockab.variation'] = 'Variation';
$_lang['blockab.variations'] = 'Variations';
$_lang['blockab.variation_create'] = 'Create Variation';
$_lang['blockab.variation_update'] = 'Edit Variation';
$_lang['blockab.variation_duplicate'] = 'Duplicate Variation';
$_lang['blockab.variation_remove'] = 'Remove Variation';
$_lang['blockab.variation_remove_confirm'] = 'Are you sure you want to remove this variation?';

// Variation fields
$_lang['blockab.variation.name'] = 'Name';
$_lang['blockab.variation.variant_key'] = 'Variant Key';
$_lang['blockab.variation.variant_key_desc'] = 'Key used in MIGX blocks (e.g., "A", "B", "C")';
$_lang['blockab.variation.description'] = 'Description';
$_lang['blockab.variation.active'] = 'Active';
$_lang['blockab.variation.weight'] = 'Weight';
$_lang['blockab.variation.weight_desc'] = 'Higher weight = more likely to be shown (default: 100)';

// Statistics
$_lang['blockab.stats'] = 'Statistics';
$_lang['blockab.stats.picks'] = 'Views';
$_lang['blockab.stats.conversions'] = 'Conversions';
$_lang['blockab.stats.conversion_rate'] = 'Conversion Rate';
$_lang['blockab.stats.no_data'] = 'No data yet';
$_lang['blockab.stats.total_views'] = 'Total Views';
$_lang['blockab.stats.best_rate'] = 'Best Rate';
$_lang['blockab.stats.days'] = 'days';
$_lang['blockab.stats.period'] = 'Period';
$_lang['blockab.stats.status'] = 'Status';
$_lang['blockab.stats.significant_95'] = 'Statistically significant (95% confidence)';
$_lang['blockab.stats.significant_99'] = 'Statistically significant (99% confidence)';
$_lang['blockab.stats.not_significant'] = 'Test running — not yet conclusive';
$_lang['blockab.stats.insufficient_data'] = 'Insufficient data';
$_lang['blockab.stats.winner'] = 'Winner';
$_lang['blockab.stats.stop_test'] = 'Stop Test';
$_lang['blockab.stats.stop_test_confirm'] = 'Are you sure you want to stop the test? The test will be deactivated.';
$_lang['blockab.stats.min_samples_warning'] = '{views} views — minimum {needed} required';
$_lang['blockab.stats.variant'] = 'Variant';
$_lang['blockab.stats.test_stopped'] = 'Test stopped';

// Messages
$_lang['blockab.test_saved'] = 'Test saved successfully';
$_lang['blockab.test_removed'] = 'Test removed successfully';
$_lang['blockab.variation_saved'] = 'Variation saved successfully';
$_lang['blockab.variation_removed'] = 'Variation removed successfully';

// Errors
$_lang['blockab.error.test_not_found'] = 'Test not found';
$_lang['blockab.error.variation_not_found'] = 'Variation not found';
$_lang['blockab.error.missing_testgroup'] = 'Test group is required';
$_lang['blockab.error.missing_variant_key'] = 'Variant key is required';
$_lang['blockab.error.test_name_required'] = 'Test name is required';
$_lang['blockab.error.variation_name_required'] = 'Variation name is required';
$_lang['blockab.error.variation_key_required'] = 'Variant key is required';
$_lang['blockab.error.test_required'] = 'Test is required';
$_lang['blockab.error.variant_key_exists'] = 'This variant key already exists for this test';
$_lang['blockab.error.test_group_exists'] = 'This test group already exists. Please choose a unique test group name.';
$_lang['blockab.error.test_save_failed'] = 'Failed to save test';
$_lang['blockab.error.variation_save_failed'] = 'Failed to save variation';
