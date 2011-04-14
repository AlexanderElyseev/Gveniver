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

/**
 *
 *
 * Provide Singleton pattern.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
abstract class GvKernelConst
{
    /**
     * Separator of path in configuration tree, used in {@see GvKernelConfig}.
     *
     * @var string
     */
    const CONFIG_PATH_SEPARATOR = '/';
    //-----------------------------------------------------------------------------

    /**
     * XML configuration file name, used in {@see GvKernelConfig}.
     * 
     * @var string
     */
    const CONFIG_XML_FILE = 'config.xml';
    //-----------------------------------------------------------------------------

    /**
     * Cache configuration file name, used in {@see GvKernelConfig}.
     *
     * @var string
     */
    const CONFIG_CACHE_FILE = 'config.dat';
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------


} // End class
//-----------------------------------------------------------------------------