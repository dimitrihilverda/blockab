<?php
/**
 * BlockAB Get Variants Snippet
 *
 * Returns available variants for a specific test group for use in MIGX dropdowns
 * Returns format: A||B||C or A==Variant A||B==Variant B
 *
 * USAGE IN MIGX:
 * In your MIGX field configuration, set:
 * - inputTVtype: "listbox"
 * - inputOptionValues: @EVAL return $modx->runSnippet('BlockABGetVariants', array('testGroup' => $scriptProperties['ab_test_group']));
 *
 * Note: This requires the test group to be selected first. For a simpler setup,
 * just use a static list: A||B||C||D||E
 *
 * PARAMETERS:
 * @var string $testGroup - The test group to get variants for
 * @var string $format - Output format: "simple" (A||B||C) or "detailed" (A==Variant A||B==Variant B)
 * @var bool $includeInactive - Include inactive variations (default: false)
 *
 * @return string Formatted list of variants
 *
 * @package blockab
 */

// Get parameters
$testGroup = $modx->getOption('testGroup', $scriptProperties, '');
$format = $modx->getOption('format', $scriptProperties, 'detailed');
$includeInactive = (bool)$modx->getOption('includeInactive', $scriptProperties, false);

// Initialize BlockAB
$blockabPath = $modx->getOption('blockab.core_path', null,
    $modx->getOption('core_path') . 'components/blockab/');
$blockabModelPath = $blockabPath . 'model/';

if (!$modx->loadClass('blockab', $blockabModelPath . 'blockab/', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[BlockAB] Could not load BlockAB class');
    return 'A||B||C';
}

$blockab = new BlockAB($modx);

// Use class method
return $blockab->getVariantsForDropdown($testGroup, $includeInactive, $format);
