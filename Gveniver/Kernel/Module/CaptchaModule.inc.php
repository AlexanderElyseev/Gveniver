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

namespace Gveniver\Kernel\Module;

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
class CaptchaModule extends BaseModule
{
    /**
     * Current CAPTCHA controller.
     *
     * @var \Gveniver\Captcha\BaseCaptcha
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

        $sClassName = '\\Gveniver\\Captcha\\'.$aProvider['Class'];
        $aArgs = isset($aProvider['Args']) && is_array($aProvider['Args'])
            ? $aProvider['Args']
            : array();

        // Create provider.
        $this->_cCaptcha = new $sClassName($aArgs);
        if (!$this->_cCaptcha) {
            $this->getApplication()->trace->addLine(
                '[%s] Error in creating controller for CAPTCHA.',
                __CLASS__
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
     * @return boolean
     */
    public function check()
    {
        return $this->_cCaptcha->check();

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------
