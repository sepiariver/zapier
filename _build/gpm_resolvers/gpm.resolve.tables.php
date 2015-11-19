<?php
/**
 * Resolve creating db tables
 *
 * THIS RESOLVER IS AUTOMATICALLY GENERATED, NO CHANGES WILL APPLY
 *
 * @package zapier
 * @subpackage build
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('zapier.core_path', null, $modx->getOption('core_path') . 'components/zapier/') . 'model/';
            $modx->addPackage('zapier', $modelPath, 'modx_');


            $manager = $modx->getManager();

            $manager->createObjectContainer('ZapierSubscriptions');

            break;
    }
}

return true;