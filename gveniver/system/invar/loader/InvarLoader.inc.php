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

GvInclude::instance()->includeFile('gveniver/GvKernelModule.inc.php');

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
abstract class InvarLoader
{
    /**
     * Analyze invars from request query string.
     *
     * @return array
     * @abstract
     */
    public abstract function analyzeRequest();
    //-----------------------------------------------------------------------------

    /**
     * Building request string by associative array of parameters.
     *
     * @param array $aVariables Array of parameters for building request string.
     *
     * @return string
     * @abstract
     */
    public abstract function buildRequest(array $aVariables = array());
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------