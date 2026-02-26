<?php
/**
 * System settings for BlockAB
 *
 * @package blockab
 * @subpackage build
 */

$settings = array();

$settings['blockab.core_path'] = $modx->newObject('modSystemSetting');
$settings['blockab.core_path']->fromArray(array(
    'key' => 'blockab.core_path',
    'value' => '{core_path}components/blockab/',
    'xtype' => 'textfield',
    'namespace' => 'blockab',
    'area' => 'Paths',
    'editedon' => time(),
), '', true, true);

$settings['blockab.assets_path'] = $modx->newObject('modSystemSetting');
$settings['blockab.assets_path']->fromArray(array(
    'key' => 'blockab.assets_path',
    'value' => '{assets_path}components/blockab/',
    'xtype' => 'textfield',
    'namespace' => 'blockab',
    'area' => 'Paths',
    'editedon' => time(),
), '', true, true);

$settings['blockab.assets_url'] = $modx->newObject('modSystemSetting');
$settings['blockab.assets_url']->fromArray(array(
    'key' => 'blockab.assets_url',
    'value' => '{assets_url}components/blockab/',
    'xtype' => 'textfield',
    'namespace' => 'blockab',
    'area' => 'Paths',
    'editedon' => time(),
), '', true, true);

$settings['blockab.use_previous_picks'] = $modx->newObject('modSystemSetting');
$settings['blockab.use_previous_picks']->fromArray(array(
    'key' => 'blockab.use_previous_picks',
    'value' => true,
    'xtype' => 'combo-boolean',
    'namespace' => 'blockab',
    'area' => 'General',
    'editedon' => time(),
), '', true, true);

return $settings;
