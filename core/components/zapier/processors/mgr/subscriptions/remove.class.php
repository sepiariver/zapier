<?php
/**
 * Remove a Zapier Client
 * 
 * @package Zapier
 * @subpackage processors
 */
class ZapierSubscriptionsRemoveProcessor extends modObjectRemoveProcessor {
    public $classKey = 'ZapierSubscriptions';
    public $languageTopics = array('zapier:default');
    public $objectType = 'zapier.subscriptions';
    /** @var ZapierSubscriptions */
    public $object;

    public function afterRemove()
    {
        /** @var xPDOFileCache $provider */
        $provider = $this->modx->cacheManager->getCacheProvider('zapier');
        $provider->flush();

        return parent::afterRemove();
    }
}

return 'ZapierSubscriptionsRemoveProcessor';