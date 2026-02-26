<?php
/**
 * BlockAB - A/B Testing for MIGX Blocks
 *
 * Copyright 2026 by Moving-in.nl
 *
 * BlockAB is free software; you can redistribute it and/or modify it
 * under the terms of the GNU General Public License as published by the Free
 * Software Foundation; either version 2 of the License, or (at your option) any
 * later version.
 *
 * @package blockab
 */

class BlockAB {
    /** @var modX */
    public $modx;

    /** @var array */
    public $config = array();

    /** @var bool */
    public $considerPreviousPicks = true;

    /** @var array */
    public $lastPickDetails = array();

    /** @var array In-memory cache for variations per test (avoids repeated DB queries within one page load) */
    protected $_variationsCache = array();

    /** @var array In-memory cache for active tests per test group */
    protected $_testCache = array();

    /** @var array */
    public $cacheOptions = array(
        xPDO::OPT_CACHE_KEY => 'blockab',
    );

    /** @var array */
    protected $_defaultSession = array(
        '_picked'    => array(),
        '_converted' => array(),
    );

    /**
     * Constructor
     *
     * @param modX $modx
     * @param array $config
     */
    public function __construct(modX &$modx, array $config = array()) {
        $this->modx =& $modx;

        $basePath = $this->modx->getOption('blockab.core_path', $config,
            $this->modx->getOption('core_path') . 'components/blockab/');
        $assetsUrl = $this->modx->getOption('blockab.assets_url', $config,
            $this->modx->getOption('assets_url') . 'components/blockab/');
        $assetsPath = $this->modx->getOption('blockab.assets_path', $config,
            $this->modx->getOption('assets_path') . 'components/blockab/');

        $this->config = array_merge(array(
            'basePath' => $basePath,
            'corePath' => $basePath,
            'modelPath' => $basePath . 'model/',
            'processorsPath' => $basePath . 'processors/',
            'elementsPath' => $basePath . 'elements/',
            'assetsPath' => $assetsPath,
            'assetsUrl' => $assetsUrl,
            'jsUrl' => $assetsUrl . 'js/',
            'cssUrl' => $assetsUrl . 'css/',
            'connectorUrl' => $assetsUrl . 'connector.php',
        ), $config);

        $this->modx->lexicon->load('blockab:default');

        $modelPath = $this->config['modelPath'];
        $this->modx->addPackage('blockab', $modelPath);

        $this->considerPreviousPicks = (bool)$this->modx->getOption('blockab.use_previous_picks', null, true);
    }

    /**
     * Get user session data
     *
     * @return array
     */
    public function getUserData() {
        if (isset($_SESSION['_blockab'])) {
            $data = $_SESSION['_blockab'];
        } else {
            $data = $_SESSION['_blockab'] = $this->_defaultSession;
        }
        return $data;
    }

    /**
     * Determine if a block should be shown based on A/B test
     *
     * @param string $testGroup - The test group identifier (e.g., "homepage_hero")
     * @param string $variantKey - The variant key for this block (e.g., "A", "B", "C")
     * @param int $resourceId - Optional resource ID for filtering
     * @return bool - True if this variant should be shown
     */
    public function shouldShowBlock($testGroup, $variantKey, $resourceId = null) {
        // Als geen test group, altijd tonen
        if (empty($testGroup)) {
            return true;
        }

        // Haal actieve test op voor deze group
        $test = $this->getActiveTest($testGroup, $resourceId);

        // Geen actieve test? Toon alles
        if (!$test) {
            return true;
        }

        // Haal varianten op
        $variations = $this->getVariationsForTest($test);

        // Geen varianten? Toon alles
        if (empty($variations)) {
            return true;
        }

        // Check of deze variant key bestaat in de test
        $variantExists = false;
        foreach ($variations as $variation) {
            if ($variation['variant_key'] === $variantKey) {
                $variantExists = true;
                break;
            }
        }

        // Variant bestaat niet in test? Toon het niet
        if (!$variantExists) {
            return false;
        }

        // Pick een variant (of gebruik eerder gepickte)
        $pickedVariation = $this->pickOne($test, $variations);

        // Return true als deze variant de gepickte is
        return ($pickedVariation && $pickedVariation['variant_key'] === $variantKey);
    }

    /**
     * Get active test for a test group
     *
     * @param string $testGroup
     * @param int $resourceId
     * @return babTest|null
     */
    public function getActiveTest($testGroup, $resourceId = null) {
        $cacheKey = $testGroup . ':' . (int)$resourceId;
        if (array_key_exists($cacheKey, $this->_testCache)) {
            return $this->_testCache[$cacheKey];
        }

        $where = array(
            'test_group' => $testGroup,
            'active' => 1,
            'archived' => 0,
        );

        $test = $this->modx->getObject('babTest', $where);

        if (!$test) {
            $this->_testCache[$cacheKey] = null;
            return null;
        }

        // Check resource filtering als opgegeven
        if ($resourceId) {
            $resources = $test->get('resources');
            if (!empty($resources)) {
                $allowedResources = $this->_parseResourceDefinition($resources);
                if (!in_array($resourceId, $allowedResources)) {
                    $this->_testCache[$cacheKey] = null;
                    return null;
                }
            }
        }

        $this->_testCache[$cacheKey] = $test;
        return $test;
    }

    /**
     * Get variations for a test with statistics
     *
     * @param babTest $test
     * @return array
     */
    public function getVariationsForTest(babTest $test) {
        $testId = $test->get('id');

        if (isset($this->_variationsCache[$testId])) {
            return $this->_variationsCache[$testId];
        }

        $variations = $this->modx->getCollection('babVariation', array(
            'test' => $testId,
            'active' => 1,
        ));

        $result = array();
        foreach ($variations as $variation) {
            $variationId = $variation->get('id');

            // Get statistics
            $picks = $this->getSum('babPick', array(
                'test' => $testId,
                'variation' => $variationId,
            ));

            $conversions = $this->getSum('babConversion', array(
                'test' => $testId,
                'variation' => $variationId,
            ));

            $conversionRate = ($picks > 0) ? ($conversions / $picks) * 100 : 0;

            $result[$variationId] = array(
                'id' => $variationId,
                'name' => $variation->get('name'),
                'variant_key' => $variation->get('variant_key'),
                'weight' => $variation->get('weight'),
                'picks' => $picks,
                'conversions' => $conversions,
                'conversionrate' => $conversionRate,
            );
        }

        $this->_variationsCache[$testId] = $result;
        return $result;
    }

    /**
     * Pick one variation to show
     *
     * @param babTest $test
     * @param array $variations
     * @return array|null
     */
    public function pickOne(babTest $test, array $variations) {
        $testId = $test->get('id');
        $userData = $this->getUserData();

        $theOne = false;
        $mode = '';

        $totalConversions = 0;
        foreach ($variations as $variation) {
            $totalConversions += $variation['conversions'];
        }

        // Check previous pick
        if ($this->considerPreviousPicks &&
            isset($userData['_picked'][$testId])) {
            $previousId = $userData['_picked'][$testId];

            if (isset($variations[$previousId])) {
                $mode = 'previous';
                $theOne = $previousId;
            }
        }

        // Pick new one if needed
        if (!$theOne) {
            $random = $this->pickOneRandomly($test, $totalConversions);

            if ($random) {
                // Random pick with weights
                $theOne = $this->pickRandomWeighted($variations);
                $mode = 'random';
            } else {
                // Best performing
                $highestRate = 0;
                $highestVariation = 0;

                foreach ($variations as $variationId => $variation) {
                    if ($variation['conversionrate'] > $highestRate) {
                        $highestRate = $variation['conversionrate'];
                        $highestVariation = $variationId;
                    }
                }

                $theOne = $highestVariation;
                $mode = 'bestpick';
            }

            if ($theOne) {
                $this->registerPick($testId, $theOne);
            }
        }

        $this->lastPickDetails = array(
            'test' => $testId,
            'mode' => $mode,
            'pick' => $theOne,
            'variation' => $variations[$theOne] ?? null,
            'variations' => $variations,
        );

        return $variations[$theOne] ?? null;
    }

    /**
     * Pick randomly based on weights
     *
     * @param array $variations
     * @return int|false
     */
    protected function pickRandomWeighted(array $variations) {
        $totalWeight = 0;
        foreach ($variations as $variation) {
            $totalWeight += $variation['weight'];
        }

        if ($totalWeight <= 0) {
            // No weights, pick truly random
            $keys = array_keys($variations);
            shuffle($keys);
            return reset($keys);
        }

        $random = rand(1, $totalWeight);
        $currentWeight = 0;

        foreach ($variations as $variationId => $variation) {
            $currentWeight += $variation['weight'];
            if ($random <= $currentWeight) {
                return $variationId;
            }
        }

        return false;
    }

    /**
     * Decide if we should pick randomly or use optimization
     *
     * @param babTest $test
     * @param int $conversions
     * @return bool
     */
    public function pickOneRandomly(babTest $test, $conversions) {
        if (!$test->get('smartoptimize')) {
            return true;
        }

        $random = ($conversions <= $test->get('threshold'));

        if (!$random) {
            $randomChance = rand(0, 100);
            if ($randomChance < $test->get('randomize')) {
                $random = true;
            }
        }

        return $random;
    }

    /**
     * Register that a variation was picked
     *
     * @param int $testId
     * @param int $variationId
     */
    public function registerPick($testId, $variationId) {
        // Set session
        if (!isset($_SESSION['_blockab'])) {
            $_SESSION['_blockab'] = $this->_defaultSession;
        }

        $_SESSION['_blockab']['_picked'][$testId] = $variationId;

        // Log to database
        $pick = $this->modx->getObject('babPick', array(
            'test' => $testId,
            'variation' => $variationId,
            'date' => date('Ymd'),
        ));

        if (!$pick) {
            $pick = $this->modx->newObject('babPick');
            $pick->fromArray(array(
                'test' => $testId,
                'variation' => $variationId,
                'date' => date('Ymd'),
                'amount' => 0,
            ), '', true);
        }

        $pick->set('amount', $pick->get('amount') + 1);

        if (!$pick->save()) {
            $this->modx->log(modX::LOG_LEVEL_ERROR, '[BlockAB] Could not save pick');
        }
    }

    /**
     * Register a conversion
     *
     * @param mixed $tests - Test ID, array of IDs, or "*" for all
     * @return array Debug info (session data, log, conversions saved)
     */
    public function registerConversion($tests) {
        $userData = $this->getUserData();
        $visitedTests = $userData['_picked'] ?? array();

        $convertedTests = $userData['_converted'] ?? array();

        $debug = array(
            'session_found' => !empty($visitedTests),
            'session_data'  => $visitedTests,
            'tests_param'   => $tests,
            'log'           => array(),
            'conversions_saved' => 0,
        );

        if (empty($visitedTests)) {
            $debug['log'][] = 'SKIP: geen sessie data gevonden in $_SESSION[\'_blockab\'][\'_picked\'] — bezoek eerst de A/B pagina in dezelfde browsersessie';
        }

        // "*" means all visited tests
        if (!is_array($tests) && ($tests === '*')) {
            $tests = array_keys($visitedTests);
            $debug['log'][] = 'tests=* → verwerkt test IDs uit sessie: ' . (empty($tests) ? '(leeg)' : implode(', ', $tests));
        }

        // Convert string to array
        if (!is_array($tests)) {
            $tests = array_filter(array_map('trim', explode(',', $tests)));
        }

        foreach ($tests as $testId) {
            $testId = (int)$testId;

            // Verify test exists
            $test = $this->modx->getObject('babTest', $testId);
            if (!$test) {
                $debug['log'][] = "SKIP test $testId: test niet gevonden in database";
                continue;
            }

            // Get variation user saw
            $variationId = isset($visitedTests[$testId]) ? (int)$visitedTests[$testId] : 0;

            if (!$variationId) {
                $debug['log'][] = "SKIP test $testId ({$test->get('name')}): geen sessie pick gevonden voor deze test";
                continue;
            }

            // Skip if already converted in this session
            if (isset($convertedTests[$testId])) {
                $debug['log'][] = "SKIP test $testId ({$test->get('name')}): conversie al geregistreerd in deze sessie";
                continue;
            }

            // Verify variation exists
            $variation = $this->modx->getObject('babVariation', array(
                'id' => $variationId,
                'test' => $testId,
            ));

            if (!$variation) {
                $debug['log'][] = "SKIP test $testId: variant ID $variationId niet gevonden";
                continue;
            }

            // Save conversion
            $conversion = $this->modx->getObject('babConversion', array(
                'test' => $testId,
                'variation' => $variationId,
                'date' => date('Ymd'),
            ));

            if (!$conversion) {
                $conversion = $this->modx->newObject('babConversion');
                $conversion->fromArray(array(
                    'test' => $testId,
                    'variation' => $variationId,
                    'date' => date('Ymd'),
                    'amount' => 0,
                ), '', true);
            }

            $conversion->set('amount', $conversion->get('amount') + 1);

            if ($conversion->save()) {
                // Mark as converted in session to prevent duplicates
                $_SESSION['_blockab']['_converted'][$testId] = true;

                $debug['log'][] = "OK test $testId ({$test->get('name')}): conversie opgeslagen voor variant {$variation->get('variant_key')} (variation ID $variationId)";
                $debug['conversions_saved']++;
            } else {
                $debug['log'][] = "FOUT test $testId: conversie opslaan mislukt";
                $this->modx->log(modX::LOG_LEVEL_ERROR, '[BlockAB] Could not save conversion');
            }
        }

        return $debug;
    }

    /**
     * Get sum of a field
     *
     * @param string $class
     * @param array $where
     * @param string $field
     * @return int
     */
    public function getSum($class, array $where = array(), $field = 'amount') {
        $c = $this->modx->newQuery($class);
        $c->select('SUM(' . $field . ') as cnt');
        $c->where($where);

        if ($c->prepare() && $c->stmt->execute()) {
            return (int)$c->stmt->fetchColumn();
        }

        return 0;
    }

    /**
     * Parse resource definition (supports ranges and children)
     *
     * @param string $resources
     * @return array
     */
    protected function _parseResourceDefinition($resources) {
        $return = array();
        $resources = explode(',', $resources);

        foreach ($resources as $def) {
            $def = trim($def);

            switch (true) {
                // 5> to use all children of resource 5
                case (substr($def, -1) === '>'):
                    $id = (int)substr($def, 0, -1);
                    $children = $this->modx->getChildIds($id);
                    $return = array_merge($return, array_values($children));
                    break;

                // 3-5 to use 3, 4 and 5
                case (strpos($def, '-', 1) > 0):
                    $pos = strpos($def, '-', 1);
                    $start = (int)substr($def, 0, $pos);
                    $end = (int)substr($def, $pos + 1);

                    if ($end < $start) {
                        $temp = $start;
                        $start = $end;
                        $end = $temp;
                    }

                    while ($start <= $end) {
                        $return[] = $start;
                        $start++;
                    }
                    break;

                default:
                    if (is_numeric($def)) {
                        $return[] = (int)$def;
                    }
            }
        }

        return array_unique($return);
    }

    /**
     * Get all test groups for dropdowns
     *
     * @param bool $includeInactive
     * @param bool $includeArchived
     * @param string $format - 'migx' or 'json'
     * @return string
     */
    public function getTestGroupsForDropdown($includeInactive = false, $includeArchived = false, $format = 'migx') {
        $where = array();
        if (!$includeInactive) {
            $where['active'] = 1;
        }
        if (!$includeArchived) {
            $where['archived'] = 0;
        }

        $tests = $this->modx->getCollection('babTest', $where);

        if (!$tests || count($tests) === 0) {
            return ($format === 'migx') ? '==-- No Tests Available --' : '[]';
        }

        $groups = array();
        foreach ($tests as $test) {
            $testGroup = $test->get('test_group');
            $name = $test->get('name');

            // Use test_group as key, name as display value
            if (!isset($groups[$testGroup])) {
                $groups[$testGroup] = $name;
            }
        }

        switch ($format) {
            case 'json':
                return json_encode($groups);

            case 'migx':
            default:
                // MIGX format: value==Display||value2==Display2
                $result = array('==-- Select Test Group --');

                foreach ($groups as $key => $value) {
                    $result[] = $value . '==' . $key;
                }

                return implode('||', $result);
        }
    }

    /**
     * Get variants for a test group for dropdowns
     *
     * @param string $testGroup
     * @param bool $includeInactive
     * @param string $format - 'simple' (A||B) or 'detailed' (A - Name||B - Name)
     * @return string
     */
    public function getVariantsForDropdown($testGroup, $includeInactive = false, $format = 'detailed') {
        if (empty($testGroup)) {
            return 'A||B||C||D||E';
        }

        $test = $this->modx->getObject('babTest', array(
            'test_group' => $testGroup
        ));

        if (!$test) {
            return 'A||B||C||D||E';
        }

        $where = array(
            'test' => $test->get('id')
        );

        if (!$includeInactive) {
            $where['active'] = 1;
        }

        $variations = $this->modx->getCollection('babVariation', $where);

        if (empty($variations)) {
            return 'A||B||C||D||E';
        }

        $result = array();

        foreach ($variations as $variation) {
            $key = $variation->get('variant_key');
            $name = $variation->get('name');

            if ($format === 'detailed') {
                $result[] = $key . '==' . $key . ' - ' . $name;
            } else {
                $result[] = $key;
            }
        }

        return implode('||', $result);
    }
}
