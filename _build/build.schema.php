<?php
/**
 * Revise schema build script
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
    'properties' => $root . '_build/data/properties/',
    'assets_core' => $root . 'assets/components/' . PKG_LNAME,
    'source_core' => $root . 'core/components/' . PKG_LNAME,
    'model' => $root . 'core/components/' . PKG_LNAME . '/model/',
);
unset($root);

/* override with your own defines here (see build.config.sample.php) */
require_once $sources['build'] . 'build.config.php';
require_once MODX_CORE_PATH . 'xpdo/xpdo.class.php';

/* override with your own properties here (see build.properties.sample.php) */
require_once $sources['build'] . 'build.properties.php';

foreach (array('mysql', /*'sqlsrv'*/) as $driver) {
    $xpdo= new xPDO(
        $properties["{$driver}_string_dsn_nodb"],
        $properties["{$driver}_string_username"],
        $properties["{$driver}_string_password"],
        $properties["{$driver}_array_options"],
        $properties["{$driver}_array_driverOptions"]
    );
    $xpdo->setPackage('modx', dirname(XPDO_CORE_PATH) . '/model/');
    $xpdo->setLogTarget(XPDO_CLI_MODE ? 'ECHO' : 'HTML');
    $xpdo->setDebug(true);

    $manager= $xpdo->getManager();
    $generator= $manager->getGenerator();

    $generator->classTemplate= <<<EOD
<?php
/*
 * Revise
 *
 * Copyright 2013 by Jason Coward <jason@modx.com>
 *
 * This file is part of Revise, a simple versioning component for MODX Revolution.
 *
 * Revise is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Revise is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Revise; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 */

class [+class+] extends [+extends+] {}
EOD;
    $generator->platformTemplate= <<<EOD
<?php
/*
 * Revise
 *
 * Copyright 2013 by Jason Coward <jason@modx.com>
 *
 * This file is part of Revise, a simple versioning component for MODX Revolution.
 *
 * Revise is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Revise is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Revise; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 */

require_once dirname(dirname(__FILE__)) . '/[+class-lowercase+].class.php';
class [+class+]_[+platform+] extends [+class+] {}
EOD;
    $generator->mapHeader= <<<EOD
<?php
/*
 * Revise
 *
 * Copyright 2013 by Jason Coward <jason@modx.com>
 *
 * This file is part of Revise, a simple versioning component for MODX Revolution.
 *
 * Revise is free software; you can redistribute it and/or modify it under the
 * terms of the GNU General Public License as published by the Free Software
 * Foundation; either version 2 of the License, or (at your option) any later
 * version.
 *
 * Revise is distributed in the hope that it will be useful, but WITHOUT ANY
 * WARRANTY; without even the implied warranty of MERCHANTABILITY or FITNESS FOR
 * A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License along with
 * Revise; if not, write to the Free Software Foundation, Inc., 59 Temple Place,
 * Suite 330, Boston, MA 02111-1307 USA
 */
EOD;
    $generator->parseSchema($sources['build'] . 'schema/'.PKG_LNAME.'.'.$driver.'.schema.xml', $sources['model']);
}

$tend = microtime(true);
$totalTime = ($tend - $tstart);
$totalTime = sprintf("%2.4f s", $totalTime);

echo "Parsed Schema " . PKG_NAME. " Successfully.\n";
echo "Execution time: {$totalTime}\n";
exit();
