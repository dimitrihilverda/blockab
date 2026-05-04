<?php
/**
 * Get variants per test group for the manager preview button.
 *
 * Input:
 *   - groups: comma-separated list of test_group keys
 *
 * Output:
 *   {
 *     "success": true,
 *     "object": {
 *       "groups": {
 *         "homepage_hero": {
 *           "name": "Homepage Hero Test",
 *           "variants": [
 *             {"key": "A", "name": "Control"},
 *             {"key": "B", "name": "Treatment"}
 *           ]
 *         },
 *         "header": {...}
 *       }
 *     }
 *   }
 *
 * Only returns variants from active, non-archived tests.
 *
 * @package blockab
 * @subpackage processors
 */
class babTestGetVariantsForPreviewProcessor extends modProcessor {

    public function checkPermissions() {
        return $this->modx->hasPermission('view_unpublished');
    }

    public function getLanguageTopics() {
        return array('blockab:default');
    }

    public function process() {
        $groupsCsv = (string)$this->getProperty('groups', '');
        $groups = array_filter(array_map('trim', explode(',', $groupsCsv)));

        $result = array();
        foreach ($groups as $group) {
            $result[$group] = array(
                'name'     => $group, // fallback to the key if no test found
                'variants' => array(),
            );
            $test = $this->modx->getObject('babTest', array(
                'test_group' => $group,
                'active'     => 1,
                'archived'   => 0,
            ));
            if (!$test) {
                continue;
            }
            $testName = $test->get('name');
            if (!empty($testName)) {
                $result[$group]['name'] = $testName;
            }
            $variations = $this->modx->getCollection('babVariation', array(
                'test'   => $test->get('id'),
                'active' => 1,
            ));
            foreach ($variations as $v) {
                $result[$group]['variants'][] = array(
                    'key'  => $v->get('variant_key'),
                    'name' => $v->get('name'),
                );
            }
        }

        return $this->success('', array('groups' => $result));
    }
}

return 'babTestGetVariantsForPreviewProcessor';
