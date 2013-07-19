<?php
$elements = array();

/** @var modPlugin $element */
$element = $modx->newObject('modPlugin');
$element->fromArray(
    array(
        'name' => 'Revise',
        'plugincode' => file_get_contents($sources['source_core'] . 'elements/plugins/Revise.php'),
    ),
    '',
    true,
    true
);
$properties = include $sources['properties'] . 'Revise.properties.php';
$element->setProperties($properties);
array_push($elements, $element);
unset($element, $properties);

return $elements;
