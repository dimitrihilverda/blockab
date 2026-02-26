<?php
/**
 * Duplicate a variation
 *
 * @package blockab
 * @subpackage processors
 */

class babVariationDuplicateProcessor extends modProcessor {
    public function process() {
        $id = $this->getProperty('id');
        $newName = $this->getProperty('name');
        $newVariantKey = $this->getProperty('variant_key');

        if (empty($id)) {
            return $this->failure($this->modx->lexicon('blockab.error.variation_not_found'));
        }

        if (empty($newName)) {
            return $this->failure($this->modx->lexicon('blockab.error.variation_name_required'));
        }

        if (empty($newVariantKey)) {
            return $this->failure($this->modx->lexicon('blockab.error.variation_key_required'));
        }

        // Get the original variation
        $originalVariation = $this->modx->getObject('babVariation', $id);
        if (!$originalVariation) {
            return $this->failure($this->modx->lexicon('blockab.error.variation_not_found'));
        }

        // Check if variant key already exists for this test
        $testId = $originalVariation->get('test');
        $existingVariation = $this->modx->getObject('babVariation', array(
            'test' => $testId,
            'variant_key' => $newVariantKey
        ));

        if ($existingVariation) {
            return $this->failure($this->modx->lexicon('blockab.error.variant_key_exists'));
        }

        // Create new variation
        $newVariation = $this->modx->newObject('babVariation');
        $newVariation->set('test', $testId);
        $newVariation->set('variant_key', $newVariantKey);
        $newVariation->set('name', $newName);
        $newVariation->set('description', $originalVariation->get('description'));
        $newVariation->set('chunk_id', $originalVariation->get('chunk_id'));
        $newVariation->set('weight', $originalVariation->get('weight'));
        $newVariation->set('active', $originalVariation->get('active'));
        $newVariation->set('created_at', date('Y-m-d H:i:s'));

        if (!$newVariation->save()) {
            return $this->failure($this->modx->lexicon('blockab.error.variation_save_failed'));
        }

        return $this->success('', $newVariation->toArray());
    }
}

return 'babVariationDuplicateProcessor';
