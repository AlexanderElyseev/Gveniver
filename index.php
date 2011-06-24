<?php
/**
 * Base index file.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
//-----------------------------------------------------------------------------
$begin = microtime(true);
//-----------------------------------------------------------------------------

// Include Gveniver Framework.
require_once 'gveniver/init.inc.php';

// Include Smarty.
require_once '/var/www/lib/Smarty-3.0.7/libs/Smarty.class.php';

// Include Propel.
//set_include_path('/var/www/profclub/eas/data/propel/build/classes/'.PATH_SEPARATOR.get_include_path());
//require_once 'propel/Propel.php';
//require_once 'ProfClubPDO.php';
//Propel::init('/var/www/profclub/eas/data/propel/build/conf/profclub-conf.php');

// Start profile and output result content.
$cDummyKernel = new Gveniver\Kernel\Kernel('Dummy');
echo $cDummyKernel->getProfile()->start();

//-----------------------------------------------------------------------------
printf(
    'End. Time: %.4f sec., memory: %.2f MiB;<br/>',
    round(microtime(true) - $begin, 4),
    memory_get_peak_usage() / 1024 / 1024
);
//-----------------------------------------------------------------------------