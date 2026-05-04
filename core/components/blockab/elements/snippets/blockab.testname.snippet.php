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

$test = $modx->getObject('babTest', array('test_group' => $group));
if (!$test) {
    return $group;
}

$name = $test->get('name');
return !empty($name) ? $name : $group;
