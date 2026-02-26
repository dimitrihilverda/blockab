<?php
/**
 * Update a variation
 *
 * @package blockab
 * @subpackage processors
 */

class babVariationUpdateProcessor extends modObjectUpdateProcessor {
    public $classKey = 'babVariation';
    public $languageTopics = array('blockab:default');
    public $objectType = 'blockab.variation';

    /**
     * @return bool
     */
    public function beforeSave() {
        $name = $this->getProperty('name');
        $variantKey = $this->getProperty('variant_key');

        if (empty($name)) {
            $this->addFieldError('name', $this->modx->lexicon('blockab.error.variation_name_required'));
        }

        if (empty($variantKey)) {
            $this->addFieldError('variant_key', $this->modx->lexicon('blockab.error.missing_variant_key'));
        }

        // Check if variant_key already exists for this test (excluding current variation)
        $exists = $this->modx->getObject('babVariation', array(
            'test' => $this->object->get('test'),
            'variant_key' => $variantKey,
            'id:!=' => $this->object->get('id')
        ));

        if ($exists) {
            $this->addFieldError('variant_key', $this->modx->lexicon('blockab.error.variant_key_exists'));
        }

        return parent::beforeSave();
    }
}

return 'babVariationUpdateProcessor';
