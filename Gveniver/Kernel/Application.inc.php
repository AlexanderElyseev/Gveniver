<?php
/**
 * File contains base and final class of kernel.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Kernel;

/**
 * Base and final class of application.
 *
 * PHP version 5
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * 
 * @property  \Gveniver\Kernel\Module\CacheModule     $cache
 * @property  \Gveniver\Kernel\Module\CaptchaModule   $captcha
 * @property  \Gveniver\Kernel\Module\DataModule      $data
 * @property  \Gveniver\Kernel\Module\ExtensionModule $extension
 * @property  \Gveniver\Kernel\Module\InvarModule     $invar
 * @property  \Gveniver\Kernel\Module\LogModule       $log
 * @property  \Gveniver\Kernel\Module\RedirectModule  $redirect
 * @property  \Gveniver\Kernel\Module\TemplateModule  $template
 * @property  \Gveniver\Kernel\Module\TraceModule     $trace
 */
final class Application
{
    /**
     * Configuration of application.
     *
     * @var \Gveniver\Config
     */
    private $_cConfig;
    //-----------------------------------------------------------------------------

    /**
     * Current profile of application.
     *
     * @var Profile\BaseProfile
     */
    private $_cProfile;
    //-----------------------------------------------------------------------------

    /**
     * List of loaded modules.
     *
     * @var array
     */
    private $_aModules = array();
    //-----------------------------------------------------------------------------
    
    /**
     * Hash table for relation between module name and module file name..
     *
     * @var array
     */
    private $_aModuleAliasList = array();
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Constructor of {@see Kernel} class.
     * Initialize new instance of kernel and PHP environment by kernel configuration.
     *
     * @param string|Profile\BaseProfile $mProfile               Path to profile directory or name of profile, or profile instance.
     * @param string                     $sApplicationConfigFile Path to XML configuration file for application.
     */
    public function __construct($mProfile, $sApplicationConfigFile = null)
    {
        // Initialize and load base configuration.
        $this->_cConfig = new \Gveniver\Config();
        $this->_cConfig->mergeXmlFile(GV_PATH_BASE.'config.xml');

        // Append additional application configuration, if specified.
        if ($sApplicationConfigFile)
            $this->_cConfig->mergeXmlFile($sApplicationConfigFile);

        // Initialize profile of application.
        if ($mProfile instanceof Profile\BaseProfile)
            $this->_cProfile = $mProfile;
        else {
            $this->_cProfile = $this->_loadProfile($mProfile);
            if (!$this->_cProfile)
                throw new \Gveniver\Exception\ArgumentException('Profile "'.$mProfile.'" is not loaded.');
        }

        // Initialization of environment.
        $this->_initEnvironment();

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Getter for configuration of application.
     *
     * @return \Gveniver\Config
     */
    public function getConfig()
    {
        return $this->_cConfig;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Overloaded getter method for non existing class fields.
     * Returns module by field name.
     *
     * @param string $sName Parameter name for reading.
     *
     * @return Module Null on error.
     */
    public function __get($sName)
    {
        $cModule = $this->getModule($sName);
        if (!$cModule)
            throw new \Gveniver\Exception\BaseException(sprintf('Module ("%s") not loaded.', $sName));

        return $cModule;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Initialization of environment by configuration of current profile.
     *
     * @return void
     */
    private function _initEnvironment()
    {
        $this->trace->addLine('[%s] Initializing environement of the kernel.', __CLASS__);

        // Error reporting.
        $nErrorReporting = $this->getConfig()->get('Kernel/ErrorReporting');
        ini_set('error_reporting', $nErrorReporting);
        $this->trace->addLine('[%s] Error reporting: %d.', __CLASS__, $nErrorReporting);

        // Display errors.
        $bDisplayErrors = self::toBoolean($this->getConfig()->get('Kernel/DisplayErrors'));
        ini_set('display_errors', $bDisplayErrors);
        $this->trace->addLine('[%s] Display errors: %s.', __CLASS__, $bDisplayErrors ? 'true' : 'false');
        
        // Start session.
        $bStartSession = self::toBoolean($this->getConfig()->get('Kernel/StartSession'));
        if ($bStartSession) {
            session_start();
            $this->trace->addLine('[%s] Session started.', __CLASS__);
        }

        // Locale.
        $sLocale = $this->getConfig()->get('Kernel/Locale');
        if ($sLocale) {
            setlocale(LC_ALL, $sLocale);
            $this->trace->addLine('[%s] Locale: %s.', __CLASS__, $sLocale);
        } else {
            $this->trace->addLine('[%s] Locale is not loaded from configuration.', __CLASS__);
        }

        // Timezone.
        $sTimezone = $this->getConfig()->get('Kernel/Timezone');
        if ($sTimezone) {
            date_default_timezone_set($sTimezone);
            $this->trace->addLine('[%s] Timezone: %s.', __CLASS__, $sTimezone);
        } else {
            $this->trace->addLine('[%s] Timezone is not loaded from configuration.', __CLASS__);
        }

        // Multibyte encoding.
        $sEncoding = $this->getConfig()->get('Kernel/Encoding');
        if ($sEncoding) {
            mb_internal_encoding($sEncoding);
            mb_regex_encoding($sEncoding);
            $this->trace->addLine('[%s] Multibyte encoding: %s.', __CLASS__, $sEncoding);
        } else {
            $this->trace->addLine('[%s] Multibyte encoding is not loaded from configuration.', __CLASS__);
        }

        // Use output buffering.
        $bUseBuffering = self::toBoolean($this->getConfig()->get('Kernel/UseOutputBuffering'));
        if ($bUseBuffering) {
            $this->trace->addLine('[%s] Use output buffering.', __CLASS__);

            // Force compression even when client does not report support.
            $bForceCompression = self::toBoolean($this->getConfig()->get('Kernel/ForceCompression'));

            // Prefer deflate over gzip when both are supported.
            $bPreferDeflate = self::toBoolean($this->getConfig()->get('Kernel/PreferDeflate'));

            // Handle the output stream and set a handler function.
            if(isset($_SERVER['HTTP_ACCEPT_ENCODING']))
                $sAE = $_SERVER['HTTP_ACCEPT_ENCODING'];
            else
                $sAE = $_SERVER['HTTP_TE'];

            $bGzipSupport = (strpos($sAE, 'gzip') !== false) || $bForceCompression;
            $bDeflateSupport = (strpos($sAE, 'deflate') !== false) || $bForceCompression;
            if($bGzipSupport && $bDeflateSupport)
                $bDeflateSupport = $bPreferDeflate;

            if ($bDeflateSupport) {
                // Defalte compression.
                header('Content-Encoding: deflate');
                ob_start('Kernel::obHandlerDeflate');
            } elseif ($bGzipSupport) {
                // Gzip compression.
                header('Content-Encoding: gzip');
                ob_start('Kernel::obHandlerGzip');
            } else
                // No compression.
                ob_start();

        } // End if

        $this->trace->addLine('[%s] Environement of the kernel successfully initialized.', __CLASS__);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load application profile instance by name.
     *
     * If current profile extends other profile, load this profile by name.
     * All configurations are combined start from base.
     *
     * @param string $sProfileName Path to application profile dir or name of application profile for loading.
     * If directory is specified, load from directory. Otherwise, load from base profile directory
     * with specified profile name.
     *
     * @return Profile\BaseProfile|null Returns application profile by specified name or null, if module not loaded.
     */
    private function _loadProfile($sProfileName)
    {
        // Name of profile must be correct.
        if (!$sProfileName) {
            $this->trace->addLine('[%s] Try to load profile without name.', __CLASS__);
            return null;
        }

        // Check profile directory.
        if (is_dir($sProfileName) && \Gveniver\isAbsolutePath($sProfileName)) {
            $this->trace->addLine('[%s] Load profile by path ("%s").', __CLASS__, $sProfileName);
            $sProfileDir = $sProfileName;
            $sProfileName = basename($sProfileName);
        } else {
            $this->trace->addLine('[%s] Load profile by name ("%s").', __CLASS__, $sProfileName);
            $sProfileDir = \Gveniver\correctPath($this->getConfig()->get('Kernel/ProfilePath'), true).$sProfileName.GV_DS;
            if (!is_dir($sProfileDir)) {
                $this->trace->addLine('[%s] Profile directory ("%s") is not exists.', __CLASS__, $sProfileDir);
                return null;
            }
        }
        
        // Load class name of profile.
        $sProfileClass = $this->_getProfileClassName($sProfileName);
        if (!$sProfileClass) {
            $this->trace->addLine('[%s] Profile class ("%s") is not loaded.', __CLASS__, $sProfileClass);
            return null;
        }
        $this->trace->addLine('[%s] Profile class ("%s") successfully loaded.', __CLASS__, $sProfileClass);

        // Load parent profile.
        $cParentProfile = null;
        $sParentProfileName = $this->_getProfileNameByClassName(get_parent_class($sProfileClass));
        if ($sParentProfileName) {
            $this->trace->addLine('[%s] Load base profile ("%s").', __CLASS__, $sParentProfileName);
            $cParentProfile = $this->_loadProfile($sParentProfileName);
            if ($cParentProfile)
                $this->trace->addLine('[%s] Load profile ("%s") successfully loaded.', __CLASS__, $sParentProfileName);
            else
                $this->trace->addLine('[%s] Load profile ("%s") is not loaded.', __CLASS__, $sParentProfileName);
        }

        // Create instance of profile.
        try {
            $cProfile = new $sProfileClass($this, $sProfileDir, $cParentProfile);
        } catch (\Gveniver\Exception\BaseException $cEx) {
            $this->trace->addLine(
                '[%s] Exception in profile ("%s") constructor: "%s".',
                __CLASS__,
                $sProfileName,
                $cEx->getMessage()
            );
            return null;
        }
        $this->trace->addLine('[%s] Profile instance ("%s") successfully created.', __CLASS__, $sProfileClass);

        /** @var $cProfile \Gveniver\Kernel\Profile\BaseProfile */

        // Load configuration of profile and append to main configuration.
        $sProfileXmlFile = $sProfileDir.'config.xml';
        if ($cProfile->getConfig()->mergeXmlFile($sProfileXmlFile)) {
            $this->getConfig()->mergeConfig($cProfile->getConfig());
            $this->trace->addLine(
                '[%s] Configuration parameters of profile ("%s") successfully loaded (from "%s").',
                __CLASS__,
                $sProfileName,
                $sProfileXmlFile
            );
        } else {
            $this->trace->addLine(
                '[%s] Configuration parameters of profile ("%s") not found in "%s".',
                __CLASS__,
                $sProfileName,
                $sProfileXmlFile
            );
        }
    
        return $cProfile;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Returns name of profile by full class name with namespaces or without.
     *
     * @param string $sFullProfileClassName Full class name.
     * 
     * @return string
     */
    private function _getProfileNameByClassName($sFullProfileClassName)
    {
        $aClassNameParts = explode('\\', $sFullProfileClassName);
        $sClassNameWithoutNamespaces = array_pop($aClassNameParts);

        preg_match('/(\w+)Profile/', $sClassNameWithoutNamespaces, $aMatches);
        return isset($aMatches[1]) ? $aMatches[1] : null;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Load class name of profile for creating new instance.
     * If profile class is not defined or is not loaded, use base {@see Profile} class.
     *
     * @param string $sProfileName Name of profile for loading.
     *
     * @return string|null Returns class name of profile or null, if loaded wrong profile class.
     */
    private function _getProfileClassName($sProfileName)
    {
        // Include profile file with class, if target class is not exists.
        $sProfileClass = '\\Gveniver\\Kernel\\Profile\\'.$sProfileName.'Profile';
        if (!class_exists($sProfileClass, true)) {
            $this->trace->addLine(
                '[%s] Profile class ("%s") is not exists. Loading base profile.',
                __CLASS__,
                $sProfileClass
            );
            return '\\Gveniver\\Kernel\\Profile\\BaseProfile';
        }

        // Profile class must extend base profile class.
        if (!in_array('Gveniver\\Kernel\\Profile\\BaseProfile', class_parents($sProfileClass))) {
            $this->trace->addLine(
                '[%s] Profile class ("%s") must extends base profile class. Loading base profile.',
                __CLASS__,
                $sProfileClass
            );
            return null;
        }

        return $sProfileClass;

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Returns module by name.
     *
     * @param string $sModuleName Name of module. May be short (ex. trace -> TraceModule).
     *
     * @return Module|null
     */
    public function getModule($sModuleName)
    {
        // If was an attempt of loading module, only return saved result.
        if (array_key_exists($sModuleName, $this->_aModules))
            return $this->_aModules[$sModuleName];

        // Mark attemp of loading module as failed by default.
        $this->_aModules[$sModuleName] = null;

        // Build correct module class name.
        $sModuleClassName = isset($this->_aModuleAliasList[$sModuleName])
            ? 'Gveniver\\Kernel\\Module\\'.$this->_aModuleAliasList[$sModuleName]
            : 'Gveniver\\Kernel\\Module\\'.ucfirst($sModuleName).'Module';

        // Class of module must exists.
        if (!class_exists($sModuleClassName))
            return null;

        // Class of module must extends base module class.
        if (!in_array('Gveniver\\Kernel\\Module\\BaseModule', class_parents($sModuleClassName)))
            return null;

        // Check module relations.
        $aRelations = $this->getConfig()->get(array('Module', $sModuleName, 'Relations'));
        if (is_array($aRelations) && count($aRelations) > 0)
            foreach ($aRelations as $sRelationModule)
                if (!$this->getModule($sRelationModule['Name']))
                    return null;
        
        // Try to create new instance of module.
        try {
            $cModule = new $sModuleClassName($this);
        } catch (\Exception $cEx) {
            return null;
        }

        // Save to cache and return result.
        return $this->_aModules[$sModuleName] = $cModule;

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Returns current application profile.
     *
     * @return \Gveniver\Kernel\Profile\BaseProfile
     */
    public function getProfile()
    {
        return $this->_cProfile;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Output buffer handler with GZip compression.
     *
     * @param string $sBuffer Buffer data.
     *
     * @return string
     */
    public static function obHandlerGzip($sBuffer)
    {
        return gzencode($sBuffer, 9);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Output buffer handler with deflate compression.
     *
     * @param string $sBuffer Buffer data.
     *
     * @return string
     */
    public static function obHandlerDeflate($sBuffer)
    {
        return gzdeflate($sBuffer, 9);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Convert value to boolean.
     *
     * @param mixed $mValue Value to convert.
     *
     * @return bool Convert result.
     * @static
     */
    public static function toBoolean($mValue)
    {
        if ($mValue === true || $mValue === 1 || $mValue === '1' || $mValue === 'true')
            return true;

        return false;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Convert value to integer, if value is not null.
     *
     * @param mixed $mValue Value to convert.
     *
     * @return integer|null Converted result.
     * @static
     */
    public static function toIntegerOrNull($mValue)
    {
        return is_null($mValue) ? null : intval($mValue);

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------