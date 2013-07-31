<?php
$settings = array();

/** @var modSystemSetting $setting */
$setting = $modx->newObject('modSystemSetting');
$setting->fromArray(
    array(
        'key' => 'revise.gc_maxlifetime',
        'value' => '0',
        'xtype' => 'textfield',
        'namespace' => 'revise',
        'area' => 'revise.gc',
    ),
    '',
    true,
    true
);
array_push($settings, $setting);
unset($setting);

return $settings;
