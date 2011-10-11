<?php
/**
 * File contains extension class for redirections.
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
 * Extension class for redirections.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class GvRedirectExt extends SimpleExtension
{
    /**
     * Returns redirect data from {@see RedirectModule}.
     * 
     * @return string
     */
    public function getRedirect()
    {
        return $this->getApplication()->redirect->__toString();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Redirect to specified location right now.
     * If url is not specified or wron, nothing happens.
     *
     * @param string $sUrl Url for redirection
     *
     * @return void
     */
    public function redirect($sUrl = null)
    {
        // Do nothing, if url is not specified.
        if (!$sUrl) {
            $this->getApplication()->trace->addLine('[%s] Url is not specified.', __CLASS__);
            return;
        }

        // Set redirection url to module and go away.
        $this->getApplication()->redirect->setUrl($sUrl);
        $this->getApplication()->redirect->redirect();
        return;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns saved in session value of variable with specified name.
     * If nothing is loaded, return specified default value.
     *
     * @param string $sName         Name of variable for loading.
     * @param mixed  $mDefaultValue Default value, returns if nothig is loaded.
     *
     * @return mixed
     */
    public function getSessionVariable($sName = null, $mDefaultValue = null)
    {
        // Do nothing, if variable name is not specified.
        if (!$sName)
            return null;

        $sRet = $this->getApplication()->redirect->getSessionVariable($sName);
        return $sRet ? $sRet : $mDefaultValue;

    }  // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------