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

GvKernelInclude::instance()->includeFile('src/system/invar/loader/InvarLoader.inc.php');

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
class SimpleInvarLoader extends InvarLoader
{
    /**
     * Analyze invars from request query string.
     * Build list by GET array.
     *
     * @return array
     */
    public function analyzeRequest()
    {
        return array();

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Building request string by associative array of parameters.
     *
     * @param array $aVariables Array of parameters for building request string.
     *
     * @return string
     */
    public function buildRequest(array $aVariables = array())
    {
        return '?'.http_build_query($aVariables);
        
    } // End function
    //-----------------------------------------------------------------------------
       
} // End class
//-----------------------------------------------------------------------------