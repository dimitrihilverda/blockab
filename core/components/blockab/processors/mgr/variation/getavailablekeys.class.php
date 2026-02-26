<?php
/**
 * Get available variant keys for a test
 *
 * @package blockab
 * @subpackage processors
 */

class babVariationGetAvailableKeysProcessor extends modProcessor {
    public function process() {
        $testId = $this->getProperty('test');

        // Get default keys from system setting
        $defaultKeys = $this->modx->getOption('blockab.default_variant_keys', null, 'A,B,C,D,E,F,G,H,I,J');
        $allKeys = array_map('trim', explode(',', $defaultKeys));

        // Get already used keys for this test
        $usedKeys = array();
        if ($testId) {
            $variations = $this->modx->getCollection('babVariation', array('test' => $testId));
            foreach ($variations as $variation) {
                $usedKeys[] = $variation->get('variant_key');
            }
        }

        // Filter out used keys
        $availableKeys = array_diff($allKeys, $usedKeys);

        // Format for combobox
        $results = array();
        foreach ($availableKeys as $key) {
            $results[] = array(
                'key' => $key,
                'display' => $key
            );
        }

        return $this->outputArray($results);
    }
}

return 'babVariationGetAvailableKeysProcessor';
