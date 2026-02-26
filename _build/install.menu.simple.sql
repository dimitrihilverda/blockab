-- BlockAB Menu - Simplified Installation
-- Run these queries ONE BY ONE and check the results

-- Step 1: Create namespace
INSERT INTO `mdx_namespaces` (`name`, `path`, `assets_path`)
VALUES ('blockab', '{core_path}components/blockab/', '{assets_path}components/blockab/')
ON DUPLICATE KEY UPDATE `path` = '{core_path}components/blockab/', `assets_path` = '{assets_path}components/blockab/';

-- Step 2: Check namespace was created (run this to verify)
SELECT * FROM `mdx_namespaces` WHERE `name` = 'blockab';

-- Step 3: Create action (if step 2 shows the namespace)
INSERT INTO `mdx_actions` (`namespace`, `controller`, `haslayout`, `lang_topics`, `assets`, `help_url`)
VALUES ('blockab', 'index', 1, 'blockab:default', '', '');

-- Step 4: Get the action ID that was just created
SELECT id FROM `mdx_actions` WHERE `namespace` = 'blockab' AND `controller` = 'index';
-- Note this ID, you'll need it for the menu!

-- Step 5: Create menu (replace XXX with the action ID from step 4)
-- Example: If action ID is 123, change 'XXX' to '123'
INSERT INTO `mdx_menus` (`text`, `parent`, `description`, `icon`, `menuindex`, `params`, `handler`, `permissions`, `namespace`, `action`)
VALUES ('blockab', 'components', 'blockab.menu_desc', '', 0, '', '', '', 'blockab', 'XXX');

-- Step 6: Verify menu was created
SELECT * FROM `mdx_menus` WHERE `text` = 'blockab';
