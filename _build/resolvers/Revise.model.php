<?php
if ($object && $object->xpdo) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            /** @var modX $modx */
            $modx =& $object->xpdo;
            $corePath = $modx->getOption('revise.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH));
            $modx->getService('revise', 'Revise', $corePath . 'components/revise/model/revise/', array('core_path' => $corePath));

            /** @var xPDOManager $manager */
            $manager = $modx->getManager();
            $oldLevel = $modx->setLogLevel(modX::LOG_LEVEL_ERROR);
            $manager->createObjectContainer('ReviseResourceDraft');
            $manager->createObjectContainer('ReviseResourceHistory');

            /* make sure source column gets default="0" */
            $manager->alterField('ReviseResourceDraft', 'source');

            $modx->setLogLevel($oldLevel);
            break;
        default:
            break;
    }
}
return true;
