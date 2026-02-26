<?php
/**
 * BlockAB Transport Package Build Script
 *
 * @package blockab
 * @subpackage build
 */

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tstart = $mtime;
set_time_limit(0);

/* Define package */
define('PKG_NAME', 'BlockAB');
define('PKG_NAME_LOWER', 'blockab');
define('PKG_VERSION', '1.0.0');
define('PKG_RELEASE', 'beta1');

/* Define paths */
$root = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'source_assets' => $root . 'assets/components/' . PKG_NAME_LOWER,
    'source_core' => $root . 'core/components/' . PKG_NAME_LOWER,
    'docs' => $root . 'core/components/' . PKG_NAME_LOWER . '/docs/',
    'lexicon' => $root . 'core/components/' . PKG_NAME_LOWER . '/lexicon/',
    'model' => $root . 'core/components/' . PKG_NAME_LOWER . '/model/',
);
unset($root);

/* Instantiate MODx */
if (PHP_SAPI !== 'cli' || empty($argv[1])) {
    die("Usage: php build.transport.php /path/to/modx\n");
}
$modxBase = rtrim($argv[1], '/\\') . '/';
if (!is_dir($modxBase . 'core/')) {
    die("Error: '{$modxBase}core/' not found. Check the path.\n");
}
$configCore = file_exists($modxBase . 'public_html/config.core.php')
    ? $modxBase . 'public_html/config.core.php'
    : $modxBase . 'config.core.php';
if (!file_exists($configCore)) {
    die("Error: config.core.php not found under '{$modxBase}'.\n");
}
require_once $configCore;
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

echo '<pre>';
flush();

$modx->log(modX::LOG_LEVEL_INFO, 'Packaging ' . PKG_NAME . '...');

/* Create the package builder */
$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_NAME_LOWER, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_NAME_LOWER, false, true, '{core_path}components/' . PKG_NAME_LOWER . '/');

$modx->log(modX::LOG_LEVEL_INFO, 'Created Transport Package and Namespace.');

/* Create category */
$category = $modx->newObject('modCategory');
$category->set('id', 1);
$category->set('category', PKG_NAME);

$modx->log(modX::LOG_LEVEL_INFO, 'Created category.');

/* Add snippets */
$snippets = array();

$snippets[0] = $modx->newObject('modSnippet');
$snippets[0]->fromArray(array(
    'id' => 1,
    'name' => 'BlockAB',
    'description' => 'Determines if a MIGX block should be shown based on A/B testing',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/snippets/blockab.snippet.php'),
), '', true, true);

$snippets[1] = $modx->newObject('modSnippet');
$snippets[1]->fromArray(array(
    'id' => 2,
    'name' => 'BlockABConversion',
    'description' => 'Registers a conversion for A/B tests',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/snippets/blockab.conversion.snippet.php'),
), '', true, true);

$snippets[2] = $modx->newObject('modSnippet');
$snippets[2]->fromArray(array(
    'id' => 3,
    'name' => 'BlockABGetTestGroups',
    'description' => 'Returns available test groups for use in MIGX dropdowns',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/snippets/blockab.gettestgroups.snippet.php'),
), '', true, true);

$snippets[3] = $modx->newObject('modSnippet');
$snippets[3]->fromArray(array(
    'id' => 4,
    'name' => 'BlockABGetVariants',
    'description' => 'Returns available variants for a specific test group for use in MIGX dropdowns',
    'snippet' => file_get_contents($sources['source_core'] . '/elements/snippets/blockab.getvariants.snippet.php'),
), '', true, true);

if (count($snippets) > 0) {
    $category->addMany($snippets);
    $modx->log(modX::LOG_LEVEL_INFO, 'Added ' . count($snippets) . ' snippets.');
}

/* Create category vehicle */
$attr = array(
    xPDOTransport::UNIQUE_KEY => 'category',
    xPDOTransport::PRESERVE_KEYS => false,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'Snippets' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => 'name',
        ),
    ),
);

$vehicle = $builder->createVehicle($category, $attr);

/* Add file resolvers */
$vehicle->resolve('file', array(
    'source' => $sources['source_core'],
    'target' => "return MODX_CORE_PATH . 'components/';",
));

$vehicle->resolve('file', array(
    'source' => $sources['source_assets'],
    'target' => "return MODX_ASSETS_PATH . 'components/';",
));

/* Add resolver for tables */
$vehicle->resolve('php', array(
    'source' => $sources['resolvers'] . 'resolve.tables.php',
));

$builder->putVehicle($vehicle);

$modx->log(modX::LOG_LEVEL_INFO, 'Added category vehicle with file resolvers.');

/* Load system settings */
$settings = include $sources['data'] . 'transport.settings.php';

if (!is_array($settings)) {
    $modx->log(modX::LOG_LEVEL_ERROR, 'Could not load system settings.');
} else {
    $attributes = array(
        xPDOTransport::UNIQUE_KEY => 'key',
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => false,
    );

    foreach ($settings as $setting) {
        $vehicle = $builder->createVehicle($setting, $attributes);
        $builder->putVehicle($vehicle);
    }

    $modx->log(modX::LOG_LEVEL_INFO, 'Added ' . count($settings) . ' system settings.');
}

/* Create menu */
$menu = $modx->newObject('modMenu');
$menu->fromArray(array(
    'text' => PKG_NAME_LOWER,
    'parent' => 'components',
    'description' => PKG_NAME_LOWER . '.menu_desc',
    'icon' => 'images/icons/plugin.gif',
    'menuindex' => 0,
    'params' => '',
    'handler' => '',
    'action' => 'home',
    'namespace' => PKG_NAME_LOWER,
), '', true, true);

$vehicle = $builder->createVehicle($menu, array(
    xPDOTransport::PRESERVE_KEYS => true,
    xPDOTransport::UPDATE_OBJECT => true,
    xPDOTransport::UNIQUE_KEY => 'text',
    xPDOTransport::RELATED_OBJECTS => true,
    xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array(
        'Action' => array(
            xPDOTransport::PRESERVE_KEYS => false,
            xPDOTransport::UPDATE_OBJECT => true,
            xPDOTransport::UNIQUE_KEY => array('namespace', 'controller'),
        ),
    ),
));

$modx->log(modX::LOG_LEVEL_INFO, 'Packaged in menu.');
$builder->putVehicle($vehicle);

/* Now pack in the license file, readme and setup options */
$builder->setPackageAttributes(array(
    'license' => file_get_contents($sources['docs'] . 'license.txt'),
    'readme' => file_get_contents($sources['docs'] . 'readme.txt'),
    'changelog' => file_get_contents($sources['docs'] . 'changelog.txt'),
));

$modx->log(modX::LOG_LEVEL_INFO, 'Added package attributes and setup options.');

/* Zip up the package */
$builder->pack();

$mtime = microtime();
$mtime = explode(" ", $mtime);
$mtime = $mtime[1] + $mtime[0];
$tend = $mtime;
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO, "\n<br />Package Built.<br />\nExecution time: {$totalTime}\n");

echo '</pre>';
