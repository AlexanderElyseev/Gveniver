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
GvInclude::instance()->includeFile('system/cache/FileSplitter.inc.php');

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
     * Array of configuration parameters for extension.
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

        $this->_aConfig['CacheScripts'] = GvKernel::toBoolean($this->cKernel->cConfig->get('Profile/CacheScript'));
        $this->_aConfig['CacheStyles'] = GvKernel::toBoolean($this->cKernel->cConfig->get('Profile/CacheStyle'));

        $this->_aConfig['UseScriptTemplate'] = GvKernel::toBoolean($this->cKernel->cConfig->get('Profile/UseScriptTemplate'));
        $this->_aConfig['UseStyleTemplate'] = GvKernel::toBoolean($this->cKernel->cConfig->get('Profile/UseStyleTemplate'));
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns list scripts for current section and action.
     *
     * @return array
     */
    public function getScripts()
    {
        // If JavaScript configuration is used.
        $sJsConfigScript = '';
        if ($this->_aConfig['UseConfigScript']) {
            $sJsConfigScript = $this->_buildScriptHtml(
                $this->cKernel->invar->getLink(
                    array(
                         $this->_aConfig['InvarSectionKey'] => $this->_aConfig['ConfigScriptSection']
                    )
                )
            );
        }

        // Load scripts data.
        $cProfile = $this->cKernel->getProfile();
        $sSectionName = $cProfile->getCurrentSectionName();
        $sActionValue = $cProfile->getCurrentAction();
        $aScriptDataList = $this->cKernel->getProfile()->getScriptList(
            $sSectionName,
            $sActionValue
        );

        // Load scripts from cache.
        if ($this->_aConfig['CacheScripts']) {
            // First, try to load scripts from cache.
            $sCacheScripts = $this->_getCacheScripts($sSectionName, $sActionValue);
            if ($sCacheScripts)
                return $sJsConfigScript.$sCacheScripts;

            // If cache not loaded, save and reload script cache.
            // This action for preventing use of not cached scripts.
            $this->_saveCacheScripts($aScriptDataList, $sSectionName, $sActionValue);
            $sCacheScripts = $this->_getCacheScripts($sSectionName, $sActionValue);
            if ($sCacheScripts)
                return $sJsConfigScript.$sCacheScripts;
        }

        // Build result list of scripts.
        $sRet = '';
        $sScriptWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsScriptWeb');
        if (is_array($aScriptDataList))
            foreach ($aScriptDataList as $aScript)
               $sRet .= $this->_buildScriptHtml($sScriptWebPath.$aScript['FileName']);

        return $sJsConfigScript.$sRet;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build HTML data for script.
     * 
     * @param string $sFileUrl Url to script file.
     *
     * @return string
     */
    private function _buildScriptHtml($sFileUrl)
    {
        // Try to load file template for output scripts.
        if ($this->_aConfig['UseScriptTemplate']) {
            $sScriptTpl = $this->cKernel->template->getTemplate('cms_html_script');
            if ($sScriptTpl) {
                return $sScriptTpl->parse(array('file' => $sFileUrl));
            } else {
                $this->cKernel->trace->addLine(
                    '[%s] Html template for scripts not found. Use default tempate.',
                    __CLASS__
                );
            }
        }
        
        // Use default template for output scripts.
        return '<script type="text/javascript" src="'.$sFileUrl.'"></script>';

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load cached scripts.
     * Returns html code for connecting to cached scripts.
     *
     * @param string $sSectionName Name of current section for load cached scripts.
     * @param string $sActionValue Current value of action.
     *
     * @return string Html code for cached scripts for this section or null on error.
     */
    private function _getCacheScripts($sSectionName, $sActionValue)
    {
        try {
            $sCacheAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsCache');
            $sScriptCacheWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsCacheWeb');
            $sCacheFile = $this->_buildScriptCacheFileName($sSectionName, $sActionValue);

            // Check script cache.
            if (!FileSplitter::isCorrectCache($sCacheAbsPath.$sCacheFile))
                return null;

            return $this->_buildScriptHtml($sScriptCacheWebPath.$sCacheFile);

        } catch (Exception $cEx) {
            $this->cKernel->trace->addLine('[%s] Exception: %s.', __CLASS__, $cEx->getMessage());
        }

        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save scripts of section to single cache script.
     *
     * @param array  $aList        List of scripts data for cache.
     * @param string $sSectionName Name of current section for load cached scripts.
     * @param string $sActionValue Current value of action.
     *
     * @return void
     */
    private function _saveCacheScripts($aList, $sSectionName, $sActionValue)
    {
        if (!is_array($aList) || count($aList) == 0)
            return;

        try {
            $sScriptAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsScript');
            $sCacheAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsCache');
            $sCacheFile = $this->_buildScriptCacheFileName($sSectionName, $sActionValue);
            $cCacheSplitter = new FileSplitter($sCacheAbsPath.$sCacheFile);
            foreach ($aList as $aScript)
                $cCacheSplitter->addFile($sScriptAbsPath.$aScript['FileName']);

            $cCacheSplitter->Save();

        } catch (Exception $cEx) {
            $this->cKernel->trace->addLine('[%s] Exception: %s.', __CLASS__, $cEx->getMessage());
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build file name for cache script file.
     *
     * @param string $sSectionName Section name of cache script.
     * @param string $sActionValue Action name of cache script.
     *
     * @return string
     */
    private function _buildScriptCacheFileName($sSectionName, $sActionValue)
    {
        return 'script-'.md5($sSectionName.$sActionValue).'.js';

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns html code for styles for current page.
     *
     * @return string
     */
    public function getStyles()
    {
        // Load data of profile.
        $cProfile = $this->cKernel->getProfile();
        $sSectionName = $cProfile->getCurrentSectionName();
        $sActionValue = $cProfile->getCurrentAction();
        $aStyleList = $cProfile->getStyleList(
            $sSectionName,
            $sActionValue
        );

        // Load styles from cache.
        if ($this->_aConfig['CacheStyles']) {
            $sCacheStyles = $this->_getCacheStyles($aStyleList, $sSectionName, $sActionValue);
            if ($sCacheStyles)
                return $sCacheStyles;

            // If cache not loaded, save and reload style cache.
            // This action for preventing use of not cached styles.
            $this->_saveCacheStyles($aStyleList, $sSectionName, $sActionValue);
            $sCacheStyles = $this->_getCacheStyles($aStyleList, $sSectionName, $sActionValue);
            if ($sCacheStyles)
                return $sCacheStyles;
        }

        // Build result list of styles.
        $sRet = '';
        $sStyleWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsStyleWeb');
        if (is_array($aStyleList)) {
            foreach ($aStyleList as $aStyle) {
                $sRet .= $this->_buildStyleHtml(
                    $sStyleWebPath.$aStyle['FileName'],
                    isset($aStyle['Condition']) ? $aStyle['Condition'] : null
                );
            }
        }
        
        return $sRet;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build HTML data for CSS styles.
     *
     * @param string $sFileUrl   Url to CSS style file.
     * @param string $sCondition Condition for CSS conditional comment.
     *
     * @return string
     */
    private function _buildStyleHtml($sFileUrl, $sCondition = null)
    {
        // Try to load file template for output styles.
        if ($this->_aConfig['UseStyleTemplate']) {
            $sStyleTpl = $this->cKernel->template->getTemplate('cms_html_style');
            if ($sStyleTpl) {
                return $sStyleTpl->parse(
                    array('file' => $sFileUrl, 'condition' => $sCondition)
                );
            } else {
                $this->cKernel->trace->addLine(
                    '[%s] Html template for styles not found. Use default tempate.',
                    __CLASS__
                );
            }
        }

        // Use default template for output styles.
        $sRet = '<link rel="stylesheet" type="text/css" href="'.$sFileUrl.'" />';
        if ($sCondition)
            $sRet = '<!--[if {[$condition]}]>{[/if]}'.$sRet.'<![endif]-->';
        return $sRet;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load styles from cahce.
     * Returns html code for connecting to cached styles.
     *
     * @param array  $aList        List of styles in section.
     * @param string $sSectionName Section name.
     * @param string $sActionValue Current value of action.
     *
     * @return string Html code with list of cached styles of null on error.
     */
    private function _getCacheStyles($aList, $sSectionName, $sActionValue)
    {
        try {
            $sCacheAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsCache');
            $sStyleCacheWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsCacheWeb');

            // Group styles by condition.
            $aVariousConditions = array();
            foreach ($aList as $aStyle)
                $aVariousConditions[isset($aStyle['Condition']) ? $aStyle['Condition'] : ''][] = $aStyle;

            $sRet = '';
            foreach ($aVariousConditions as $sCondition => $aSameConditions) {
                $sCacheFile = $this->_buildStyleCacheFileName($sSectionName, $sActionValue, $sCondition);
                if (!FileSplitter::isCorrectCache($sCacheAbsPath.$sCacheFile))
                    return null;

                $sRet .= $this->_buildStyleHtml($sStyleCacheWebPath.$sCacheFile, $sCondition);

            } // End foreach

            return $sRet;

        } catch (Exception $cEx) {
            $this->cKernel->trace->addLine('[%s] Exception: %s.', __CLASS__, $cEx->getMessage());
        }

        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save cache of styles to single style file for each condition.
     *
     * @param array  $aList        List of styles for cache.
     * @param string $sSectionName Current section name for saving style cache.
     * @param string $sActionValue Current value of action.
     *
     * @return void
     */
    private function _saveCacheStyles($aList, $sSectionName, $sActionValue)
    {
        if (!is_array($aList) || count($aList) == 0)
            return;

        try {
            $sStyleAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsStyle');
            $sCacheAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsCache');

            // Group styles by condition.
            $aVariousConditions = array();
            foreach ($aList as $aStyle)
                $aVariousConditions[isset($aStyle['Condition']) ? $aStyle['Condition'] : ''][] = $aStyle;

            // Save each group of styles.
            foreach ($aVariousConditions as $sCondition => $aSameConditions) {
                $sCacheFile = $this->_buildStyleCacheFileName($sSectionName, $sActionValue, $sCondition);
                $cCacheSplitter = new FileSplitter($sCacheAbsPath.$sCacheFile);
                foreach ($aSameConditions as $aSameConditionStyle)
                    $cCacheSplitter->addFile($sStyleAbsPath.$aSameConditionStyle['FileName']);

                $cCacheSplitter->Save();

            } // End foreach

        } catch (Exception $cEx) {
            $this->cKernel->trace->addLine('[%s] Exception: %s.', __CLASS__, $cEx->getMessage());
        }

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build file name for cache style file.
     *
     * @param string $sSectionName Section name of cache style.
     * @param string $sActionValue Action name of cache style.
     * @param string $sCondition   Condition for cache style.
     *
     * @return string
     */
    private function _buildStyleCacheFileName($sSectionName, $sActionValue, $sCondition)
    {
        return 'style-'.md5($sSectionName.$sActionValue.$sCondition).'.css';
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns title of current section with specified action from profile
     * configuration.
     *
     * @return string
     */
    public function getTitle()
    {
        $cProfile = $this->cKernel->getProfile();
        return $cProfile->getTitle(
            $cProfile->getCurrentSectionName(),
            $cProfile->getCurrentAction()
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns subtitle of current section with specified action from profile
     * configuration.
     *
     * @return string
     */
    public function getSubTitle()
    {
        $cProfile = $this->cKernel->getProfile();
        return $cProfile->getSubTitle(
            $cProfile->getCurrentSectionName(),
            $cProfile->getCurrentAction()
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns content-type of current section with specified action from profile
     * configuration.
     *
     * @return string
     */
    public function getContentType()
    {
        $cProfile = $this->cKernel->getProfile();
        return $cProfile->getContentType(
            $cProfile->getCurrentSectionName(),
            $cProfile->getCurrentAction()
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns keywords for current section with specified action from profile
     * configuration.
     *
     * @return string
     */
    public function getKeywords()
    {
        $cProfile = $this->cKernel->getProfile();
        return $cProfile->getKeywords(
            $cProfile->getCurrentSectionName(),
            $cProfile->getCurrentAction()
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns author of current section with specified action from profile
     * configuration.
     *
     * @return string
     */
    public function getAuthor()
    {
        $cProfile = $this->cKernel->getProfile();
        return $cProfile->getAuthor(
            $cProfile->getCurrentSectionName(),
            $cProfile->getCurrentAction()
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns information for robots of current section with specified action from
     * profile configuration.
     *
     * @return string
     */
    public function getRobots()
    {
        $cProfile = $this->cKernel->getProfile();
        return $cProfile->getRobots(
            $cProfile->getCurrentSectionName(),
            $cProfile->getCurrentAction()
        );

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------