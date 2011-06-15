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

namespace Gveniver\Extension;
\Gveniver\Loader::i('system/extension/SimpleExtension.inc.php');
\Gveniver\Loader::i('system/cache/FileSplitter.inc.php');
\Gveniver\Loader::i('system/cache/packer/StylePacker.inc.php');
\Gveniver\Loader::i('system/cache/packer/ScriptPacker.inc.php');

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
     * @param \Gveniver\Kernel\Kernel $cKernel Current kernel.
     */
    public function __construct(\Gveniver\Kernel\Kernel $cKernel)
    {
        // Base parent constructor.
        parent::__construct($cKernel);

        // Configuration parameters.
        $this->_aConfig['UseConfigScript'] = \Gveniver\Kernel\Kernel::toBoolean($this->cKernel->cConfig->get('Kernel/UseConfigScript'));
        $this->_aConfig['ConfigScriptSection'] = $this->cKernel->cConfig->get('Kernel/ConfigScriptSection');
        $this->_aConfig['InvarSectionKey'] = $this->cKernel->cConfig->get('Kernel/InvarSectionKey');

        $this->_aConfig['CacheScripts'] = \Gveniver\Kernel\Kernel::toBoolean($this->cKernel->cConfig->get('Profile/CacheScript'));
        $this->_aConfig['CacheStyles'] = \Gveniver\Kernel\Kernel::toBoolean($this->cKernel->cConfig->get('Profile/CacheStyle'));

        $this->_aConfig['UseScriptTemplate'] = \Gveniver\Kernel\Kernel::toBoolean($this->cKernel->cConfig->get('Profile/UseScriptTemplate'));
        $this->_aConfig['UseStyleTemplate'] = \Gveniver\Kernel\Kernel::toBoolean($this->cKernel->cConfig->get('Profile/UseStyleTemplate'));
        
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
                'FileName' => $this->cKernel->invar->getLink(
                    array($this->_aConfig['InvarSectionKey'] => $this->_aConfig['ConfigScriptSection'])
                )
            );

        // Load scripts data from profile configuration.
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
            $aCacheScripts = $this->_getCacheScripts($sSectionName, $sActionValue);
            if ($aCacheScripts)
                return array_merge($aRet, $aCacheScripts);

            // If cache not loaded, save and reload script cache.
            // This action for prevent using of not cached scripts.
            $this->_saveCacheScripts($aScriptDataList, $sSectionName, $sActionValue);
            $aCacheScripts = $this->_getCacheScripts($sSectionName, $sActionValue);
            if ($aCacheScripts)
                return array_merge($aRet, $aCacheScripts);
        }

        // Set absolute path to scripts.
        $sScriptWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsScriptWeb');
        foreach ($aScriptDataList as &$aScript) {
            $aScript['FileName'] = $sScriptWebPath.$aScript['FileName'];
        }

        return array_merge($aRet, $aScriptDataList);

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
            $sCacheAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsCache');
            $sCacheFile = $this->_buildScriptCacheFileName($sSectionName, $sActionValue);
            if (!\Gveniver\FileSplitter::isCorrectCache($sCacheAbsPath.$sCacheFile))
                return null;

            $sScriptCacheWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsCacheWeb');
            return array(
                array('FileName' => $sScriptCacheWebPath.$sCacheFile)
            );

        } catch (\Gveniver\Exception\Exception $cEx) {
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
            $cCacheSplitter = new \Gveniver\FileSplitter($sCacheAbsPath.$sCacheFile, new \Gveniver\ScriptPacker());
            foreach ($aList as $aScript)
                $cCacheSplitter->addFile($sScriptAbsPath.$aScript['FileName']);

            $cCacheSplitter->save();

        } catch (\Gveniver\Exception\Exception $cEx) {
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
     * Returns list of styles for current page.
     *
     * @return array
     */
    public function getStyles()
    {
        // Load data from profile configuration.
        $cProfile = $this->cKernel->getProfile();
        $sSectionName = $cProfile->getCurrentSectionName();
        $sActionValue = $cProfile->getCurrentAction();
        $aStyleList = $cProfile->getStyleList(
            $sSectionName,
            $sActionValue
        );

        // Load styles from cache.
        if ($this->_aConfig['CacheStyles']) {
            $aCacheStyles = $this->_getCacheStyles($aStyleList, $sSectionName, $sActionValue);
            if ($aCacheStyles)
                return $aCacheStyles;

            // If cache not loaded, save and reload style cache.
            // This action for preventing use of not cached styles.
            $this->_saveCacheStyles($aStyleList, $sSectionName, $sActionValue);
            $aCacheStyles = $this->_getCacheStyles($aStyleList, $sSectionName, $sActionValue);
            if ($aCacheStyles)
                return $aCacheStyles;
        }

        // Build result list of styles.
        $aRet = array();
        $sStyleWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsStyleWeb');
        if (is_array($aStyleList)) {
            foreach ($aStyleList as $aStyle) {
                $aRet[] = array(
                    'FileName'  => $sStyleWebPath.$aStyle['FileName'],
                    'Condition' => isset($aStyle['Condition']) ? $aStyle['Condition'] : null
                );
            }
        }
        
        return $aRet;

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
            $sCacheAbsPath = $this->cKernel->cConfig->get('Profile/Path/AbsCache');
            $sStyleCacheWebPath = $this->cKernel->cConfig->get('Profile/Path/AbsCacheWeb');

            // Group styles by condition.
            $aVariousConditions = array();
            foreach ($aList as $aStyle)
                $aVariousConditions[isset($aStyle['Condition']) ? $aStyle['Condition'] : ''][] = $aStyle;

            // Build result list.
            $aRet = array();
            foreach ($aVariousConditions as $sCondition => $aSameConditions) {
                $sCacheFile = $this->_buildStyleCacheFileName($sSectionName, $sActionValue, $sCondition);
                if (!\Gveniver\FileSplitter::isCorrectCache($sCacheAbsPath.$sCacheFile))
                    return null;

                $aRet[] = array(
                    'FileName'  => $sStyleCacheWebPath.$sCacheFile,
                    'Condition' => $sCondition
                );

            } // End foreach

            return $aRet;

        } catch (\Gveniver\Exception\Exception $cEx) {
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
                $cCacheSplitter = new \Gveniver\FileSplitter($sCacheAbsPath.$sCacheFile, new \Gveniver\StylePacker());
                foreach ($aSameConditions as $aSameConditionStyle)
                    $cCacheSplitter->addFile($sStyleAbsPath.$aSameConditionStyle['FileName']);

                $cCacheSplitter->save();

            } // End foreach

        } catch (\Gveniver\Exception\Exception $cEx) {
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