<?php
/**
 * BlockAB Conversion Snippet
 *
 * Registers a conversion for A/B tests
 *
 * USAGE:
 * Place this snippet on your "thank you" or success page:
 *
 * [[!BlockABConversion]]
 *
 * Or specify specific test IDs:
 * [[!BlockABConversion? &tests=`1,2,3`]]
 *
 * Or use "*" to convert all visited tests:
 * [[!BlockABConversion? &tests=`*`]]
 *
 * Enable debug output (only visible for managers):
 * [[!BlockABConversion? &debug=`1`]]
 *
 * PARAMETERS:
 * @var string $tests - Test IDs to register conversion for. Use "*" for all visited tests, or comma-separated IDs
 * @var bool $debug   - Show debug output (only visible to logged-in managers)
 * @var bool $redirect - Redirect after registration (default: false)
 * @var string $redirectTo - Resource ID to redirect to (if redirect is true)
 *
 * @package blockab
 */

// Get parameters
$tests      = $modx->getOption('tests', $scriptProperties, '*');
$debug      = (bool)$modx->getOption('debug', $scriptProperties, false);
$redirect   = (bool)$modx->getOption('redirect', $scriptProperties, false);
$redirectTo = $modx->getOption('redirectTo', $scriptProperties, '');

// Initialize BlockAB
$blockabPath      = $modx->getOption('blockab.core_path', null,
    $modx->getOption('core_path') . 'components/blockab/');
$blockabModelPath = $blockabPath . 'model/';

if (!$modx->loadClass('blockab', $blockabModelPath . 'blockab/', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, '[BlockAB] Could not load BlockAB class');
    return '';
}

$blockab = new BlockAB($modx);

// Register conversion — always returns debug info
$info = $blockab->registerConversion($tests);

// Show debug output for managers only
if ($debug && $modx->hasPermission('view_unpublished')) {
    $sessionDump = '';
    if (!empty($info['session_data'])) {
        $rows = array();
        foreach ($info['session_data'] as $testId => $variationId) {
            $rows[] = 'test ' . (int)$testId . ' → variation ' . (int)$variationId;
        }
        $sessionDump = implode('<br>', $rows);
    } else {
        $sessionDump = '<em>(leeg)</em>';
    }

    $logLines = '';
    foreach ($info['log'] as $line) {
        $color = (strpos($line, 'OK') === 0) ? '#28a745' : ((strpos($line, 'FOUT') === 0) ? '#dc3545' : '#856404');
        $logLines .= '<div style="color:' . $color . ';margin:2px 0;">' . htmlspecialchars($line) . '</div>';
    }
    if (!$logLines) {
        $logLines = '<em>(geen log regels)</em>';
    }

    $statusColor  = $info['conversions_saved'] > 0 ? '#155724' : '#856404';
    $statusBg     = $info['conversions_saved'] > 0 ? '#d4edda' : '#fff3cd';
    $statusBorder = $info['conversions_saved'] > 0 ? '#c3e6cb' : '#ffeeba';
    $statusText   = $info['conversions_saved'] > 0
        ? '✓ ' . $info['conversions_saved'] . ' conversie(s) opgeslagen'
        : '⚠ Geen conversies opgeslagen';

    $output = '<div style="background:' . $statusBg . ';border:1px solid ' . $statusBorder . ';color:' . $statusColor . ';padding:12px 16px;margin:10px 0;border-radius:4px;font-size:13px;font-family:monospace;">'
        . '<strong>BlockAB Conversion Debug</strong><br><br>'
        . '<strong>Status:</strong> ' . $statusText . '<br><br>'
        . '<strong>Sessie picks:</strong><br>' . $sessionDump . '<br><br>'
        . '<strong>Log:</strong><br>' . $logLines
        . '</div>';

    $modx->regClientHTMLBlock($output);
}

// Optional redirect
if ($redirect && !empty($redirectTo)) {
    $url = $modx->makeUrl($redirectTo, '', '', 'full');
    $modx->sendRedirect($url);
}

return '';
