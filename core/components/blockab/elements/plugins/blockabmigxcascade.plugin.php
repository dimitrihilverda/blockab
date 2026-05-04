<?php
/**
 * BlockAB MIGX Cascade Plugin
 *
 * Manager side (OnDocFormPrerender):
 *   - loads cascade JS + preview-button JS + lexicon strings
 * Web side (OnLoadWebDocument):
 *   - if a preview override param (?ab_<group>=<variant>) is present AND
 *     the visitor has view_unpublished, mark the resource as non-cacheable
 *     so the manager always sees a fresh render of the chosen variant
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

    case 'OnLoadWebDocument':
        // Detect any ab_<group>= preview override in the query string
        $hasOverride = false;
        foreach ($_GET as $key => $value) {
            if (strpos($key, 'ab_') === 0 && $value !== '') {
                $hasOverride = true;
                break;
            }
        }
        if ($hasOverride && $modx->hasPermission('view_unpublished')) {
            if (isset($modx->resource) && is_object($modx->resource)) {
                // Skip the page cache for this request — manager preview
                // must render the chosen variant fresh, not whatever
                // happened to be cached earlier.
                $modx->resource->_cacheable = false;
                if (method_exists($modx->resource, 'set')) {
                    $modx->resource->set('cacheable', false);
                }
            }
        }
        break;
}
return '';
