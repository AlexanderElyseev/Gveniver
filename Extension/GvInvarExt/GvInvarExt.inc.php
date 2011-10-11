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
     * Return value of input variable by name.
     *
     * @param string  $sInvarName Name of invar for loading value.
     * @param boolean $bFromPost  Load variable from POST.
     *
     * @return mixed|null
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