<?php
/**
 * BlockAB MIGX Cascade Plugin
 *
 * Loads the variant cascade dropdown JS and the preview-button JS
 * on the resource edit page (event: OnDocFormPrerender).
 *
 * @package blockab
 */
switch ($modx->event->name) {
    case 'OnDocFormPrerender':
        $assetsUrl = $modx->getOption('blockab.assets_url', null,
            $modx->getOption('assets_url') . 'components/blockab/');
        $modx->regClientStartupScript($assetsUrl . 'js/mgr/migx-cascade.js');
        $modx->regClientStartupScript($assetsUrl . 'js/mgr/preview-button.js');
        break;
}
return '';
