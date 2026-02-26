<?php
$xpdo_meta_map['babPick']= array (
  'package' => 'blockab',
  'version' => '1.0',
  'table' => 'blockab_pick',
  'tableMeta' => 
  array (
    'engine' => 'InnoDB',
  ),
  'fields' => 
  array (
    'test' => NULL,
    'variation' => NULL,
    'date' => NULL,
    'amount' => 0,
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
    'variation' => 
    array (
      'dbtype' => 'int',
      'precision' => '11',
      'phptype' => 'integer',
      'null' => false,
      'attributes' => 'unsigned',
      'index' => 'index',
    ),
    'date' => 
    array (
      'dbtype' => 'int',
      'precision' => '8',
      'phptype' => 'integer',
      'null' => false,
      'attributes' => 'unsigned',
      'index' => 'index',
    ),
    'amount' => 
    array (
      'dbtype' => 'int',
      'precision' => '10',
      'phptype' => 'integer',
      'null' => false,
      'default' => 0,
      'attributes' => 'unsigned',
    ),
  ),
  'indexes' => 
  array (
    'test_date' => 
    array (
      'alias' => 'test_date',
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
        'date' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
    'variation_date' => 
    array (
      'alias' => 'variation_date',
      'primary' => false,
      'unique' => false,
      'type' => 'BTREE',
      'columns' => 
      array (
        'variation' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
        'date' => 
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
    'Variation' => 
    array (
      'class' => 'babVariation',
      'cardinality' => 'one',
      'foreign' => 'id',
      'local' => 'variation',
      'owner' => 'foreign',
    ),
  ),
);
