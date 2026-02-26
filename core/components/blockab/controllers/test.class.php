<?php
/**
 * BlockAB Test Detail Controller
 *
 * @package blockab
 * @subpackage controllers
 */

class BlockABTestManagerController extends modExtraManagerController {
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

        // Set test_id and test_name in config
        $testId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $this->blockab->config['test_id'] = $testId;
        $test = $this->modx->getObject('babTest', $testId);
        if ($test) {
            $this->blockab->config['test_name'] = $test->get('name');
            $this->blockab->config['test_group'] = $test->get('test_group');
        }
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
        $testId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
        $test = $this->modx->getObject('babTest', $testId);
        if ($test) {
            return $this->modx->lexicon('blockab') . ' - ' . $test->get('name');
        }
        return $this->modx->lexicon('blockab');
    }

    /**
     * @return void
     */
    public function loadCustomCssJs() {
        // Set config inline before widgets load
        $this->addHtml('<script type="text/javascript">BlockAB.config = ' . $this->modx->toJSON($this->blockab->config) . ';</script>');

        // Load widget files
        $this->addJavascript($this->blockab->config['jsUrl'] . 'mgr/widgets/variations.grid.js');
        $this->addJavascript($this->blockab->config['jsUrl'] . 'mgr/widgets/variations.windows.js');
        $this->addJavascript($this->blockab->config['jsUrl'] . 'mgr/widgets/stats.panel.js');
        $this->addJavascript($this->blockab->config['jsUrl'] . 'mgr/widgets/test.panel.js');

        // Load page in Ext.onReady
        $this->addHtml('<script type="text/javascript">
        Ext.onReady(function() {
            MODx.load({
                xtype: "blockab-panel-test",
                renderTo: "blockab-panel-test-div"
            });
        });
        </script>');
    }

    /**
     * @return string
     */
    public function getTemplateFile() {
        return $this->blockab->config['templatesPath'] . 'test.tpl';
    }
}
