<?php
/**
 * File contains base abstract class for loader of invars.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvInclude::i('GvKernelModule.inc.php');

/**
 * Base abstract class for loader of invars.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
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