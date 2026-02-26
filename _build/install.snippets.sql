-- BlockAB Snippets Installation
-- Run this SQL to install/update the BlockAB snippets
-- Replace 'mdx_' with your actual MODX table prefix if different

-- Get the BlockAB category ID or create it
SET @category_id = (SELECT id FROM mdx_categories WHERE category = 'BlockAB' LIMIT 1);

-- If category doesn't exist, insert it
INSERT IGNORE INTO mdx_categories (category, parent, `rank`)
VALUES ('BlockAB', 0, 0);

SET @category_id = (SELECT id FROM mdx_categories WHERE category = 'BlockAB' LIMIT 1);

-- Install BlockABGetTestGroups snippet
INSERT INTO mdx_site_snippets (
    name,
    description,
    snippet,
    category
) VALUES (
    'BlockABGetTestGroups',
    'Returns available test groups for use in MIGX dropdowns',
    '/**
 * BlockAB Get Test Groups Snippet
 *
 * Returns available test groups for use in MIGX dropdowns
 * Returns format: group_key==Group Name||group_key2==Group Name 2
 *
 * USAGE IN MIGX:
 * In your MIGX field configuration, set:
 * - inputTVtype: "listbox"
 * - inputOptionValues: @EVAL return $modx->runSnippet(\'\'BlockABGetTestGroups\'\');
 *
 * PARAMETERS:
 * @var string $format - Output format: "migx" (default) or "json"
 * @var bool $includeInactive - Include inactive tests (default: false)
 * @var bool $includeArchived - Include archived tests (default: false)
 *
 * @return string Formatted list of test groups
 *
 * @package blockab
 */

// Get parameters
$format = $modx->getOption(\'\'format\'\', $scriptProperties, \'\'migx\'\');
$includeInactive = (bool)$modx->getOption(\'\'includeInactive\'\', $scriptProperties, false);
$includeArchived = (bool)$modx->getOption(\'\'includeArchived\'\', $scriptProperties, false);

// Initialize BlockAB
$blockabPath = $modx->getOption(\'\'blockab.core_path\'\', null,
    $modx->getOption(\'\'core_path\'\') . \'\'components/blockab/\'\');
$blockabModelPath = $blockabPath . \'\'model/\'\';

if (!$modx->loadClass(\'\'blockab\'\', $blockabModelPath . \'\'blockab/\'\', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, \'\'[BlockAB] Could not load BlockAB class\'\');
    return \'\'\'\';
}

// Build query criteria
$where = array();
if (!$includeInactive) {
    $where[\'\'active\'\'] = 1;
}
if (!$includeArchived) {
    $where[\'\'archived\'\'] = 0;
}

// Get unique test groups
$c = $modx->newQuery(\'\'babTest\'\');
$c->where($where);
$c->select(\'\'test_group, name\'\');
$c->sortby(\'\'name\'\', \'\'ASC\'\');
$tests = $modx->getCollection(\'\'babTest\'\', $c);

$groups = array();
foreach ($tests as $test) {
    $testGroup = $test->get(\'\'test_group\'\');
    $name = $test->get(\'\'name\'\');

    // Use test_group as key, name as display value
    if (!isset($groups[$testGroup])) {
        $groups[$testGroup] = $name;
    }
}

// Return empty option first
$result = array();

switch ($format) {
    case \'\'json\'\':
        return json_encode($groups);

    case \'\'migx\'\':
    default:
        // MIGX format: value==Display||value2==Display2
        // Add empty option
        $result[] = \'\'==-- Select Test Group --\'\';

        foreach ($groups as $key => $value) {
            $result[] = $key . \'\'==\'\' . $value;
        }

        return implode(\'\'||\'\', $result);
}',
    @category_id
) AS new_vals ON DUPLICATE KEY UPDATE
    description = new_vals.description,
    snippet = new_vals.snippet,
    category = new_vals.category;

-- Install BlockABGetVariants snippet
INSERT INTO mdx_site_snippets (
    name,
    description,
    snippet,
    category
) VALUES (
    'BlockABGetVariants',
    'Returns available variants for a specific test group for use in MIGX dropdowns',
    '/**
 * BlockAB Get Variants Snippet
 *
 * Returns available variants for a specific test group for use in MIGX dropdowns
 * Returns format: A||B||C or A==Variant A||B==Variant B
 *
 * USAGE IN MIGX:
 * In your MIGX field configuration, set:
 * - inputTVtype: "listbox"
 * - inputOptionValues: @EVAL return $modx->runSnippet(\'\'BlockABGetVariants\'\', array(\'\'testGroup\'\' => $scriptProperties[\'\'ab_test_group\'\']));
 *
 * Note: This requires the test group to be selected first. For a simpler setup,
 * just use a static list: A||B||C||D||E
 *
 * PARAMETERS:
 * @var string $testGroup - The test group to get variants for
 * @var string $format - Output format: "simple" (A||B||C) or "detailed" (A==Variant A||B==Variant B)
 * @var bool $includeInactive - Include inactive variations (default: false)
 *
 * @return string Formatted list of variants
 *
 * @package blockab
 */

// Get parameters
$testGroup = $modx->getOption(\'\'testGroup\'\', $scriptProperties, \'\'\'\');
$format = $modx->getOption(\'\'format\'\', $scriptProperties, \'\'detailed\'\');
$includeInactive = (bool)$modx->getOption(\'\'includeInactive\'\', $scriptProperties, false);

// If no test group specified, return default variants
if (empty($testGroup)) {
    return \'\'A||B||C||D||E\'\';
}

// Initialize BlockAB
$blockabPath = $modx->getOption(\'\'blockab.core_path\'\', null,
    $modx->getOption(\'\'core_path\'\') . \'\'components/blockab/\'\');
$blockabModelPath = $blockabPath . \'\'model/\'\';

if (!$modx->loadClass(\'\'blockab\'\', $blockabModelPath . \'\'blockab/\'\', true, true)) {
    $modx->log(modX::LOG_LEVEL_ERROR, \'\'[BlockAB] Could not load BlockAB class\'\');
    return \'\'A||B||C\'\';
}

// Get test
$test = $modx->getObject(\'\'babTest\'\', array(
    \'\'test_group\'\' => $testGroup
));

if (!$test) {
    // Test not found, return default
    return \'\'A||B||C||D||E\'\';
}

// Get variations
$where = array(
    \'\'test\'\' => $test->get(\'\'id\'\')
);

if (!$includeInactive) {
    $where[\'\'active\'\'] = 1;
}

$c = $modx->newQuery(\'\'babVariation\'\');
$c->where($where);
$c->sortby(\'\'variant_key\'\', \'\'ASC\'\');
$variations = $modx->getCollection(\'\'babVariation\'\', $c);

if (empty($variations)) {
    return \'\'A||B||C||D||E\'\';
}

$result = array();

foreach ($variations as $variation) {
    $key = $variation->get(\'\'variant_key\'\');
    $name = $variation->get(\'\'name\'\');

    if ($format === \'\'detailed\'\') {
        $result[] = $key . \'\'==\'\' . $key . \'\' - \'\' . $name;
    } else {
        $result[] = $key;
    }
}

return implode(\'\'||\'\', $result);',
    @category_id
) AS new_vals ON DUPLICATE KEY UPDATE
    description = new_vals.description,
    snippet = new_vals.snippet,
    category = new_vals.category;
