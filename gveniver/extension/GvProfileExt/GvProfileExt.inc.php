<?php
/**
 * File contains extension for access to profile data.
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
\Gveniver\Loader::i('system/cache/FileSplitter.inc.php');
\Gveniver\Loader::i('system/cache/packer/StylePacker.inc.php');
\Gveniver\Loader::i('system/cache/packer/ScriptPacker.inc.php');

/**
 * Extension class for access to profile data.
 *
 * @category  Gveniver
 * @package   Extension
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class GvProfileExt extends SimpleExtension
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
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication)
    {
        // Base parent constructor.
        parent::__construct($cApplication);

        // Configuration parameters.
        $this->_aConfig['UseConfigScript'] = \Gveniver\Kernel\Application::toBoolean($this->getApplication()->getConfig()->get('Kernel/UseConfigScript'));
        $this->_aConfig['ConfigScriptSection'] = $this->getApplication()->getConfig()->get('Kernel/ConfigScriptSection');
        $this->_aConfig['InvarSectionKey'] = $this->getApplication()->getConfig()->get('Kernel/InvarSectionKey');

        $this->_aConfig['CacheScripts'] = \Gveniver\Kernel\Application::toBoolean($this->getApplication()->getConfig()->get('Profile/CacheScript'));
        $this->_aConfig['CacheStyles'] = \Gveniver\Kernel\Application::toBoolean($this->getApplication()->getConfig()->get('Profile/CacheStyle'));

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns configuration parameters.
     *
     * @param string $sPath Path to configuration parameter.
     *
     * @return string|null
     */
    public function getConfigVariable($sPath)
    {
        if ($sPath)
            return $this->getApplication()->getConfig()->get($sPath);

        return null;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns result of parsing template for current action.
     *
     * @return string|null Result of parsing or null on error.
     */
    public function parseActTemplate()
    {
        $cProfile = $this->getApplication()->getProfile();
        $sSectionName = $cProfile->getCurrentSectionName();
        $sActionValue = $cProfile->getCurrentAction();
        $sTemplateName = $cProfile->getActTemplate($sSectionName, $sActionValue);
        if ($sTemplateName)
            return  $this->getApplication()->template->parseTemplate($sTemplateName);

        return null;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Parse template by name.
     *
     * @param string $sTemplateName Name of template for parsing.
     * @param array  $aParams       Parameters fot parsing template.
     *
     * @return string|null  Result of parsing or null on error.
     */
    public function parseTemplate($sTemplateName, $aParams = array())
    {
        return $sTemplateName
            ? $this->getApplication()->template->parseTemplate($sTemplateName, $aParams)
            : null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns list scripts for current section and action.
     *
     * @return array
     */
    public function getScripts()
    {
        $aRet = array();

        // Add JavaScript configuration.
        if ($this->_aConfig['UseConfigScript'])
            $aRet[] = array(
                'WebFileName' => $this->getApplication()->invar->getLink(
                    array($this->_aConfig['InvarSectionKey'] => $this->_aConfig['ConfigScriptSection'])
                )
            );

        $cProfile = $this->getApplication()->getProfile();
        $sSectionName = $cProfile->getCurrentSectionName();
        $sActionValue = $cProfile->getCurrentAction();

        // Load scripts from cache.
        if ($this->_aConfig['CacheScripts']) {
            // First, try to load scripts from cache.
            $aCacheScripts = $this->_getCacheScripts($sSectionName, $sActionValue);
            if ($aCacheScripts)
                return array_merge($aRet, $aCacheScripts);

            // If cache not loaded, save and reload script cache.
            // This action for prevent using of not cached scripts.
            $this->_saveCacheScripts(
                $this->_buildScriptList($sSectionName, $sActionValue),
                $sSectionName,
                $sActionValue
            );
            $aCacheScripts = $this->_getCacheScripts($sSectionName, $sActionValue);
            if ($aCacheScripts)
                return array_merge($aRet, $aCacheScripts);
        }

        // Load scripts without cache.
        return array_merge($aRet, $this->_buildScriptList($sSectionName, $sActionValue));

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build list of scripts from profile and from parent profiles.
     * Scripts of child clubs ovverides scripts of parent clubs by file name.
     *
     * Duplicate scripts cheks by absolute web path.
     *
     * @param string $sSectionName Name of section for load list of scripts.
     * @param string $sActionValue Value of action for load list of scripts.
     *
     * @return array
     */
    private function _buildScriptList($sSectionName, $sActionValue)
    {
        $aScriptNames = array();

        // For each profile, build list of scripts.
        $aUniqueScripts = array();
        foreach (array_reverse($this->getApplication()->getProfile()->getParentProfileList()) as $cProfile) {
            /* @var $cProfile \Gveniver\Kernel\Profile */

            // Load scripts data from profile configuration.
            $aScriptDataList = $cProfile->getScriptList($sSectionName, $sActionValue);

            // Set absolute path to scripts.
            $sScriptAbsPath = $cProfile->getConfig()->get('Profile/Path/AbsScript');
            $sScriptWebPath = $cProfile->getConfig()->get('Profile/Path/AbsScriptWeb');
            foreach ($aScriptDataList as $aScript) {
                
                // Check duplicate styles by absolute web path.
                $sAbsWebPath = $sScriptWebPath.$aScript['FileName'];
                if (in_array($sAbsWebPath, $aUniqueScripts))
                    continue;

                $aUniqueScripts[] = $sAbsWebPath;
                $aScript['WebFileName'] = $sAbsWebPath;
                $aScript['AbsFileName'] = $sScriptAbsPath.$aScript['FileName'];
                $aScriptNames[$aScript['FileName']] = $aScript;

            } // End foreach

        } // End foreach

        return array_values($aScriptNames);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load cached scripts.
     * Returns html code for connecting to cached scripts.
     *
     * @param string $sSectionName Name of current section for load cached scripts.
     * @param string $sActionValue Current value of action.
     *
     * @return array|null List of cached scripts for this section and action or null on error.
     */
    private function _getCacheScripts($sSectionName, $sActionValue)
    {
        try {
            // Check script cache.
            $sCacheAbsPath = $this->getApplication()->getConfig()->get('Profile/Path/AbsCache');
            $sCacheFile = $this->_buildScriptCacheFileName($sSectionName, $sActionValue);
            if (!\Gveniver\Cache\FileSplitter::isCorrectCache($sCacheAbsPath.$sCacheFile))
                return null;

            $sScriptCacheWebPath = $this->getApplication()->getConfig()->get('Profile/Path/AbsCacheWeb');
            return array(
                array('WebFileName' => $sScriptCacheWebPath.$sCacheFile)
            );

        } catch (\Gveniver\Exception\Exception $cEx) {
            $this->getApplication()->trace->addLine('[%s] Exception: %s.', __CLASS__, $cEx->getMessage());
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
            $sCacheAbsPath = $this->getApplication()->getConfig()->get('Profile/Path/AbsCache');
            $sCacheFile = $this->_buildScriptCacheFileName($sSectionName, $sActionValue);
            
            $cCacheSplitter = new \Gveniver\Cache\FileSplitter(
                $sCacheAbsPath.$sCacheFile,
                new \Gveniver\Cache\ScriptPacker()
            );
            foreach ($aList as $aScript)
                $cCacheSplitter->addFile($aScript['AbsFileName']);

            $cCacheSplitter->save();

        } catch (\Gveniver\Exception\Exception $cEx) {
            $this->getApplication()->trace->addLine('[%s] Exception: %s.', __CLASS__, $cEx->getMessage());
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
     * Returns list of styles for current page.
     * 
     * @return array
     */
    public function getStyles()
    {
        // Load data from profile configuration.
        $cProfile = $this->getApplication()->getProfile();
        $sSectionName = $cProfile->getCurrentSectionName();
        $sActionValue = $cProfile->getCurrentAction();

        // Load styles from cache.
        if ($this->_aConfig['CacheStyles']) {
            $aCacheStyles = $this->_getCacheStyles(
                $this->_buildStyleList($sSectionName, $sActionValue),
                $sSectionName,
                $sActionValue
            );
            if ($aCacheStyles)
                return $aCacheStyles;

            // If cache not loaded, save and reload style cache.
            // This action for preventing use of not cached styles.
            $this->_saveCacheStyles(
                $this->_buildStyleList($sSectionName, $sActionValue),
                $sSectionName,
                $sActionValue
            );
            $aCacheStyles = $this->_getCacheStyles(
                $this->_buildStyleList($sSectionName, $sActionValue),
                $sSectionName,
                $sActionValue
            );
            if ($aCacheStyles)
                return $aCacheStyles;
        }

        // Load styles without cache.
        return $this->_buildStyleList($sSectionName, $sActionValue);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build list of styles from profile and from parent profiles.
     * Styles of child clubs ovverides styles of parent clubs by file name.
     *
     * Duplicate styles cheks by absolute web path.
     *
     * @param string $sSectionName Name of section for load list of styles.
     * @param string $sActionValue Value of action for load list of styles.
     *
     * @return array
     */
    private function _buildStyleList($sSectionName, $sActionValue)
    {
        $aStyleNames = array();

        // For each profile, build list of styles.
        $aUniqueStyles = array();
        foreach (array_reverse($this->getApplication()->getProfile()->getParentProfileList()) as $cProfile) {
            /* @var $cProfile \Gveniver\Kernel\Profile */

            // Load styles data from profile configuration.
            $aStyleDataList = $cProfile->getStyleList($sSectionName, $sActionValue);

            // Set absolute path to styles.
            $sStyleAbsPath = $cProfile->getConfig()->get('Profile/Path/AbsStyle');
            $sStyleWebPath = $cProfile->getConfig()->get('Profile/Path/AbsStyleWeb');
            foreach ($aStyleDataList as $aStyle) {

                // Check duplicate styles by absolute web path.
                $sAbsWebPath = $sStyleWebPath.$aStyle['FileName'];
                if (in_array($sAbsWebPath, $aUniqueStyles))
                    continue;

                $aUniqueStyles[] = $sAbsWebPath;
                $aStyle['WebFileName'] = $sAbsWebPath;
                $aStyle['AbsFileName'] = $sStyleAbsPath.$aStyle['FileName'];
                $aStyle['Condition'] = isset($aStyle['Condition']) ? $aStyle['Condition'] : null;
                $aStyleNames[$aStyle['FileName']] = $aStyle;

            } // End foreach

        } // End foreach

        return array_values($aStyleNames);

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
     * @return array|null List of cached styles or null on error.
     */
    private function _getCacheStyles($aList, $sSectionName, $sActionValue)
    {
        try {
            $sCacheAbsPath = $this->getApplication()->getConfig()->get('Profile/Path/AbsCache');
            $sStyleCacheWebPath = $this->getApplication()->getConfig()->get('Profile/Path/AbsCacheWeb');

            // Group styles by condition.
            $aVariousConditions = array();
            foreach ($aList as $aStyle)
                $aVariousConditions[isset($aStyle['Condition']) ? $aStyle['Condition'] : ''][] = $aStyle;

            // Build result list.
            $aRet = array();
            foreach ($aVariousConditions as $sCondition => $aSameConditions) {
                $sCacheFile = $this->_buildStyleCacheFileName($sSectionName, $sActionValue, $sCondition);
                if (!\Gveniver\Cache\FileSplitter::isCorrectCache($sCacheAbsPath.$sCacheFile))
                    return null;

                $aRet[] = array(
                    'WebFileName'  => $sStyleCacheWebPath.$sCacheFile,
                    'Condition' => $sCondition
                );

            } // End foreach

            return $aRet;

        } catch (\Gveniver\Exception\Exception $cEx) {
            $this->getApplication()->trace->addLine('[%s] Exception: %s.', __CLASS__, $cEx->getMessage());
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
            $sCacheAbsPath = $this->getApplication()->getConfig()->get('Profile/Path/AbsCache');

            // Group styles by condition.
            $aVariousConditions = array();
            foreach ($aList as $aStyle)
                $aVariousConditions[isset($aStyle['Condition']) ? $aStyle['Condition'] : ''][] = $aStyle;

            // Save each group of styles.
            foreach ($aVariousConditions as $sCondition => $aSameConditions) {
                $sCacheFile = $this->_buildStyleCacheFileName($sSectionName, $sActionValue, $sCondition);
                $cCacheSplitter = new \Gveniver\Cache\FileSplitter(
                    $sCacheAbsPath.$sCacheFile,
                    new \Gveniver\Cache\StylePacker()
                );
                foreach ($aSameConditions as $aSameConditionStyle)
                    $cCacheSplitter->addFile($aSameConditionStyle['AbsFileName']);

                $cCacheSplitter->save();

            } // End foreach

        } catch (\Gveniver\Exception\Exception $cEx) {
            $this->getApplication()->trace->addLine('[%s] Exception: %s.', __CLASS__, $cEx->getMessage());
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
        $cProfile = $this->getApplication()->getProfile();
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
        $cProfile = $this->getApplication()->getProfile();
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
        $cProfile = $this->getApplication()->getProfile();
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
        $cProfile = $this->getApplication()->getProfile();
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
        $cProfile = $this->getApplication()->getProfile();
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
        $cProfile = $this->getApplication()->getProfile();
        return $cProfile->getRobots(
            $cProfile->getCurrentSectionName(),
            $cProfile->getCurrentAction()
        );

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------