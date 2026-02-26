<?php
/**
 * BlockAB Get Test Groups Snippet
 *
 * Returns available test groups for use in MIGX dropdowns
 * Returns format: group_key==Group Name||group_key2==Group Name 2
 *
 * USAGE IN MIGX:
 * In your MIGX field configuration, set:
 * - inputTVtype: "listbox"
 * - inputOptionValues: @EVAL return $modx->runSnippet('BlockABGetTestGroups');
 *
 * PARAMETERS:
 * @var string $format - Output format: "migx" (default) or "json"
 * @var bool $includeInactive - Include inactive tests (default: false)
 * @var bool $includeArchived - Include archived tests (default: false)
 *
 * @return string Formatted list of test groups
 *
 * @package blockab
 */

// Get parameters
$format = $modx->getOption('format', $scriptProperties, 'migx');
$includeInactive = (bool)$modx->getOption('includeInactive', $scriptProperties, false);
$includeArchived = (bool)$modx->getOption('includeArchived', $scriptProperties, false);

// Initialize BlockAB
$blockabPath = $modx->getOption('blockab.core_path', null,
    $modx->getOption('core_path') . 'components/blockab/');
$blockabModelPath = $blockabPath . 'model/';

if (!$modx->loadClass('blockab', $blockabModelPath . 'blockab/', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[BlockAB] Could not load BlockAB class');
    return '';
}

$blockab = new BlockAB($modx);

// Use class method
return $blockab->getTestGroupsForDropdown($includeInactive, $includeArchived, $format);
