<?php
/**
 * Get all test groups for MIGX dropdown
 *
 * @package blockab
 * @subpackage processors
 */

class babTestGetTestGroupsProcessor extends modProcessor {
    public function process() {
        // Get all active, non-archived tests
        $tests = $this->modx->getCollection('babTest', array(
            'active' => 1,
            'archived' => 0
        ));

        $results = array();
        foreach ($tests as $test) {
            $results[] = array(
                'test_group' => $test->get('test_group'),
                'name' => $test->get('name') . ' (' . $test->get('test_group') . ')'
            );
        }

        return $this->outputArray($results);
    }
}

return 'babTestGetTestGroupsProcessor';
