<?php
$elements = array();

/** @var modPlugin $element */
$element = $modx->newObject('modPlugin');
$element->fromArray(
    array(
        'name' => 'Revise',
        'description' => 'Handles creating historical revisions when editing Resources.'
    ),
    '',
    true,
    true
);
$element->set('plugincode', file_get_contents($sources['source_core'] . 'elements/plugins/Revise.php'));

if (is_readable($sources['properties'] . $element->get('name') . '.properties.php')) {
    $properties = include $sources['properties'] . $element->get('name') . '.properties.php';
    $element->setProperties($properties);
}
array_push($elements, $element);
unset($element, $properties);

return $elements;
