<?php
/**
 * File contains kernel extension for access to profile data.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

GvInclude::instance()->includeFile('system/extension/SimpleExtension.inc.php');

/**
 * Kernel extension class for access to profile data.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class GvProfileExt extends SimpleExtenson
{
    /**
     * Array of configuration parameters.
     *
     * @var boolean
     */
    private $_aConfig = false;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Overloaded class constructor.
     *
     * @param GvKernel $cKernel Current kernel.
     */
    public function __construct(GvKernel $cKernel)
    {
        // Base parent constructor.
        parent::__construct($cKernel);

        // Configuration parameters.
        $this->_aConfig['UseConfigScript'] = GvKernel::toBoolean($this->cKernel->cConfig->get('Kernel/UseConfigScript'));
        $this->_aConfig['ConfigScriptSection'] = $this->cKernel->cConfig->get('Kernel/ConfigScriptSection');
        $this->_aConfig['InvarSectionKey'] = $this->cKernel->cConfig->get('Kernel/InvarSectionKey');

        $this->_aConfig['CacheScripts'] = GvKernel::toBoolean($this->cKernel->cConfig->get('Cache/Scripts'));
        $this->_aConfig['CacheStyles'] = GvKernel::toBoolean($this->cKernel->cConfig->get('Cache/Styles'));

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns html code for scripts of current section.
     *
     * @return string
     */
    public function getScripts()
    {
        // If JavaScript configuration is used.
        $sScriptTpl = $this->cKernel->template->getTemplate('cms_html_script');
        $sJsConfigScript = '';
        if ($this->_aConfig['UseConfigScript']) {
            $aLinkData = array($this->_aConfig['InvarSectionKey'] => $this->_aConfig['ConfigScriptSection']);
            $aTplData = array('splitter' => $this->cKernel->invar->getLink($aLinkData));
            $sJsConfigScript = $this->cKernel->template->parseTemplate($sScriptTpl, $aTplData);
        }

        // Load scripts data.
        $sSectionName = $this->cKernel->getProfile()->getCurrentSectionName();
        $aScriptDataList = $this->cKernel->getProfile()->getScriptList($sSectionName);

        // Load scripts from cache.
        if ($this->_aConfig['CacheScripts']) {
            // First, try to load scripts from cache.
            $sCacheScripts = $this->_getCacheScripts($sSectionName);
            if ($sCacheScripts)
                return $sJsConfigScript.$sCacheScripts;

            // If cache not loaded, save and reload script cache.
            // This action for preventing use of not cached scripts.
            $this->_saveCacheScripts($aScriptDataList, $sSectionName);
            $sCacheScripts = $this->_getCacheScripts($sSectionName);
            if ($sCacheScripts)
                return $sJsConfigScript.$sCacheScripts;
        }

        // Build result list of scripts.
        $sRet = '';
        $sScriptWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsScriptWeb');
        if (is_array($aScriptDataList))
            foreach ($aScriptDataList as $aScript)
                $sRet .= $this->cKernel->template->parseTemplate($sScriptTpl, array('splitter' => $sScriptWebPath.$aScript['FileName']));

        return $sJsConfigScript.$sRet;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load cached scripts.
     * Returns html code for connecting to cached scripts.
     *
     * @param string $sSectionName Name of current section for load cached scripts.
     *
     * @return string Html code for cached scripts for this section or null on error.
     */
    private function _getCacheScripts($sSectionName)
    {
        try {
            $sCacheAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsCache');
            $sScriptCacheWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsCacheWeb');
            $sCacheFile = 'script-'.md5($sSectionName).'.js';

            // Check script cache.
            if (!FileSplitter::isCorrectCache($sCacheAbsPath.$sCacheFile))
                return null;

            return $this->cKernel->template->parseTemplateFile('cms_html_script', array('splitter' => $sScriptCacheWebPath.$sCacheFile));

        } catch (Exception $cEx) {
            $this->cKernel->trace->addLine('[HeaderExt] Eception. '.$cEx);
        }

        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save scripts of section to single cache script.
     *
     * @param array  $aList        List of scripts data for cache.
     * @param string $sSectionName Name of current section for load cached scripts.
     *
     * @return void
     */
    private function _saveCacheScripts($aList, $sSectionName)
    {
        try {
            $sScriptAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsScript');
            $sCacheAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsCache');
            $sCacheFile = 'script-'.md5($sSectionName).'.js';
            $cCacheSplitter = new FileSplitter($sCacheAbsPath.$sCacheFile);
            foreach ($aList as $aScript)
                $cCacheSplitter->addFile($sScriptAbsPath.$aScript['FileName']);

            $cCacheSplitter->Save();

        } catch (Exception $cEx) {
            $this->cKernel->trace->addLine('[HeaderExt] Eception. '.$cEx);
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns html code for styles for current page.
     *
     * @return string
     */
    public function getStyles()
    {
        // TODO
        $sSectionName = $this->cKernel->getProfile()->getCurrentSectionName();
        $aStyleDataList = $this->cKernel->getProfile()->getStyleList($sSectionName);

        // Load styles from cache.
        if ($this->_aConfig['CacheStyles']) {
            $sCacheStyles = $this->_getCacheStyles($aStyleDataList, $sSectionName);
            if ($sCacheStyles)
                return $sCacheStyles;

            // If cache not loaded, save and reload style cache.
            // This action for preventing use of not cached styles.
            $this->_saveCacheStyles($aStyleDataList, $sSectionName);
            $sCacheStyles = $this->_getCacheStyles($aStyleDataList, $sSectionName);
            if ($sCacheStyles)
                return $sCacheStyles;
        }

        // Build result list of styles.
        $sRet = '';
        $sStyleWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsStyleWeb');
        $sStyleTpl = $this->cKernel->template->getTemplate('cms_html_style');
        if (is_array($aStyleDataList)) {
            foreach ($aStyleDataList as $aStyle) {
                $aTplData = array('splitter' => $sStyleWebPath.$aStyle['FileName']);
                if (isset($aStyle['Condition']))
                    $aTplData['condition'] = $aStyle['Condition'];

                $sRet .= $this->cKernel->template->parseTemplate($sStyleTpl, $aTplData);
            }
        }
        return $sRet;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load styles from cahce.
     * Returns html code for connecting to cached styles.
     *
     * @param array  $aList        List of styles in section.
     * @param string $sSectionName Section name.
     *
     * @return string Html code with list of cached styles of null on error.
     */
    private function _getCacheStyles($aList, $sSectionName)
    {
        try {
            $sCacheAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsCache');
            $sStyleCacheWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsCacheWeb');

            // Group styles by condition.
            $aVariousConditions = array();
            foreach ($aList as $aStyle)
                $aVariousConditions[isset($aStyle['Condition']) ? $aStyle['Condition'] : ''][] = $aStyle;

            $sRet = '';
            $sStyleTpl = $this->cKernel->template->getTemplate('cms_html_style');
            foreach ($aVariousConditions as $sCondition => $aSameConditions) {
                $sCacheFile = 'style-'.md5($sSectionName.$sCondition).'.css';
                if (!FileSplitter::isCorrectCache($sCacheAbsPath.$sCacheFile))
                    return null;

                $aTplData = array('splitter' => $sStyleCacheWebPath.$sCacheFile, 'condition' => $sCondition);
                $sRet .= $this->cKernel->template->parseTemplate($sStyleTpl, $aTplData);

            } // End foreach

            return $sRet;

        } catch (Exception $cEx) {
            $this->cKernel->trace->addLine('[HeaderExt] Eception. '.$cEx);
        }

        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save cache of styles to single style splitter for each condition.
     *
     * @param array  $aList        List of styles for cache.
     * @param string $sSectionName Current section name for saving style cache.
     *
     * @return void
     */
    private function _saveCacheStyles($aList, $sSectionName)
    {
        try {
            $sStyleAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsStyle');
            $sCacheAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsCache');

            // Group styles by condition.
            $aVariousConditions = array();
            foreach ($aList as $aStyle)
                $aVariousConditions[isset($aStyle['Condition']) ? $aStyle['Condition'] : ''][] = $aStyle;

            // Save each group of styles.
            foreach ($aVariousConditions as $sCondition => $aSameConditions) {
                $sCacheFile = 'style-'.md5($sSectionName.$sCondition).'.css';
                $cCacheSplitter = new FileSplitter($sCacheAbsPath.$sCacheFile);
                foreach ($aSameConditions as $aSameConditionStyle)
                    $cCacheSplitter->addFile($sStyleAbsPath.$aSameConditionStyle['FileName']);

                $cCacheSplitter->Save();

            } // End foreach

        } catch (Exception $cEx) {
            $this->cKernel->trace->addLine('[HeaderExt] Eception. '.$cEx);
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns html code for title of current page.
     *
     * @return string
     */
    public function getTitle()
    {
        // TODO
        $sSectionName = $this->cKernel->getProfile()->getCurrentSectionName();
        $sAct = InvarsModule::instance()->getVariable('act');

        // Build template data.
        $cPd = $this->cKernel->getProfile();
        $aTplData = array(
            'title'    => $cPd->getTitle($sSectionName, $sAct),
            'subtitle' => $cPd->getSubTitle($sSectionName, $sAct)
        );

        return $this->cKernel->template->parseTemplateFile('cms_header_title', $aTplData);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns content type of current page.
     *
     * @return string
     */
    public function getContentType()
    {
        return $this->cKernel->getProfile()->getContentType($this->cKernel->getProfile()->getCurrentSectionName());

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns keywords for current page.
     *
     * @return string
     */
    public function getKeywords()
    {
        return $this->cKernel->getProfile()->getKeywords($this->cKernel->getProfile()->getCurrentSectionName());

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns author of current section from profile configuration.
     *
     * @return string
     */
    public function getAuthor()
    {
        return $this->cKernel->getProfile()->getAuthor($this->cKernel->getProfile()->getCurrentSectionName());

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns information for robots of current section from profile configuration.
     *
     * @return string
     */
    public function getRobots()
    {
        return $this->cKernel->getProfile()->getRobots($this->cKernel->getProfile()->getCurrentSectionName());

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------