<?php
$buildConfig = dirname(dirname(dirname(dirname(__FILE__)))).'/_build/build.config.php';
if (file_exists($buildConfig)) {
    require_once $buildConfig;
} else {
    require_once dirname(dirname(dirname(dirname(__FILE__)))) . '/config.core.php';
}
require_once MODX_CORE_PATH . 'config/' . MODX_CONFIG_KEY . '.inc.php';
require_once MODX_CONNECTORS_PATH . 'index.php';

$corePath = $modx->getOption('revise.core_path', null, $modx->getOption('core_path', null, MODX_CORE_PATH));
$modx->getService(
    'revise',
    'Revise',
    $corePath . 'components/revise/model/revise/',
    array(
        'core_path' => $corePath
    )
);

/* handle request */
$modx->request->handleRequest(
    array(
        'processors_path' => $modx->getOption('processors_path', null, $corePath . 'components/revise/processors/'),
        'location' => '',
    )
);
