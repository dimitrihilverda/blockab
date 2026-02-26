<?php
/**
 * Duplicate a test with all its variations
 *
 * @package blockab
 * @subpackage processors
 */

class babTestDuplicateProcessor extends modProcessor {
    public function process() {
        $id = $this->getProperty('id');
        $newName = $this->getProperty('name');
        $newTestGroup = $this->getProperty('test_group');

        if (empty($id)) {
            return $this->failure($this->modx->lexicon('blockab.error.test_not_found'));
        }

        if (empty($newName)) {
            return $this->failure($this->modx->lexicon('blockab.error.test_name_required'));
        }

        if (empty($newTestGroup)) {
            return $this->failure($this->modx->lexicon('blockab.error.missing_testgroup'));
        }

        // Check if test_group already exists
        $existingTest = $this->modx->getObject('babTest', array('test_group' => $newTestGroup));
        if ($existingTest) {
            return $this->failure($this->modx->lexicon('blockab.error.test_group_exists'));
        }

        // Get the original test
        $originalTest = $this->modx->getObject('babTest', $id);
        if (!$originalTest) {
            return $this->failure($this->modx->lexicon('blockab.error.test_not_found'));
        }

        // Create new test
        $newTest = $this->modx->newObject('babTest');
        $newTest->set('name', $newName);
        $newTest->set('test_group', $newTestGroup);
        $newTest->set('description', $originalTest->get('description'));
        $newTest->set('status', $originalTest->get('status'));
        $newTest->set('context_key', $originalTest->get('context_key'));
        $newTest->set('created_at', date('Y-m-d H:i:s'));

        if (!$newTest->save()) {
            return $this->failure($this->modx->lexicon('blockab.error.test_save_failed'));
        }

        // Duplicate all variations
        $variations = $this->modx->getCollection('babVariation', array('test' => $id));
        foreach ($variations as $variation) {
            $newVariation = $this->modx->newObject('babVariation');
            $newVariation->set('test', $newTest->get('id'));
            $newVariation->set('variant_key', $variation->get('variant_key'));
            $newVariation->set('name', $variation->get('name'));
            $newVariation->set('description', $variation->get('description'));
            $newVariation->set('chunk_id', $variation->get('chunk_id'));
            $newVariation->set('weight', $variation->get('weight'));
            $newVariation->set('active', $variation->get('active'));
            $newVariation->set('created_at', date('Y-m-d H:i:s'));
            $newVariation->save();
        }

        return $this->success('', $newTest->toArray());
    }
}

return 'babTestDuplicateProcessor';
