<?php
/**
 * File contains class of module for redirections.
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
 * Class of module for redirections.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class RedirectModule extends BaseModule
{
    /**
     * Redirection url.
     *
     * @var string
     */
    private $_sUrl;
    //-----------------------------------------------------------------------------

    /**
     * Redirection time.
     *
     * @var int
     */
    private $_nRedirectionTime;
    //-----------------------------------------------------------------------------

    /**
     * Redirection template name.
     *
     * @var string
     */
    private $_sRedirectionTemplateName;
    //-----------------------------------------------------------------------------

    /**
     * Redirection link text.
     *
     * @var string
     */
    private $_sLinkText;
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

        // Load redirection configuration.
        $this->_nRedirectionTime = (int)$this->getApplication()->getConfig()->get('Module/RedirectModule/WaitTime');
        $this->_sRedirectionTemplateName = $this->getApplication()->getConfig()->get('Module/RedirectModule/Template');

        $this->getApplication()->trace->addLine('[%s] Init successful.', __CLASS__);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Outputs redirection link as string.
     *
     * @return string
     */
    public function __toString()
    {
        // Do not redirect to incorrect url.
        if (!$this->_sRedirectionTemplateName || !$this->_sUrl || !\Gveniver\is_correct_url($this->_sUrl))
            return '';

        // Load template for redirection.
        $cTpl = null;
        if (!$this->getApplication()->template->getTemplate($this->_sRedirectionTemplateName, $cTpl))
            return '';

        /** @var $cTpl \Gveniver\Template\BaseTemplate */

        return $cTpl->parse(
            array(
                 'href' => $this->_sUrl,
                 'time' => $this->_nRedirectionTime,
                 'text' => $this->_sLinkText
            )
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Sets redirection link.
     *
     * @param string  $sUrl     Url to redirect.
     * @param boolean $bReWrite Rewrite current url.
     *
     * @return void
     */
    function setUrl($sUrl, $bReWrite = true)
    {
        // Url is setted and no need to rewrite.
        if (!$bReWrite && \Gveniver\is_correct_url($this->_sUrl))
            return;

        $this->getApplication()->trace->addLine('[%s] Set url to "%s"', __CLASS__, $sUrl);

        $this->_sUrl = $sUrl;
        return;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Sets text for redirection link.
     *
     * @param string $sText Text for redirection link.
     *
     * @return void
     */
    public function setText($sText)
    {
        $this->_sLinkText = $sText;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Sets the time of waiting before redirection to another page.
     *
     * @param int $nTime Time of waiting before redirect in seconds.
     *
     * @return void
     */
    public function setRedirectionTime($nTime)
    {
        $this->_nRedirectionTime = $nTime;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for current url for redirection.
     *
     * @return string
     */
    public function getUrl()
    {
        return $this->_sUrl;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Redirects right now.
     *
     * @return void
     */
    public function redirect()
    {
        // Check correctness.
        if (!\Gveniver\is_correct_url($this->_sUrl))
            return;

        // Redirect.
        header('Location: '.$this->_sUrl);
        return;

    } // End
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------