<?php
/**
 * File contains class of module for CAPTCHA.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Kernel;
\Gveniver\Loader::i('system/kernel/Module.inc.php');
\Gveniver\Loader::i('system/captcha/factory/CaptchaFactory.inc.php');

/**
 * Class of module for CAPTCHA.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class CaptchaModule extends Module
{
    /**
     * Current CAPTCHA controller.
     *
     * @var \Gveniver\Captcha\Captcha
     */
    private $_cCaptcha;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Full initialization of module.
     *
     * @return bool True on success.
     */
    protected function init()
    {
        $this->getApplication()->trace->addLine('[%s] Init.', __CLASS__);

        // Load configuration.
        $aProvidersList = $this->getApplication()->getConfig()->get('Module/CaptchaModule/Providers');
        if (!$aProvidersList || !is_array($aProvidersList) || count($aProvidersList) == 0) {
            $this->getApplication()->trace->addLine(
                '[%s] Configuration of CAPTCHA is not exists.',
                __CLASS__
            );
            return false;
        }

        // Use first provider.
        $aProvider = reset($aProvidersList);
        if (!is_array($aProvider) || !isset($aProvider['Class'])) {
            $this->getApplication()->trace->addLine(
                '[%s] Wrong configuration of CAPTCHA.',
                __CLASS__
            );
            return false;
        }
        $sClassName = $aProvider['Class'];
        $aArgs = isset($aProvider['Args']) && is_array($aProvider['Args']) ? $aProvider['Args'] : array();

        // Create controller.
        $this->_cCaptcha =  \Gveniver\Loader::createObject(
            array(
                'class' => $sClassName,
                'ns'    => '\\Gveniver\\Captcha',
                'path'  => 'system/captcha/%class%.inc.php',
                'args'  => array($aArgs)
            ),
            $nErrCode
        );
        if (!$this->_cCaptcha) {
            $this->getApplication()->trace->addLine(
                '[%s] Error in creating controller for CAPTCHA: "%s".',
                __CLASS__,
                \Gveniver\Loader::getErrorInfo($nErrCode)
            );
            return false;
        }

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Display CAPTCHA for user.
     *
     * @return string
     */
    public function display()
    {
        return $this->_cCaptcha->display();
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Check answer of user.
     *
     * @param string $sKey      Key for check response.
     * @param string $sResponse Response of user.
     *
     * @return boolean
     */
    public function check($sKey, $sResponse)
    {
        return $this->_cCaptcha->check($sKey, $sResponse);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------
