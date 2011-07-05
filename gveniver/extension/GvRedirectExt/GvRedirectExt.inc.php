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
\Gveniver\Loader::i('system/extension/SimpleExtension.inc.php');

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

} // End class
//-----------------------------------------------------------------------------