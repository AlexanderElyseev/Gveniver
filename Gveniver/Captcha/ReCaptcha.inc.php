<?php
/**
 * File contains controller class for CAPTCHA using reCAPTCHA system.
 *
 * @category  Gveniver
 * @package   BaseCaptcha
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Captcha;
require_once 'Lib/recaptchalib.php';

/**
 * Controller class for CAPTCHA using reCAPTCHA system.
 *
 * @category  Gveniver
 * @package   BaseCaptcha
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class ReCaptcha extends BaseCaptcha
{
    /**
     * Public key for reCaptcha.
     *
     * @var string
     */
    private $_sPublicKey;
    //-----------------------------------------------------------------------------

    /**
     * Private key for reCaptcha.
     *
     * @var string
     */
    private $_sPrivateKey;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     *
     * @param array $aArgs Arguments for CAPCTHA provider from configuration.
     */
    public function __construct(array $aArgs)
    {
        if (!isset($aArgs['PublicKey']) || !isset($aArgs['PrivateKey']))
            throw new \Exception('Public and private key must be set.');

        $this->_sPublicKey = $aArgs['PublicKey'];
        $this->_sPrivateKey = $aArgs['PrivateKey'];

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Display CAPTCHA for user.
     *
     * @return string
     * @abstract
     */
    public function display()
    {
        return \recaptcha_get_html($this->_sPublicKey, true);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Check answer of user.
     *
     * @return boolean
     */
    public function check()
    {
        $resp = recaptcha_check_answer(
            $this->_sPrivateKey,
            $_SERVER['REMOTE_ADDR'],
            isset($_POST['recaptcha_challenge_field']) ? $_POST['recaptcha_challenge_field'] : null,
            isset($_POST['recaptcha_response_field']) ? $_POST['recaptcha_response_field'] : null
        );

        return $resp->is_valid;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------