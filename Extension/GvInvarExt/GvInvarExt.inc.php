<?php
/**
 * File contains extension class for working with invar parameters from template.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Extension;

/**
 * Extension class for working with invar parameters from template.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class GvInvarExt extends SimpleExtension
{
    /**
     * Returns value of input variable by name.
     *
     * @param string  $sInvarName Name of invar for loading value.
     * @param boolean $bFromPost  Load variable from POST.
     *
     * @return mixed|null Returns null if variable with specified name is not defined.
     */
    public function getVariable($sInvarName, $bFromPost = false)
    {
        if (!$sInvarName)
            return null;

        return $bFromPost
            ? $this->getApplication()->invar->post($sInvarName)
            : $this->getApplication()->invar->get($sInvarName);
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Extended variant of loading input variables by name with filtration.
     *
     * @param string  $sInvarName The name of invar for loading value.
     * @param array   $aFilter    The data for filters.
     * @param boolean $bFromPost  Load variable from POST.
     *
     * @return mixed|null Returns null if variable with specified name is not defined.
     */
    public function getVariableEx($sInvarName, array $aFilter, $bFromPost = false)
    {
        if (!$sInvarName)
            return null;

        $mResult = null;
        $bResult = $bFromPost
            ? $this->getApplication()->invar->postEx($sInvarName, $aFilter, $mResult)
            : $this->getApplication()->invar->getEx($sInvarName, $aFilter, $mResult);
        if (!$bResult)
            return null;

        return $mResult;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns link address by specified parameters.
     *
     * @param array $aParams Parameters or request for link.
     *
     * @return string
     */
    public function getLink(array $aParams = array())
    {
        return $this->getApplication()->invar->getLink($aParams);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------