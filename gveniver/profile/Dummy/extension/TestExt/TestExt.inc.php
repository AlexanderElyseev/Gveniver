<?php
/**
 * File contains test kernel extension.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvInclude::instance()->includeFile('system/extension/SimpleExtension.inc.php');

/**
 * Test kernel extension class.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class TestExt extends SimpleExtension
{
    /**
     * Test extension method.
     * 
     * @param string $v Arg1.
     * @param string $f Arg2.
     *
     * @return string
     */
    public function tESt($v, $f)
    {
        //var_dump(func_get_args());
        return 'this is test';
    }
    
} // End class
//-----------------------------------------------------------------------------