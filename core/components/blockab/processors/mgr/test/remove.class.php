<?php
/**
 * Remove a test
 *
 * @package blockab
 * @subpackage processors
 */

class babTestRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'babTest';
    public $languageTopics = array('blockab:default');
    public $objectType = 'blockab.test';

    /**
     * @return bool
     */
    public function beforeRemove() {
        // Remove all variations
        $variations = $this->modx->getCollection('babVariation', array('test' => $this->object->get('id')));
        foreach ($variations as $variation) {
            $variation->remove();
        }

        return parent::beforeRemove();
    }
}

return 'babTestRemoveProcessor';
