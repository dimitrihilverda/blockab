<?php
/**
 * BlockAB Table Installer
 *
 * Creates the BlockAB database tables via xPDO.
 * Alternative to running install.sql manually.
 *
 * USAGE (from _build directory):
 *   php install.tables.php /path/to/modx
 *
 * EXAMPLE:
 *   php install.tables.php /var/www/html
 *
 * @package blockab
 */

if (PHP_SAPI !== 'cli') {
    die('Run this script from the command line.');
}

if (empty($argv[1])) {
    echo "Usage: php install.tables.php /path/to/modx\n";
    exit(1);
}

$modxBase = rtrim($argv[1], '/\\') . '/';

// Detect config.core.php location (public_html or root)
if (file_exists($modxBase . 'public_html/config.core.php')) {
    $configCore = $modxBase . 'public_html/config.core.php';
} elseif (file_exists($modxBase . 'config.core.php')) {
    $configCore = $modxBase . 'config.core.php';
} else {
    echo "Error: config.core.php not found under '{$modxBase}'.\n";
    exit(1);
}

require_once $configCore;
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

echo "BlockAB Table Installer\n";
echo "=======================\n\n";

$modelPath = MODX_CORE_PATH . 'components/blockab/model/';
$modx->addPackage('blockab', $modelPath);

$manager = $modx->getManager();
$tables  = ['babTest', 'babVariation', 'babPick', 'babConversion'];

foreach ($tables as $table) {
    echo "Creating table for $table... ";
    if ($manager->createObjectContainer($table)) {
        echo "OK\n";
    } else {
        echo "skipped (already exists or error)\n";
    }
}

echo "\nDone!\n";
