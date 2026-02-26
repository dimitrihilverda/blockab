-- Migration: Add unique constraint to test_group column
-- This ensures test_group names are unique across all tests

-- First, check if there are any duplicate test_group values
-- You can run this query to see duplicates:
-- SELECT test_group, COUNT(*) FROM mdx_blockab_test GROUP BY test_group HAVING COUNT(*) > 1;

-- Drop existing index if it exists
ALTER TABLE `mdx_blockab_test` DROP INDEX `test_group`;

-- Add unique index on test_group
ALTER TABLE `mdx_blockab_test` ADD UNIQUE INDEX `test_group` (`test_group`);
