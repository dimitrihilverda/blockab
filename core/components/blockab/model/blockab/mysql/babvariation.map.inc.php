<?php
$xpdo_meta_map['babVariation']= array (
  'package' => 'blockab',
  'version' => '1.0',
  'table' => 'blockab_variation',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'test' => NULL,
    'name' => NULL,
    'variant_key' => NULL,
    'description' => NULL,
    'active' => 1,
    'weight' => 100,
    'created_at' => NULL,
  ),
  'fieldMeta' => 
  array (
    'test' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'attributes' => 'unsigned',
      'index' => 'index',
    ),
    'name' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '255',
      'phptype' => 'string',
      'null' => false,
    ),
    'variant_key' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
      'null' => false,
    ),
    'description' => 
    array (
      'dbtype' => 'text',
      'phptype' => 'string',
      'null' => true,
    ),
    'active' => 
    array (
      'dbtype' => 'tinyint',
      'precision' => '1',
      'phptype' => 'bool',
      'null' => false,
      'default' => 1,
    ),
    'weight' => 
    array (
      'dbtype' => 'int',
      'precision' => '3',
      'phptype' => 'integer',
      'null' => false,
      'default' => 100,
    ),
    'created_at' => 
    array (
      'dbtype' => 'datetime',
      'phptype' => 'datetime',
      'null' => false,
    ),
  ),
  'indexes' => 
  array (
    'test' => 
    array (
      'alias' => 'test',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'test' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'variant_key' => 
    array (
      'alias' => 'variant_key',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'variant_key' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
  'aggregates' => 
  array (
    'Test' => 
    array (
      'class' => 'babTest',
      'cardinality' => 'one',
      'foreign' => 'id',
      'local' => 'test',
      'owner' => 'foreign',
    ),
  ),
);
