<?php
/**
 * Get variations by test group for MIGX dropdown
 *
 * @package blockab
 * @subpackage processors
 */

class babVariationGetByTestGroupProcessor extends modProcessor {
    public function process() {
        $testGroup = $this->getProperty('test_group');

        if (empty($testGroup)) {
            return $this->outputArray(array());
        }

        // Get the test
        $test = $this->modx->getObject('babTest', array(
            'test_group' => $testGroup,
            'active' => 1,
            'archived' => 0
        ));

        if (!$test) {
            return $this->outputArray(array());
        }

        // Get active variations for this test
        $variations = $this->modx->getCollection('babVariation', array(
            'test' => $test->get('id'),
            'active' => 1
        ));

        $results = array();
        foreach ($variations as $variation) {
            $results[] = array(
                'variant_key' => $variation->get('variant_key'),
                'name' => $variation->get('variant_key') . ' - ' . $variation->get('name')
            );
        }

        return $this->outputArray($results);
    }
}

return 'babVariationGetByTestGroupProcessor';
