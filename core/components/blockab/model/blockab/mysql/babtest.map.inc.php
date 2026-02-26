<?php
$xpdo_meta_map['babTest']= array (
  'package' => 'blockab',
  'version' => '1.0',
  'table' => 'blockab_test',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'name' => NULL,
    'description' => NULL,
    'test_group' => NULL,
    'active' => 1,
    'archived' => 0,
    'smartoptimize' => 1,
    'threshold' => 100,
    'randomize' => 25,
    'resources' => NULL,
    'contexts' => NULL,
    'created_at' => NULL,
    'updated_at' => NULL,
  ),
  'fieldMeta' => 
  array (
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'test_group' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
      'index' => 'index',
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'bool',
      'null' => false,
      'default' => 1,
      'index' => 'index',
    ),
    'archived' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'bool',
      'null' => false,
      'default' => 0,
      'index' => 'index',
    ),
    'smartoptimize' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'bool',
      'null' => false,
      'default' => 1,
    ),
    'threshold' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 100,
    ),
    'randomize' => 
    array (
      'dbtype' => 'int',
      'precision' => '3',
      'phptype' => 'integer',
      'null' => false,
      'default' => 25,
    ),
    'resources' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'contexts' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => true,
    ),
    'created_at' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
    'updated_at' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => true,
    ),
  ),
  'indexes' => 
  array (
    'test_group' =>
    array (
      'alias' => 'test_group',
      'primary' => false,
      'unique' => true,
      'type' => 'BTREE',
      'columns' =>
      array (
        'test_group' =>
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'active' => 
    array (
      'alias' => 'active',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'active' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'composites' => 
  array (
    'Variations' => 
    array (
      'class' => 'babVariation',
      'cardinality' => 'many',
      'foreign' => 'test',
      'local' => 'id',
      'owner' => 'local',
    ),
    'Picks' => 
    array (
      'class' => 'babPick',
      'cardinality' => 'many',
      'foreign' => 'test',
      'local' => 'id',
      'owner' => 'local',
    ),
    'Conversions' => 
    array (
      'class' => 'babConversion',
      'cardinality' => 'many',
      'foreign' => 'test',
      'local' => 'id',
      'owner' => 'local',
    ),
  ),
);
