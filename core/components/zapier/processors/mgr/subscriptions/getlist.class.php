<?php
/**
 * Get list of Zapier Subscriptions
 *
 * @package Zapier
 * @subpackage processors
 */
class ZapierSubscriptionsGetListProcessor extends modObjectGetListProcessor {
    public $classKey = 'ZapierSubscriptions';
    public $languageTopics = array('zapier:default');
    public $defaultSortField = 'id';
    public $defaultSortDirection = 'ASC';
    public $objectType = 'zapier.subscriptions';
    /** @var ZapierSubscriptions */
    public $object;
}

return 'ZapierSubscriptionsGetListProcessor';