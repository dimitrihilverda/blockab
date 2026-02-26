<?php
/**
 * Get list of tests
 *
 * @package blockab
 * @subpackage processors
 */

class babTestGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'babTest';
    public $languageTopics = array('blockab:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'DESC';
    public $objectType = 'blockab.test';

    /**
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $query = $this->getProperty('query');
        if (!empty($query)) {
            $c->where(array(
                'name:LIKE' => '%' . $query . '%',
                'OR:test_group:LIKE' => '%' . $query . '%',
                'OR:description:LIKE' => '%' . $query . '%',
            ));
        }

        $archived = $this->getProperty('archived');
        if ($archived !== null) {
            $c->where(array('archived' => $archived));
        }

        return $c;
    }

    /**
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $array = $object->toArray();

        // Get variation count
        $array['variation_count'] = $this->modx->getCount('babVariation', array('test' => $object->get('id')));

        // Get stats
        $picks = $this->modx->blockab->getSum('babPick', array('test' => $object->get('id')));
        $conversions = $this->modx->blockab->getSum('babConversion', array('test' => $object->get('id')));

        $array['picks'] = $picks;
        $array['conversions'] = $conversions;
        $array['conversion_rate'] = ($picks > 0) ? round(($conversions / $picks) * 100, 2) : 0;

        return $array;
    }
}

return 'babTestGetListProcessor';
