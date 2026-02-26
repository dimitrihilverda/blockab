<?php
/**
 * BlockAB Connector
 *
 * @package blockab
 */

// Find config.core.php
if (file_exists(dirname(__FILE__, 4) . '/config.core.php')) {
    require_once dirname(__FILE__, 4) . '/config.core.php';
} else {
    require_once dirname(__FILE__, 5) . '/config.core.php';
}
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('blockab.core_path', null, $modx->getOption('core_path') . 'components/blockab/');
require_once $corePath . 'model/blockab/blockab.class.php';
$modx->blockab = new BlockAB($modx);

$modx->lexicon->load('blockab:default');

/* Handle request */
$modx->request->handleRequest(array(
    'processors_path' => $corePath . 'processors/',
    'location' => '',
));
