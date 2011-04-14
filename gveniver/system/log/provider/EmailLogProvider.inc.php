<?php
/**
 * File contains log provider class for saving log data to email.
 *
 * @category  Gveniver
 * @package   Log
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvKernelInclude::instance()->includeFile('gveniver/system/log/provider/LogProvider.inc.php');

/**
 * Log provider class for saving log data to email.
 *
 * @category  Gveniver
 * @package   Log
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class EmailLogProvider extends LogProvider
{
    /**
     * Recipient of email.
     *
     * @var string.
     */
    private $_sRecipient;
    //-----------------------------------------------------------------------------

    /**
     * Sender of email.
     *
     * @var string.
     */
    private $_sSender;
    //-----------------------------------------------------------------------------

    /**
     * Subject of email.
     *
     * @var string.
     */
    private $_sSubject;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     * Initialize member fields.
     *
     * @param GvKernel $cKernel     Current kernel.
     * @param array    $aConfigData Configuration data for provider.
     *
     * @return void
     */
    public function __construct(GvKernel $cKernel, array $aConfigData)
    {
        // Use parent constructor.
        parent::__construct($cKernel, $aConfigData);

        $bExistsRecipient = isset($aConfigData['Recipient']) && is_string($aConfigData['Recipient']);
        $bExistsFrom = isset($aConfigData['Sender']) && is_string($aConfigData['Sender']);
        $bExistsSubject = isset($aConfigData['Subject']) && is_string($aConfigData['Subject']);
        
        if (!$bExistsRecipient || !$bExistsFrom || !$bExistsSubject)
            throw new GvException('One ore more of configuration parameters not loaede.');

        $this->_sRecipient = $aConfigData['Recipient'];
        $this->_sSender = $aConfigData['Sender'];
        $this->_sSubject = $aConfigData['Subject'];

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Send email message with log data.
     * Do not use special classes for sending mail for prevent relation conflicts
     * with other classes.
     * 
     * @param array $aData Data to save.
     *
     * @return void
     */
    public function save(array $aData)
    {
        // Building email text.
        $sMessage = "Automatically generated log message.\n\nLog data:\n";
        foreach ($aData as $aLog) {
            $sMessage .= sprintf(
                "[%s] - %s - %d - %s - %s\n",
                date('Y-m-d H:i:s', $aLog['time']),
                self::getNameByLevel($aLog['level']),
                $aLog['code'],
                $aLog['message'],
                serialize($aLog['data'])
            );
        }
        $sMessage .= "\n";

        // Building email header.
        $sHeaders = sprintf("From: %s\r\n", $this->_sSender);
        $sHeaders .= "MIME-Version: 1.0\r\n";
        $sHeaders .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";

        // Send email.
        mail(
            mb_encode_mimeheader($this->_sRecipient, 'UTF-8', 'B', "\n"),
            mb_encode_mimeheader($this->_sSubject, 'UTF-8', 'B', "\n"),
            $sMessage,
            $sHeaders
        );

    } // End function
    //-----------------------------------------------------------------------------
    
} // End class
//-----------------------------------------------------------------------------
