<?php
/**
 * File contains invar loader class for alternative view.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Invar;
\Gveniver\Loader::i('system/invar/loader/InvarLoader.inc.php');

/**
 * AInvar loader class for alternative view.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class AlternativeInvarLoader extends InvarLoader
{
    /**
     * Analyze invars from request query string.
     *
     * @return array
     */
    public function analyzeRequest()
    {
        // Explode request invars by url separator.
        $aVars = \Gveniver\explode_ex('/', isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '');
        
        //echo("<pre>".print_r($aVars, true)."</pre><br/>");

        // Building list of correct key => value pairs.
        $aRet = array();
        foreach ($aVars as $aInvar) {
            $sKey = isset($aInvar[0]) ? urldecode(trim($aInvar[0])) : null;
            $sValue = isset($aInvar[1]) ? urldecode(trim($aInvar[1])) : null;
            $aRet[$sKey] = $sValue;
        }

        return $aRet;

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
        $sRet = '';
        foreach ($aVariables as $key => $item)
            if ($key && $item)
                $sRet .= sprintf('%s,%s/', $key, $item);
        
        return $sRet;
        
    } // End function
    //-----------------------------------------------------------------------------
       
} // End class
//-----------------------------------------------------------------------------
