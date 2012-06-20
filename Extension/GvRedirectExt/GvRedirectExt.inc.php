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
     * Redirects to specified location right now.
     * If url is not specified or wrong, nothing happens.
     *
     * @param string $sUrl The URL for redirection.
     *
     * @return void
     */
    public function redirect($sUrl = null)
    {
        // Do nothing, if the url is not specified.
        if (!$sUrl) {
            $this->getApplication()->trace->addLine('[%s] Url is not specified.', __CLASS__);
            return;
        }

        // Set the redirection url to module and go away.
        $this->getApplication()->redirect->setUrl($sUrl);
        $this->getApplication()->redirect->redirect();
        return;
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------