<?php
/**
 * Create a variation
 *
 * @package blockab
 * @subpackage processors
 */

class babVariationCreateProcessor extends modObjectCreateProcessor {
    public $classKey = 'babVariation';
    public $languageTopics = array('blockab:default');
    public $objectType = 'blockab.variation';

    /**
     * @return bool
     */
    public function beforeSave() {
        $name = $this->getProperty('name');
        $variantKey = $this->getProperty('variant_key');
        $test = $this->getProperty('test');

        if (empty($name)) {
            $this->addFieldError('name', $this->modx->lexicon('blockab.error.variation_name_required'));
        }

        if (empty($variantKey)) {
            $this->addFieldError('variant_key', $this->modx->lexicon('blockab.error.missing_variant_key'));
        }

        if (empty($test)) {
            $this->addFieldError('test', $this->modx->lexicon('blockab.error.test_required'));
        }

        // Check if variant_key already exists for this test
        $exists = $this->modx->getObject('babVariation', array(
            'test' => $test,
            'variant_key' => $variantKey
        ));

        if ($exists) {
            $this->addFieldError('variant_key', $this->modx->lexicon('blockab.error.variant_key_exists'));
        }

        // Set created_at
        $this->object->set('created_at', date('Y-m-d H:i:s'));

        return parent::beforeSave();
    }
}

return 'babVariationCreateProcessor';
