<?php
/**
 * File contains human-friendly url invar loader class.
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
 * Human-friendly url invar loader class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class HfuInvarLoader extends InvarLoader
{
    /**
     * Analyze invars from request query string.
     * 
     * Request string (from SERVER[PATH_INFO]) splits to pairs of pieces.
     * First element is the name of invar, second is value.
     *
     * @return array
     */
    public function analyzeRequest()
    {
        // Explode request invars by url separator.
        $aRequestInvars = \Gveniver\explode_ex('/', isset($_SERVER['PATH_INFO']) ? $_SERVER['PATH_INFO'] : '');
        
        // Filter empty values from begin and end of list.
        if (count($aRequestInvars) > 0) {
            if (!$aRequestInvars[0])
                array_shift($aRequestInvars);

            $nIndexLast = count($aRequestInvars) - 1;
            if (array_key_exists($nIndexLast, $aRequestInvars) && !$aRequestInvars[$nIndexLast])
                array_pop($aRequestInvars);
        }
        
        // The count of parameters must be even.
        if (count($aRequestInvars) % 2 == 1)
            array_push($aRequestInvars, null);
        
        //echo("<pre>".print_r($aRequestInvars, true)."</pre><br/>");
        
        // Building list of key => value pairs.
        $aRet = array();
        $nInvarsCount = count($aRequestInvars);
        for ($i = 0; $i < $nInvarsCount - 1; $i += 2) {
            $sKey = urldecode(trim($aRequestInvars[$i]));
            $sValue = urldecode(trim($aRequestInvars[$i + 1]));
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
            $sRet .= sprintf('%s/%s/', $key, $item);
        
        return $sRet;
        
    } // End function
    //-----------------------------------------------------------------------------
           
} // End class
//-----------------------------------------------------------------------------