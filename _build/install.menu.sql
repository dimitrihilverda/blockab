-- BlockAB Menu Item
-- Add menu item to MODX Manager
-- Replace 'mdx_' with your actual MODX table prefix if different

-- First, create the namespace if it doesn't exist
INSERT IGNORE INTO `mdx_namespaces` (`name`, `path`, `assets_path`)
VALUES ('blockab', '{core_path}components/blockab/', '{assets_path}components/blockab/');

-- Create the action for the home controller (including help_url with empty string)
INSERT INTO `mdx_actions` (`namespace`, `controller`, `haslayout`, `lang_topics`, `assets`, `help_url`)
VALUES ('blockab', 'index', 1, 'blockab:default', '', '');

-- Get the action ID (you may need to adjust this based on your installation)
SET @action_id = LAST_INSERT_ID();

-- Create the menu item
INSERT INTO `mdx_menus` (`text`, `parent`, `description`, `icon`, `menuindex`, `params`, `handler`, `permissions`, `namespace`, `action`)
VALUES ('blockab', 'components', 'blockab.menu_desc', '', 0, '', '', '', 'blockab', 'index');

-- Add system settings
INSERT INTO `mdx_system_settings` (`key`, `value`, `xtype`, `namespace`, `area`, `editedon`)
VALUES
('blockab.default_variant_keys', 'A,B,C,D,E,F,G,H,I,J', 'textfield', 'blockab', 'blockab', NULL)
ON DUPLICATE KEY UPDATE `value` = VALUES(`value`);
