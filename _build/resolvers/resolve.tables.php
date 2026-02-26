<?php
/**
 * Resolver to create database tables
 *
 * @package blockab
 * @subpackage build
 */

if ($object->xpdo) {
    $modx =& $object->xpdo;

    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            $modelPath = $modx->getOption('blockab.core_path', null,
                $modx->getOption('core_path') . 'components/blockab/') . 'model/';

            $modx->addPackage('blockab', $modelPath);

            $manager = $modx->getManager();

            // Create tables
            $manager->createObjectContainer('babTest');
            $manager->createObjectContainer('babVariation');
            $manager->createObjectContainer('babPick');
            $manager->createObjectContainer('babConversion');

            break;

        case xPDOTransport::ACTION_UNINSTALL:
            $modelPath = $modx->getOption('blockab.core_path', null,
                $modx->getOption('core_path') . 'components/blockab/') . 'model/';

            $modx->addPackage('blockab', $modelPath);

            $manager = $modx->getManager();

            // Remove tables
            $manager->removeObjectContainer('babConversion');
            $manager->removeObjectContainer('babPick');
            $manager->removeObjectContainer('babVariation');
            $manager->removeObjectContainer('babTest');

            break;
    }
}

return true;
