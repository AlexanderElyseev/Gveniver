<?php
/**
 * File contains simple invar loader class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Invar\Loader;

/**
 * Simple invar loader class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class SimpleInvarLoader extends BaseLoader
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