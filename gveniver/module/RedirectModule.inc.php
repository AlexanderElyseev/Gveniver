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

namespace Gveniver\Kernel;
\Gveniver\Loader::i('Module.inc.php');

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
class RedirectModule extends Module
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

    /**
     * Data for saving session.
     * 
     * @var array
     */
    private $_aPostData = array();
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

        // Load session data.
        $this->_loadSessionData();

        $this->getApplication()->trace->addLine('[%s] Init sucessful.', __CLASS__);
        return true;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Class destructor.
     * Save session data.
     */
    public function __destruct()
    {
        $this->_saveSessionData();
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Load saved data from session.
     *
     * @return void
     */
    private function _loadSessionData()
    {
        if (!isset($_SESSION['Gveniver']['RedirectModule']['Data'])) {
            $this->getApplication()->trace->addLine('[%s] Session data is not set.', __CLASS__);
            return;
        }

        // Load.
        $aData = $_SESSION['Gveniver']['RedirectModule']['Data'];

        // Clean.
        unset($_SESSION['Gveniver']['RedirectModule']['Data']);

        // Do not save wrong data.
        if (!is_array($aData)) {
            $this->getApplication()->trace->addLine('[%s] Wrong session datat.', __CLASS__);
            return;
        }

        // Save.
        $this->_aPostData = $aData['Data'];
        return;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save post data to session.
     *
     * @return void
     */
    private function _saveSessionData()
    {
        if (!count($this->_aPostData))
            return;

        $_SESSION['Gveniver']['RedirectModule']['Data'] = $this->_aPostData;
        return;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load variable from saved in session data.
     * 
     * @param string $sName Name of variable for loading.
     *
     * @return mixed Returns null if variable if nod specified.
     */
    public function getSessionVariable($sName)
    {
        return isset($this->_aPostData[$sName]) ? $this->_aPostData[$sName] : null;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Set data for saving in session.
     *
     * @param mixed  $mData Data for save.
     * @param string $sName Name of variable for saving. If not specified,
     * replace all session data by specified.
     *
     * @return void
     */
    public function setSessionVariable($mData, $sName = null)
    {
        if (!$sName && is_array($mData))
            $this->_aPostData = $mData;
        elseif ($sName)
            $this->_aPostData[$sName] = $mData;
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Output redirection link as string.
     *
     * @return string
     */
    public function __toString()
    {
        // Do not redirect to incorrect url.
        if (!$this->_sRedirectionTemplateName || !$this->_sUrl || !$this->isCorrectUrl($this->_sUrl))
            return '';

        // Load template for redirection.
        $cTpl = null;
        if (!$this->getApplication()->template->getTemplate($this->_sRedirectionTemplateName, $cTpl))
            return '';

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
     * Set redirection link.
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
     * Redirect right now.
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