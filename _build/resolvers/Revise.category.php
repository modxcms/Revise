<?php
$success = true;
if ($object && $pluginid= $object->get('id')) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            if ($category = $object->xpdo->getObject('modCategory', array('category' => 'Revise'))) {
                $object->set('category', $category->get('id'));
                $success = $object->save();
            } else {
                $success = false;
            }
            break;
        case xPDOTransport::ACTION_UNINSTALL: break;
    }
}
return $success;
