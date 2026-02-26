<?php
/**
 * Remove a variation
 *
 * @package blockab
 * @subpackage processors
 */

class babVariationRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'babVariation';
    public $languageTopics = array('blockab:default');
    public $objectType = 'blockab.variation';
}

return 'babVariationRemoveProcessor';
