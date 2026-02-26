<?php
/**
 * Get a test
 *
 * @package blockab
 * @subpackage processors
 */

class babTestGetProcessor extends modObjectGetProcessor {
    public $classKey = 'babTest';
    public $languageTopics = array('blockab:default');
    public $objectType = 'blockab.test';
}

return 'babTestGetProcessor';
