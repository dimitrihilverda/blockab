<?php
/**
 * BlockABTestName Snippet
 *
 * Returns the display name of an A/B test given its test_group key.
 * Useful in MIGX renderChunk templates where you have ab_test_group
 * stored as a key and want to show the friendlier name in the grid.
 *
 * USAGE in a MIGX renderChunk template:
 *
 *   [[BlockABTestName? &group=`[[+ab_test_group]]`]] / [[+ab_test_variant]]
 *
 * Returns the original group key as fallback when no matching test
 * is found, so rows always render something sensible. Returns an
 * empty string when no group is provided.
 *
 * PARAMETERS:
 * @var string $group - Required. The ab_test_group key.
 *
 * @return string
 *
 * @package blockab
 */

$group = trim((string)$modx->getOption('group', $scriptProperties, ''));
if ($group === '') {
    return '';
}

// Initialize BlockAB — the constructor calls $modx->addPackage('blockab', ...)
// which is required before $modx->getObject('babTest', ...) can resolve the
// custom xPDO class.
$blockabPath      = $modx->getOption('blockab.core_path', null,
    $modx->getOption('core_path') . 'components/blockab/');
$blockabModelPath = $blockabPath . 'model/';

if (!$modx->loadClass('blockab', $blockabModelPath . 'blockab/', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[BlockAB] Could not load BlockAB class');
    return $group;
}

$blockab = new BlockAB($modx);

return $blockab->getTestNameForGroup($group);