<?php
$success = array();
if ($object && $pluginid= $object->get('id')) {
    switch ($options[xPDOTransport::PACKAGE_ACTION]) {
        case xPDOTransport::ACTION_INSTALL:
        case xPDOTransport::ACTION_UPGRADE:
            if (isset($options['activatePlugin']) && !empty($options['activatePlugin'])) {
                $events = array(
                    'OnBeforeDocFormSave',
                    'OnDocFormRender'
                );
                foreach ($events as $eventName) {
                    /** @var modEvent $event */
                    $event = $object->xpdo->getObject('modEvent',array('name' => $eventName));
                    if ($event) {
                        /** @var modPluginEvent $pluginEvent */
                        $pluginEvent = $object->xpdo->getObject('modPluginEvent',array(
                                'pluginid' => $pluginid,
                                'event' => $event->get('name'),
                            ));
                        if (!$pluginEvent) {
                            $pluginEvent= $object->xpdo->newObject('modPluginEvent');
                            $pluginEvent->set('pluginid', $pluginid);
                            $pluginEvent->set('event', $event->get('name'));
                            $pluginEvent->set('priority', 0);
                            $pluginEvent->set('propertyset', 0);
                            $success[$eventName]= $pluginEvent->save();
                        }
                    }
                    unset($event,$pluginEvent);
                }
                unset($events,$eventName);
            }
            break;
        case xPDOTransport::ACTION_UNINSTALL: break;
    }
}
return array_search(false, $success, true) === false;
