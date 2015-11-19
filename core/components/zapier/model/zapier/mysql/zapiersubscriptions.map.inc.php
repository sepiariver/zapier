<?php
/**
 * @package zapier
 */
$xpdo_meta_map['ZapierSubscriptions']= array (
  'package' => 'zapier',
  'version' => '0.1',
  'table' => 'zapier_subscriptions',
  'extends' => 'xPDOSimpleObject',
  'fields' => 
  array (
    'target_url' => '',
    'event' => NULL,
    'client_id' => NULL,
  ),
  'fieldMeta' => 
  array (
    'target_url' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '2000',
      'phptype' => 'string',
      'null' => false,
      'default' => '',
    ),
    'event' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '100',
      'phptype' => 'string',
    ),
    'client_id' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '80',
      'phptype' => 'string',
    ),
  ),
  'indexes' => 
  array (
    'target_url' => 
    array (
      'alias' => 'target_url',
      'primary' => false,
      'unique' => true,
      'columns' => 
      array (
        'target_url' => 
        array (
          'length' => '',
          'collation' => 'A',
          'null' => false,
        ),
      ),
    ),
  ),
);
