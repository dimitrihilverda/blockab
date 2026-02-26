<?php
/**
 * BlockAB Snippet
 *
 * Determines if a MIGX block should be shown based on A/B testing
 *
 * USAGE IN TEMPLATE:
 *
 * {foreach json_decode($_modx->resource.migx_holder, true) as $block index=$index}
 *
 *     {* Check if block is part of A/B test *}
 *     {if $block.ab_test_group}
 *         {set $shouldShow = $_modx->runSnippet('BlockAB', [
 *             'testGroup' => $block.ab_test_group,
 *             'variant' => $block.ab_test_variant
 *         ])}
 *
 *         {if !$shouldShow}
 *             {continue}
 *         {/if}
 *     {/if}
 *
 *     {* Render block normally *}
 *     {include ('file:modules/' ~ $block.MIGX_formname ~ '/' ~ $block.MIGX_formname ~ '.tpl') block=$block}
 * {/foreach}
 *
 * PARAMETERS:
 * @var string $testGroup - Required. The test group identifier (e.g., "homepage_hero")
 * @var string $variant - Required. The variant key for this block (e.g., "A", "B", "C")
 * @var int $resourceId - Optional. Resource ID for filtering tests
 * @var bool $debug - Optional. Enable debug mode (shows info to admins)
 *
 * @return string "1" if block should be shown, "0" if not
 *
 * @package blockab
 */

// Get parameters
$testGroup = $modx->getOption('testGroup', $scriptProperties, '');
$variant = $modx->getOption('variant', $scriptProperties, '');
$resourceId = $modx->getOption('resourceId', $scriptProperties, $modx->resource->get('id'));
$debug = (bool)$modx->getOption('debug', $scriptProperties, false);

// No test group = block is not part of a test, always show
if (empty($testGroup)) {
    return '1';
}

// Variant missing — misconfiguration, show the block but warn admins
if (empty($variant)) {
    if ($debug && $modx->hasPermission('view_unpublished')) {
        return '<div style="background: #ff6b6b; color: white; padding: 10px; margin: 10px 0; border-radius: 4px;">
            <strong>BlockAB:</strong> variant parameter ontbreekt voor test group "' . htmlspecialchars($testGroup) . '"
        </div>';
    }
    $modx->log(modX::LOG_LEVEL_WARN, '[BlockAB] variant parameter missing for testGroup: ' . $testGroup);
    return '1';
}

// Initialize BlockAB
$blockabPath = $modx->getOption('blockab.core_path', null,
    $modx->getOption('core_path') . 'components/blockab/');
$blockabModelPath = $blockabPath . 'model/';

if (!$modx->loadClass('blockab', $blockabModelPath . 'blockab/', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[BlockAB] Could not load BlockAB class');
    return '0';
}

$blockab = new BlockAB($modx);

// Check if block should be shown
$shouldShow = $blockab->shouldShowBlock($testGroup, $variant, $resourceId);

// Debug output for admins
if ($debug && $modx->hasPermission('view_unpublished')) {
    $details = $blockab->lastPickDetails;
    $debugInfo = '<div style="background: #4a90e2; color: white; padding: 10px; margin: 10px 0; border-radius: 4px; font-size: 12px;">
        <strong>BlockAB Debug:</strong><br>
        Test Group: ' . htmlspecialchars($testGroup) . '<br>
        Variant: ' . htmlspecialchars($variant) . '<br>
        Should Show: ' . ($shouldShow ? 'YES' : 'NO') . '<br>';

    if (!empty($details)) {
        $debugInfo .= 'Picked Variant: ' . ($details['variation']['variant_key'] ?? 'none') . '<br>';
        $debugInfo .= 'Pick Mode: ' . ($details['mode'] ?? 'none') . '<br>';
        $debugInfo .= 'Test ID: ' . ($details['test'] ?? 'none') . '<br>';
    }

    $debugInfo .= '</div>';

    $modx->regClientHTMLBlock($debugInfo);
}

// Return "1" or "0" as string for use in Fenom conditionals
return $shouldShow ? '1' : '0';
