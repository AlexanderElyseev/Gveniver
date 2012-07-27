<?php
/**
 * File contains class of simple extension.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Extension;

/**
 * Class of simple extension.
 * Without export data.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class SimpleExtension extends BaseExtension
{
    /**
     * The name of current class.
     * Used for building log records without calls of function.
     *
     * @var string
     */
    protected $sClassName;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     *
     * @param \Gveniver\Kernel\Application $cApplication Current application.
     */
    public function __construct(\Gveniver\Kernel\Application $cApplication)
    {
        $this->sClassName = get_class($this);

        parent::__construct($cApplication);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Query to extension.
     * Redirect request to public function of current extension with same name.
     *
     * @param string $sAction  Name of action handler.
     * @param array  $aParams  Arguments to extension query.
     * @param array  $aOptions Options for query.
     * Elements:
     * ['format']   - target output format;
     * ['external'] - external call of extension (required permessions in export list);
     * ['cache']    - cache result of extension query;
     *
     * @return mixed
     */
    public function query($sAction, $aParams = array(), $aOptions = array())
    {
        $this->getApplication()->trace->addLine(
            '[%s : %s] Executing query: "%s".',
            __CLASS__,
            $this->sClassName,
            $sAction
        );

        // Load name of method by specified output format.
        $sMethodName = $this->_loadHandlerMethodName(
            $sAction,
            isset($aOptions['format']) && $aOptions['format'] ? $aOptions['format'] : null
        );
        if (!$sMethodName)
            return null;

        // Check query handler.
        $bExteranl = isset($aOptions['external']) && $aOptions['external'];
        if (!$this->_checkQueryHandler($sMethodName, $sAction, $bExteranl))
            return null;
        
        $this->getApplication()->trace->addLine(
            '[%s : %s] Handler found for query: "%s".',
            __CLASS__,
            $this->sClassName,
            $sAction
        );

        // Executing query.
        return $this->_executeQuery(
            $sMethodName,
            $aParams,
            isset($aOptions['cache']) && $aOptions['cache']
        );

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Load name of handler method for specified action and target output format.
     * By default, is used action value as handler method name.
     * If format is specified and method for this format is not loaded, we have an error at call.
     *
     * @param string      $sAction Action name for loading method name.
     * @param string|null $sFormat Format name or null for default output format.
     *
     * @return string|null Name of handler method or null if method is not loaded.
     */
    private function _loadHandlerMethodName($sAction, $sFormat)
    {
        // Try to load name of method with format.
        if ($sFormat) {
            $sMethodName = null;

            // Load form export data.
            $aActList = $this->getConfig()->get('Extension/ActList');
            if (is_array($aActList)) {
                foreach ($aActList as $aAction) {
                    if (isset($aAction['Name']) && $aAction['Name'] == $sAction && isset($aAction['FormatList'])) {
                        foreach ($aAction['FormatList'] as $aFormat) {
                            if (isset($aFormat['Method']) && isset($aFormat['Name']) && $aFormat['Name'] == $sFormat) {
                                $sMethodName = $aFormat['Method'];
                                break 2;
                            }
                        }
                    }
                }
            }

            if (!$sMethodName) {
                $this->getApplication()->trace->addLine(
                    '[%s : %s] Handler for query ("%s") and format ("%s") is not found.',
                    __CLASS__,
                    $this->sClassName,
                    $sAction,
                    $sFormat
                );
                return null;
            }

            return $sMethodName;

        } // End if

        // By default, used action value as handler method name.
        return $sAction;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Execute call method with specified arguments and cache parameters.
     *
     * @param string     $sHandlerMethodName Name of handler method.
     * @param array      $aArgs              Arguments of query.
     * @param array|bool $mCache             Cache parameters.
     * 
     * @return mixed|null Result of query.
     */
    private function _executeQuery($sHandlerMethodName, $aArgs, $mCache)
    {
        // Check cache before executing query.
        if ($mCache) {
            $this->getApplication()->trace->addLine('[%s : %s] Using cache for extension query.', __CLASS__, $this->sClassName);

            // Try to load cache module from application.
            // On error execute query.
            $cCacheModule = $this->getApplication()->cache;
            if ($cCacheModule) {
                // Generate identifier of cache data by parameters of call.
                $sCacheId = is_array($mCache) && isset($mCache['id'])
                    ? $mCache['id']
                    : $cCacheModule->generateId(serialize($aArgs));

                // Load data from cache.
                // If data is not loaded, load by extension and save to cache.
                $sRet = null;
                if ($cCacheModule->get($sCacheId, self::CACHE_GROUP, $sRet)) {
                    $this->getApplication()->trace->addLine('[%s : %s] Data loaded from cache.', __CLASS__, $this->sClassName);
                } else {
                    $this->getApplication()->trace->addLine('[%s : %s] Data is not loaded from cache.', __CLASS__, $this->sClassName);
                    $sRet = $this->_callQueryHandler($sHandlerMethodName, $aArgs);
                    $cCacheModule->set($sRet, $sCacheId, self::CACHE_GROUP);
                }
            } else {
                $this->getApplication()->trace->addLine('[%s : %s] Cache module not found.', __CLASS__, $this->sClassName);
                $sRet = $this->_callQueryHandler($sHandlerMethodName, $aArgs);
            }
        } else {
            $sRet = $this->_callQueryHandler($sHandlerMethodName, $aArgs);
        }

        return $sRet;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Call query handler.
     *
     * @param string $sHandlerMethodName Name of handler method.
     * @param array  $aParams            Parameters of query.
     * 
     * @return mixed Result of query.
     */
    private function _callQueryHandler($sHandlerMethodName, array $aParams)
    {
        return call_user_func_array(array($this, $sHandlerMethodName), $aParams);

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Checking of query handler.
     *
     * @param string $sHandlerMethodName Name of handler method.
     * @param string $sAction            Query action.
     * @param bool   $bExteranl          This is exteranl query.
     *
     * @return bool True if handler is correct.
     */
    private function _checkQueryHandler($sHandlerMethodName, $sAction, $bExteranl)
    {
        // Check permissions of external query.
        $bExternalCheck = true;
        if ($bExteranl) {
            $bExternalCheck = false;
            $aActList = $this->getConfig()->get('Extension/ActList');
            if (is_array($aActList)) {
                foreach ($aActList as $aAction) {
                    if (isset($aAction['Name'])
                        && $aAction['Name'] == $sAction
                        && isset($aAction['External'])
                        && \Gveniver\toBoolean($aAction['External'])
                    ) {
                        $this->getApplication()->trace->addLine(
                            '[%s : %s] Handler for query ("%s") is for externa queries.',
                            __CLASS__,
                            $this->sClassName,
                            $sAction
                        );

                        $bExternalCheck = true;
                        break;
                        
                    } // End if

                } // foreach
                
            } // End if

        } // End if
        if (!$bExternalCheck) {
            $this->getApplication()->trace->addLine(
                '[%s : %s] Handler for query ("%s") is not for externa queries.',
                __CLASS__,
                $this->sClassName,
                $sAction
            );
            $this->getApplication()->log->security(
                sprintf(
                    '[%s : %s] Attempt to call exteranl query ("%s") without permissions.',
                    __CLASS__,
                    $this->sClassName,
                    $sAction
                )
            );
            return false;
        }

        // Check existence of method.
        if (!method_exists($this, $sHandlerMethodName)) {
            $this->getApplication()->trace->addLine(
                '[%s : %s] Handler for query ("%s") is not found.',
                __CLASS__,
                $this->sClassName,
                $sAction
            );
            $this->getApplication()->log->security(
                sprintf(
                    '[%s : %s] Attempt to call non-existed function ("%s").',
                    __CLASS__,
                    $this->sClassName,
                    $sAction
                )
            );
            return false;
        }

         // Method must be public.
        $cRefl = new \ReflectionMethod($this, $sHandlerMethodName);
        if (!$cRefl->isPublic()) {
            $this->getApplication()->trace->addLine(
                '[%s : %s] Handler for query ("%s") is not public.',
                __CLASS__,
                $this->sClassName,
                $sAction
            );
            $this->getApplication()->log->security(
                sprintf(
                    '[%s : %s] Attempt to call non-user function ("%s").',
                    __CLASS__,
                    $this->sClassName,
                    $sAction
                )
            );
            return false;
        }

        // Check for special object methods or methods.
        if (substr($sHandlerMethodName, 0, 1) == '_'
            || in_array($sHandlerMethodName, array('getConfig', 'getApplication', 'getResource', 'query'))
        ) {
            $this->getApplication()->trace->addLine(
                '[%s : %s] Handler for query ("%s") is not correct.',
                __CLASS__,
                $this->sClassName,
                $sAction
            );
            $this->getApplication()->log->security(
                sprintf(
                    '[%s : %s] Attempt to call from exteranl query ("%s") non-user functions.',
                    __CLASS__,
                    $this->sClassName,
                    $sAction
                )
            );
            return false;
        }

        return true;
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------