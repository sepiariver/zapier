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
    'event' => '',
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
      'null' => false,
      'default' => '',
    ),
    'client_id' => 
    array (
      'dbtype' => 'varchar',
      'precision' => '80',
      'phptype' => 'string',
    ),
  ),
);
