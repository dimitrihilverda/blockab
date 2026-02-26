<?php
/**
 * Get list of variations
 *
 * @package blockab
 * @subpackage processors
 */

class babVariationGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'babVariation';
    public $languageTopics = array('blockab:default');
    public $defaultSortField = 'variant_key';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'blockab.variation';

    /**
     * @param xPDOQuery $c
     * @return xPDOQuery
     */
    public function prepareQueryBeforeCount(xPDOQuery $c) {
        $testId = $this->getProperty('test');
        if ($testId) {
            $c->where(array('test' => $testId));
        }

        return $c;
    }

    /**
     * @param xPDOObject $object
     * @return array
     */
    public function prepareRow(xPDOObject $object) {
        $array = $object->toArray();

        // Get stats
        $picks = $this->modx->blockab->getSum('babPick', array(
            'test' => $object->get('test'),
            'variation' => $object->get('id')
        ));

        $conversions = $this->modx->blockab->getSum('babConversion', array(
            'test' => $object->get('test'),
            'variation' => $object->get('id')
        ));

        $array['picks'] = $picks;
        $array['conversions'] = $conversions;
        $array['conversion_rate'] = ($picks > 0) ? round(($conversions / $picks) * 100, 2) : 0;

        return $array;
    }
}

return 'babVariationGetListProcessor';
