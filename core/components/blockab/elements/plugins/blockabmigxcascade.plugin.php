<?php
/**
 * BlockAB MIGX Cascade Plugin
 *
 * Loads the variant cascade dropdown JS, the preview-button JS, and
 * blockab lexicon strings on resource edit pages (OnDocFormPrerender).
 *
 * @package blockab
 */
switch ($modx->event->name) {
    case 'OnDocFormPrerender':
        $assetsUrl = $modx->getOption('blockab.assets_url', null,
            $modx->getOption('assets_url') . 'components/blockab/');

        // Expose blockab lexicon strings to manager JS so _('blockab.x') works
        $modx->lexicon->load('blockab:default');
        $entries = $modx->lexicon->fetch();
        $blockab = array();
        foreach ($entries as $k => $v) {
            if (strpos($k, 'blockab.') === 0) {
                $blockab[$k] = $v;
            }
        }
        if (!empty($blockab)) {
            $modx->regClientStartupHTMLBlock(
                '<script>Ext.onReady(function(){'
                . 'if(!MODx.lang)MODx.lang={};'
                . 'Ext.applyIf(MODx.lang,' . $modx->toJSON($blockab) . ');'
                . '});</script>'
            );
        }

        $modx->regClientStartupScript($assetsUrl . 'js/mgr/migx-cascade.js');
        $modx->regClientStartupScript($assetsUrl . 'js/mgr/preview-button.js');
        break;
}
return '';
