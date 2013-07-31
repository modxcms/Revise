<?php
/**
 * Revise build script
 *
 * @package revise
 * @subpackage build
 * @author Jason Coward <jason@modx.com>
 */
$tstart = microtime(true);
set_time_limit(0);

/* package defines */
define('PKG_NAME', 'Revise');
define('PKG_VERSION', '1.0.0');
define('PKG_RELEASE', 'dev');
define('PKG_LNAME', strtolower(PKG_NAME));

$root = dirname(dirname(__FILE__)) . '/';
$sources = array(
    'root' => $root,
    'build' => $root . '_build/',
    'data' => $root . '_build/data/',
    'resolvers' => $root . '_build/resolvers/',
    'properties' => $root . '_build/data/properties/',
    'assets_core' => $root . 'assets/components/' . PKG_LNAME . '/',
    'source_core' => $root . 'core/components/' . PKG_LNAME . '/',
);
unset($root);

// override with your own defines here (see build.config.sample.php)
require_once $sources['build'] . 'build.config.php';
require_once MODX_CORE_PATH . 'model/modx/modx.class.php';

$modx = new modX();
$modx->initialize('mgr');
$modx->setLogLevel(modX::LOG_LEVEL_INFO);
$modx->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');

$modx->loadClass('transport.modPackageBuilder', '', false, true);
$builder = new modPackageBuilder($modx);
$builder->createPackage(PKG_LNAME, PKG_VERSION, PKG_RELEASE);
$builder->registerNamespace(PKG_LNAME, false, true, '{core_path}components/' . PKG_LNAME . '/', '{assets_path}components/' . PKG_LNAME . '/');

/* add system settings */
if ($systemSettings = include $sources['data'] . 'transport.system_settings.php') {
    foreach ($systemSettings as $systemSetting) {
        $vehicle = $builder->createVehicle(
            $systemSetting,
            array(
                xPDOTransport::UPDATE_OBJECT => false,
                xPDOTransport::PRESERVE_KEYS => true
            )
        );
        $builder->putVehicle($vehicle);
    }
}

/* add menu item / action */
/** @var modAction $action */
$action = $modx->newObject('modAction');
$action->fromArray(
    array(
        'id' => 1,
        'namespace' => PKG_LNAME,
        'parent' => 0,
        'controller' => 'index',
        'haslayout' => 1,
        'lang_topics' => 'revise:default',
        'assets' => '',
    ),
    '',
    true,
    true
);
/** @var modMenu $menu */
$menu = $modx->newObject('modMenu');
$menu->fromArray(
    array(
        'parent' => 'components',
        'text' => 'revise',
        'description' => 'revise_desc',
        'icon' => 'images/icons/plugin.gif',
        'menuindex' => '0',
        'params' => '',
        'handler' => '',
    ),
    '',
    true,
    true
);
$menu->addOne($action);
/** @var modTransportVehicle $vehicle */
$vehicle= $builder->createVehicle(
    $menu,
    array (
        xPDOTransport::PRESERVE_KEYS => true,
        xPDOTransport::UPDATE_OBJECT => true,
        xPDOTransport::UNIQUE_KEY => 'text',
        xPDOTransport::RELATED_OBJECTS => true,
        xPDOTransport::RELATED_OBJECT_ATTRIBUTES => array (
            'Action' => array (
                xPDOTransport::PRESERVE_KEYS => false,
                xPDOTransport::UPDATE_OBJECT => true,
                xPDOTransport::UNIQUE_KEY => array ('namespace','controller'),
            ),
        ),
    )
);
$builder->putVehicle($vehicle);
unset($vehicle, $action, $menu);

/* add category and elements */

/** @var modCategory $category */
$category = $modx->newObject('modCategory');
$category->fromArray(
    array(
        'category' => PKG_NAME
    ),
    '',
    true,
    true
);
$vehicle = $builder->createVehicle(
    $category,
    array(
        xPDOTransport::UNIQUE_KEY => 'category',
        xPDOTransport::PRESERVE_KEYS => false,
        xPDOTransport::UPDATE_OBJECT => false,
    )
);
$vehicle->resolve(
    'file',
    array(
         'source' => $sources['source_core'],
         'target' => "return MODX_CORE_PATH . 'components/';",
    )
);
$vehicle->resolve(
    'file',
    array(
         'source' => $sources['assets_core'],
         'target' => "return MODX_ASSETS_PATH . 'components/';",
    )
);
$vehicle->resolve('php', array('source' => $sources['resolvers'] . PKG_NAME . '.model.php'));
$builder->putVehicle($vehicle);
unset($vehicle, $category);

/* package in plugins */
if ($plugins = include $sources['data'] . 'transport.plugins.php') {
    foreach ($plugins as $pluginName => $plugin) {
        $vehicle = $builder->createVehicle(
            $plugin,
            array(
                xPDOTransport::UNIQUE_KEY => 'name',
                xPDOTransport::PRESERVE_KEYS => false,
                xPDOTransport::UPDATE_OBJECT => true,
            )
        );
        if (file_exists($sources['resolvers'] . PKG_NAME . '.category.php')) {
            $vehicle->resolve('php', array('source' => $sources['resolvers'] . PKG_NAME . '.category.php'));
        }
        if (file_exists($sources['resolvers'] . PKG_NAME . '.pluginevents.php')) {
            $vehicle->resolve('php', array('source' => $sources['resolvers'] . PKG_NAME . '.pluginevents.php'));
        }
        $builder->putVehicle($vehicle);
    }
}

/* now pack in the license file, readme and setup options */
$builder->setPackageAttributes(
    array(
        'license' => file_get_contents($sources['source_core'] . 'docs/license.txt'),
        'readme' => file_get_contents($sources['source_core'] . 'docs/readme.txt'),
        'changelog' => file_get_contents($sources['source_core'] . 'docs/changelog.txt'),
        'setup-options' => array(
            'source' => $sources['build'] . 'setup.options.php'
        )
    )
);

/* zip up the package */
$builder->pack();

$tend = microtime(true);
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

$modx->log(modX::LOG_LEVEL_INFO, PKG_NAME . " Package Built Successfully.");
$modx->log(modX::LOG_LEVEL_INFO, "Execution time: {$totalTime}");
exit();
