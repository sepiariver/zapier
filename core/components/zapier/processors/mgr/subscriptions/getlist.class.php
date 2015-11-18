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
    public $objectType = 'zapier.subscriptions';
    /** @var ZapierSubscriptions */
    public $object;
}

return 'ZapierSubscriptionsGetListProcessor';