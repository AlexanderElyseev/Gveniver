<?php
/**
 * File contains base abstract application profile class.
 * 
 * @category   Gveniver
 * @package    Kernel
 * @subpackage Profile
 * @author     Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright  2008-2011 Elyseev Alexander
 * @license    http://prof-club.ru/license.txt Prof-Club License
 * @link       http://prof-club.ru
 */

namespace Gveniver\Kernel\Profile;

/**
 * Base abstract application profile class.
 * 
 * PHP version 5
 *
 * @category   Gveniver
 * @package    Kernel
 * @subpackage Profile
 * @author     Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright  2008-2011 Elyseev Alexander
 * @license    http://prof-club.ru/license.txt Prof-Club License
 * @link       http://prof-club.ru
 */
class BaseProfile
{
    /**
     * Configuration of current profile.
     *
     * @var \Gveniver\Config
     */
    private $_cConfig;
    //-----------------------------------------------------------------------------

    /**
     * Path to directory with profile.
     *
     * @var string
     */
    private $_sProfilePath;
    //-----------------------------------------------------------------------------

    /**
     * Reference to current kernel.
     *
     * @var \Gveniver\Kernel\Application
     */
    private $_cApplication;
    //-----------------------------------------------------------------------------

    /**
     * Parent profile for current.
     *
     * @var BaseProfile
     */
    private $_cParentProfile;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Constructor of {@see Profile} class.
     * Initialize new instance of application profile.
     *
     * @param \Gveniver\Kernel\Application $cApplication   Current application.
     * @param string                       $sProfileDir    Path to directory with profile.
     * @param BaseProfile                  $cParentProfile Parent profile for current.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication, $sProfileDir, BaseProfile $cParentProfile = null)
    {
        $this->_cConfig = new \Gveniver\Config();
        
        $this->_cApplication = $cApplication;
        $this->_sProfilePath = $sProfileDir;
        $this->_cParentProfile = $cParentProfile;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for configuration of profile..
     *
     * @return \Gveniver\Config
     */
    public function getConfig()
    {
        return $this->_cConfig;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for current application.
     *
     * @return \Gveniver\Kernel\Application
     */
    public function getApplication()
    {
        return $this->_cApplication;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for directory of profile.
     *
     * @return string
     */
    public function getPath()
    {
        return $this->_sProfilePath;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for parent profile.
     *
     * @return BaseProfile
     */
    public function getParentProfile()
    {
        return $this->_cParentProfile;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Return array of all parent profiles from current.
     *
     * @return array
     */
    public function getParentProfileList()
    {
        $aRet = array();
        $cP = $this;
        do {
            $aRet[] = $cP;
        } while (null !== ($cP = $cP->getParentProfile()));

        return $aRet;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Start profile logic.
     * By default, load and parse main template with current section and action.
     *
     * @return string
     */
    public function start()
    {
        $this->getApplication()->trace->addLine('[%s] Start.', __CLASS__);

        $sContentType = $this->getContentType(
            $this->getCurrentSectionName(),
            $this->getCurrentAction()
        );
        if ($sContentType) {
            $this->getApplication()->trace->addLine('[%s] Using "%s" as content-type.', __CLASS__, $sContentType);
            header('Content-type: '.$sContentType);
        }
        
        $sResult = $this->getApplication()->template->parseTemplate(
            $this->getMainTemplate(
                $this->getCurrentSectionName(),
                $this->getCurrentAction()
            )
        );
        $this->getApplication()->trace->addLine('[%s] End.', __CLASS__);
        return $sResult;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Return name of current section.
     *
     * @return string
     */
    public function getCurrentSectionName()
    {
        return $this->_cApplication->invar->get(
            $this->_cApplication->getConfig()->get('Module/InvarModule/SectionKeyName')
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns value of current action.
     *
     * @return string
     */
    public function getCurrentAction()
    {
        return $this->_cApplication->invar->get(
            $this->_cApplication->getConfig()->get('Module/InvarModule/ActionKeyName')
        );

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for content type of current page.
     *
     * @param string $sSectionName Name of section. Or default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     * 
     * @return string Content type of page or null on error.
     */
    public function getContentType($sSectionName = null, $sAct = null)
    {
        // Load for section and action.
        $aSectionList = null;
        if ($sSectionName && $sAct)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if (isset($aSection['Name']) && $aSection['Name'] == $sSectionName)
                        if (isset($aSection['ActList']))
                            foreach ($aSection['ActList'] as $aAction)
                                if (isset($aAction['Value']) && $aAction['Value'] == $sAct && isset($aAction['Section']['ContentType']))
                                    return $aAction['Section']['ContentType'];
        
        // Load for section.
        $aSectionList = array();
        if ($sSectionName)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if ($aSection['Name'] == $sSectionName && isset($aSection['ContentType']))
                        return $aSection['ContentType'];

        // Load for default section.
        $sContentType = '';
        if ($this->_cApplication->getConfig()->get('Profile/SectionList/Default/ContentType', $sContentType))
            return $sContentType;

        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add content type of page for section.
     *
     * @param string $sContentType Content type to set.
     * @param string $sSectionName Name of section. Or Default section, if not set.
     *
     * @return void
     */
    public function addContentType($sContentType, $sSectionName = null)
    {
        throw new \Gveniver\Exception\NotImplementedException();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for page author.
     *
     * @param string $sSectionName Name of section. Or Default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return string Author metadata of null, if not found.
     */
    public function getAuthor($sSectionName = null, $sAct = null)
    {
        // Load for section and action.
        $aSectionList = null;
        if ($sSectionName && $sAct)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if (isset($aSection['Name']) && $aSection['Name'] == $sSectionName)
                        if (isset($aSection['ActList']))
                            foreach ($aSection['ActList'] as $aAction)
                                if (isset($aAction['Value']) && $aAction['Value'] == $sAct && isset($aAction['Section']['Author']))
                                    return $aAction['Section']['Author'];

        // Load for section.
        $aSectionList = array();
        if ($sSectionName)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if ($aSection['Name'] == $sSectionName && isset($aSection['Author']))
                        return $aSection['Author'];

        // Load for default section.
        $sAuthor = '';
        if ($this->_cApplication->getConfig()->get('Profile/SectionList/Default/Author', $sAuthor))
            return $sAuthor;

        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add page author of page for section.
     *
     * @param string $sAuthor      Page author to set.
     * @param string $sSectionName Name of section. Or Default section, if not set.
     *
     * @return void
     */
    public function addAuthor($sAuthor, $sSectionName = null)
    {
        throw new \Gveniver\Exception\NotImplementedException();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter data for robots.
     *
     * @param string $sSectionName Name of section. Or Default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return string Robots metadata of null, if not found.
     */
    public function getRobots($sSectionName = null, $sAct = null)
    {
        // Load for section and action.
        $aSectionList = null;
        if ($sSectionName && $sAct)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if (isset($aSection['Name']) && $aSection['Name'] == $sSectionName)
                        if (isset($aSection['ActList']))
                            foreach ($aSection['ActList'] as $aAction)
                                if (isset($aAction['Value']) && $aAction['Value'] == $sAct && isset($aAction['Section']['Robots']))
                                    return $aAction['Section']['Robots'];

        // Load for section.
        $aSectionList = array();
        if ($sSectionName)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if ($aSection['Name'] == $sSectionName && isset($aSection['Robots']))
                        return $aSection['Robots'];

        // Load for default section.
        $sRobots = '';
        if ($this->_cApplication->getConfig()->get('Profile/SectionList/Default/Robots', $sRobots))
            if (is_string($sRobots))
                return $sRobots;

        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add data for robots of page for section.
     *
     * @param string $sRobots      Data for robots to set.
     * @param string $sSectionName Name of section. Or Default section, if not set.
     *
     * @return void
     */
    public function addRobots($sRobots, $sSectionName = null)
    {
        throw new \Gveniver\Exception\NotImplementedException();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for list of page keywords.
     *
     * @param string $sSectionName Name of section. Or Default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return string List of keywords.
     */
    public function getKeywords($sSectionName = null, $sAct = null)
    {
        $sResult = '';
        
        // Load for section and action.
        $aSectionList = null;
        if ($sSectionName && $sAct)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if (isset($aSection['Name']) && $aSection['Name'] == $sSectionName)
                        if (isset($aSection['ActList']))
                            foreach ($aSection['ActList'] as $aAction)
                                if (isset($aAction['Value']) && $aAction['Value'] == $sAct && isset($aAction['Section']['Keywords']))
                                    $sResult = $aAction['Section']['Keywords'];

        // Load for section.
        $aSectionList = array();
        if ($sSectionName)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if ($aSection['Name'] == $sSectionName && isset($aSection['Keywords']))
                        $sResult = ($sResult) ? ','.$aSection['Keywords'] : $aSection['Keywords'];

        // Load for default section.
        $sKeywords = '';
        if ($this->_cApplication->getConfig()->get('Profile/SectionList/Default/Keywords', $sKeywords))
            $sResult .= ($sResult) ? ','.$sKeywords : $sKeywords;

        return $sResult;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add keywords for section.
     * May contains string with delemiter ','.
     *
     * @param string $sKeywords    Keywords to add.
     * @param string $sSectionName Name of section. Or Default section, if not set.
     *
     * @return void
     */
    public function addKeywords($sKeywords, $sSectionName = null)
    {
        throw new \Gveniver\Exception\NotImplementedException();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add title for section.
     *
     * @param string $sTitle       Title to add.
     * @param string $sSectionName Name of section. Or Default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return void
     */
    public function addTitle($sTitle, $sSectionName = null, $sAct = null)
    {
        throw new \Gveniver\Exception\NotImplementedException();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add subtitle for section.
     *
     * @param string $sSubTitle    Подзаголовок раздела.
     * @param string $sSectionName Name of section. Or Default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return void
     */
    public function addSubTitle($sSubTitle, $sSectionName = null, $sAct = null)
    {
        throw new \Gveniver\Exception\NotImplementedException();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add main template for section.
     *
     * @param string $sTemplate    Template to add.
     * @param string $sSectionName Name of section. Or Default section, if not set.
     *
     * @return void
     */
    public function addMainTemplate($sTemplate, $sSectionName = null)
    {
        throw new \Gveniver\Exception\NotImplementedException();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add action template for section.
     *
     * @param string $sTemplate    Template to add.
     * @param string $sSectionName Name of section.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return void
     */
    public function addActTemplate($sTemplate, $sSectionName, $sAct = null)
    {
        throw new \Gveniver\Exception\NotImplementedException();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add script for section.
     *
     * @param string $sScript      Name of script.
     * @param string $sSectionName Name of section. Or Default section, if not set.
     *
     * @return void
     */
    public function addScript($sScript, $sSectionName = null)
    {
        throw new \Gveniver\Exception\NotImplementedException();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Add style for section.
     *
     * @param string $sStyle       Name of style.
     * @param string $sCondition   Condition of style.
     * @param string $sSectionName Name of section. Or Default section, if not set.
     *
     * @return void
     */
    public function addStyle($sStyle, $sCondition, $sSectionName = null)
    {
        throw new \Gveniver\Exception\NotImplementedException();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for action template.
     *
     * @param string $sSectionName Name of section. Or Default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return string Template name or null, if not found.
     */
    public function getActTemplate($sSectionName = null, $sAct = null)
    {
        // Load by section and action.
        $aSectionList = array();
        if ($sSectionName && $sAct)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if ($aSection['Name'] == $sSectionName && isset($aSection['ActList']))
                        foreach ($aSection['ActList'] as $aAction)
                            if (isset($aAction['Value']) && $aAction['Value'] == $sAct && isset($aAction['FileName']))
                                return $aAction['FileName'];

        // Load for section and default action.
        $aSectionList = array();
        if ($sSectionName && !$sAct)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if ($aSection['Name'] == $sSectionName && isset($aSection['ActList']['Default']['FileName']))
                        return $aSection['ActList']['Default']['FileName'];

        // Load for default section and action.
        $aDefaultSection = array();
        $this->_cApplication->getConfig()->get('Profile/SectionList/Default', $aDefaultSection);
        if (!$sSectionName && $sAct)
            if (isset($aDefaultSection['ActList']))
                foreach ($aDefaultSection['ActList'] as $aAction)
                    if (isset($aAction['Value']) && $aAction['Value'] == $sAct && isset($aAction['FileName']))
                        return $aAction['FileName'];

        // Load for default section and default action.
        $sTpl = '';
        if ($this->_cApplication->getConfig()->get('Profile/SectionList/Default/ActList/Default/FileName', $sTpl))
            return $sTpl;

        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for main template.
     *
     * @param string $sSectionName Name of section. Or Default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return string Main template name or null, if not found.
     */
    public function getMainTemplate($sSectionName = null, $sAct = null)
    {
        // Load for section and action.
        $aSectionList = array();
        if ($sSectionName && $sAct)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if (isset($aSection['Name']) && $aSection['Name'] == $sSectionName)
                        if (isset($aSection['ActList']))
                            foreach ($aSection['ActList'] as $aAction)
                                if (isset($aAction['Value']) && $aAction['Value'] == $sAct && isset($aAction['Section']['MainTemplate']))
                                    return $aAction['Section']['MainTemplate'];
        
        // Load for section.
        $aSectionList = array();
        if ($sSectionName)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if ($aSection['Name'] == $sSectionName && isset($aSection['MainTemplate']))
                        return $aSection['MainTemplate'];

        // Load for default section.
        $sTpl = '';
        if ($this->_cApplication->getConfig()->get('Profile/SectionList/Default/MainTemplate', $sTpl))
            return $sTpl;

        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns list of scripts.
     *
     * @param string $sSectionName Name of section. Or Default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return array
     */
    public function getScriptList($sSectionName = null, $sAct = null)
    {
        // Load for section and action.
        $aActionSectionScripts = array();
        $aSectionList = array();
        if ($sSectionName && $sAct)
            if ($this->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if (isset($aSection['Name']) && $aSection['Name'] == $sSectionName)
                        if (isset($aSection['ActList']))
                            foreach ($aSection['ActList'] as $aAction)
                                if (isset($aAction['Value']) && $aAction['Value'] == $sAct && isset($aAction['Section']['ScriptList']))
                                    $aActionSectionScripts = array_values($aAction['Section']['ScriptList']);

        // Load for section.
        $aSectionScripts = array();
        $aSectionList = array();
        if ($sSectionName)
            if ($this->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if ($aSection['Name'] == $sSectionName && isset($aSection['ScriptList']))
                        $aSectionScripts = array_values($aSection['ScriptList']);

        // Load for default section.
        $aBaseScripts = array();
        if ($this->getConfig()->get('Profile/SectionList/Default/ScriptList', $aBaseScripts))
            $aBaseScripts = array_values($aBaseScripts);

        return array_merge($aBaseScripts, $aSectionScripts, $aActionSectionScripts);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns list of styles.
     *
     * @param string $sSectionName Name of section. Or Default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return array
     */
    public function getStyleList($sSectionName = null, $sAct = null)
    {
        // Load for section and action.
        $aActionSectionStyles = array();
        $aSectionList = array();
        if ($sSectionName && $sAct)
            if ($this->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if (isset($aSection['Name']) && $aSection['Name'] == $sSectionName)
                        if (isset($aSection['ActList']))
                            foreach ($aSection['ActList'] as $aAction)
                                if (isset($aAction['Value']) && $aAction['Value'] == $sAct && isset($aAction['Section']['StyleList']))
                                    $aActionSectionStyles = array_values($aAction['Section']['StyleList']);

        // Load for section.
        $aSectionStyles = array();
        $aSectionList = array();
        if ($sSectionName)
            if ($this->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if ($aSection['Name'] == $sSectionName && isset($aSection['StyleList']))
                        $aSectionStyles = array_values($aSection['StyleList']);

        // Load for default section.
        $aBaseStyles = array();
        if ($this->getConfig()->get('Profile/SectionList/Default/StyleList', $aBaseStyles))
            $aBaseStyles = array_values($aBaseStyles);

        return array_merge($aBaseStyles, $aSectionStyles, $aActionSectionStyles);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for page title.
     *
     * @param string $sSectionName Name of section. Or Default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return Title for section or null, if not found.
     */
    public function getTitle($sSectionName = null, $sAct = null)
    {
        // Load for section and action.
        $aSectionList = null;
        if ($sSectionName && $sAct)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if (isset($aSection['Name']) && $aSection['Name'] == $sSectionName)
                        if (isset($aSection['ActList']))
                            foreach ($aSection['ActList'] as $aAction)
                                if (isset($aAction['Value']) && $aAction['Value'] == $sAct && isset($aAction['Section']['Title']))
                                    return $aAction['Section']['Title'];

        // Load for section.
        if ($sSectionName)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if (isset($aSection['Name']) && $aSection['Name'] == $sSectionName && isset($aSection['Title']))
                        return $aSection['Title'];

        // Load for default section.
        if ($this->_cApplication->getConfig()->get('Profile/SectionList/Default/Title', $sTitle))
            return $sTitle;

        return null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for page subtitle.
     *
     * @param string $sSectionName Name of section. Or Default section, if not set.
     * @param string $sAct         Action value. If not set, Default action.
     *
     * @return string Subtitle for section or null, if not found.
     */
    public function getSubTitle($sSectionName = null, $sAct = null)
    {
        // Load for section and action.
        $aSectionList = null;
        if ($sSectionName && $sAct)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if (isset($aSection['Name']) && $aSection['Name'] == $sSectionName)
                        if (isset($aSection['ActList']))
                            foreach ($aSection['ActList'] as $aAction)
                                if (isset($aAction['Value']) && $aAction['Value'] == $sAct && isset($aAction['Section']['SubTitle']))
                                        return $aAction['Section']['SubTitle'];

        // Load for section.
        if ($sSectionName)
            if ($this->_cApplication->getConfig()->get('Profile/SectionList/List', $aSectionList))
                foreach ($aSectionList as $aSection)
                    if (isset($aSection['Name']) && $aSection['Name'] == $sSectionName && isset($aSection['SubTitle']))
                        return $aSection['SubTitle'];

        // Load for default section.
        if ($this->_cApplication->getConfig()->get('Profile/SectionList/Default/SubTitle', $sSubTitle))
            return $sSubTitle;

        return null;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------