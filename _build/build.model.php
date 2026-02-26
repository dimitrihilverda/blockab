<?php
/**
 * Build xPDO Model from Schema
 *
 * @package blockab
 * @subpackage build
 */

// Define absolute path to MODX
define('MODX_BASE_PATH', 'C:/Projecten/moving-in.nl/');

require_once MODX_BASE_PATH . 'public_html/config.core.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget('ECHO');

echo '<pre>';
flush();

$modx->log(modX::LOG_LEVEL_INFO, 'Building BlockAB model from schema...');

$sources = array(
    'root' => dirname(dirname(__FILE__)) . '/',
    'core' => dirname(dirname(__FILE__)) . '/core/components/blockab/',
    'model' => dirname(dirname(__FILE__)) . '/core/components/blockab/model/',
    'schema' => dirname(dirname(__FILE__)) . '/core/components/blockab/model/schema/',
);

$manager = $modx->getManager();
$generator = $manager->getGenerator();

$generator->parseSchema(
    $sources['schema'] . 'blockab.mysql.schema.xml',
    $sources['model']
);

$modx->log(modX::LOG_LEVEL_INFO, 'Model classes generated successfully!');
$modx->log(modX::LOG_LEVEL_INFO, 'Location: ' . $sources['model'] . 'blockab/');

echo '</pre>';
