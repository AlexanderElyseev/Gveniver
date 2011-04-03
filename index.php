<?php
/**
 *
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
require_once 'init.inc.php';

$cDummyKernel = new GvKernel('Dummy');

$cDummyKernel->template;
$cDummyKernel->invar;
$cDummyKernel->data;
$cDummyKernel->trace;
$cDummyKernel->extension;

// Start profile and output result content.
echo $cDummyKernel->getProfile()->start();

//-----------------------------------------------------------------------------
printf(
    'End. Time: %.4f, memory: %.2f MiB;<br/>',
    round(microtime(true) - $begin, 4),
    memory_get_peak_usage() / 1024 / 1024
);
//-----------------------------------------------------------------------------