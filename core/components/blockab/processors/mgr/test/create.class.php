<?php
/**
 * Create a test
 *
 * @package blockab
 * @subpackage processors
 */

class babTestCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'babTest';
    public $languageTopics = array('blockab:default');
    public $objectType = 'blockab.test';

    /**
     * @return bool
     */
    public function beforeSave() {
        $name = $this->getProperty('name');
        $testGroup = $this->getProperty('test_group');

        if (empty($name)) {
            $this->addFieldError('name', $this->modx->lexicon('blockab.error.test_name_required'));
        }

        if (empty($testGroup)) {
            $this->addFieldError('test_group', $this->modx->lexicon('blockab.error.missing_testgroup'));
        }

        // Set created_at
        $this->object->set('created_at', date('Y-m-d H:i:s'));

        return parent::beforeSave();
    }
}

return 'babTestCreateProcessor';
