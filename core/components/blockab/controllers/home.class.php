<?php
/**
 * BlockAB Home Controller
 *
 * @package blockab
 * @subpackage controllers
 */

class BlockABHomeManagerController extends modExtraManagerController {
    /** @var BlockAB */
    public $blockab;

    /**
     * @return void
     */
    public function initialize() {
        // Load BlockAB class
        $blockabPath = $this->modx->getOption('blockab.core_path', null,
            $this->modx->getOption('core_path') . 'components/blockab/');
        require_once $blockabPath . 'model/blockab/blockab.class.php';

        $this->blockab = new BlockAB($this->modx);
        $this->addCss($this->blockab->config['cssUrl'] . 'mgr.css');
        $this->addJavascript($this->blockab->config['jsUrl'] . 'mgr/blockab.js');
        $this->addHtml('<script type="text/javascript">
            Ext.onReady(function() {
                BlockAB.config = ' . $this->modx->toJSON($this->blockab->config) . ';
                BlockAB.config.connector_url = "' . $this->blockab->config['connectorUrl'] . '";
            });
        </script>');
    }

    /**
     * @return array
     */
    public function getLanguageTopics() {
        return array('blockab:default');
    }

    /**
     * @return bool
     */
    public function checkPermissions() {
        return $this->modx->hasPermission('view_component');
    }

    /**
     * @return string
     */
    public function getPageTitle() {
        return $this->modx->lexicon('blockab');
    }

    /**
     * @return void
     */
    public function loadCustomCssJs() {
        $this->addJavascript($this->blockab->config['jsUrl'] . 'mgr/widgets/tests.grid.js');
        $this->addJavascript($this->blockab->config['jsUrl'] . 'mgr/widgets/tests.windows.js');
        $this->addJavascript($this->blockab->config['jsUrl'] . 'mgr/widgets/home.panel.js');
        $this->addLastJavascript($this->blockab->config['jsUrl'] . 'mgr/sections/home.js');
    }

    /**
     * @return string
     */
    public function getTemplateFile() {
        return $this->blockab->config['templatesPath'] . 'home.tpl';
    }
}
