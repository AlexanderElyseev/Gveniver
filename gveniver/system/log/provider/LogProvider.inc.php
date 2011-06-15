<?php
/**
 * File contains base abstract log provider class for saving log data.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver;

/**
 * Base abstract log provider class for saving log data.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
abstract class LogProvider
{
    /**
     * Log level for fatal errors.
     *
     * @var int
     */
    const LEVEL_FATALERROR = 1;
    //-----------------------------------------------------------------------------

    /**
     * Log level for errors.
     *
     * @var int
     */
    const LEVEL_ERROR = 2;
    //-----------------------------------------------------------------------------

    /**
     * Log level for security records.
     *
     * @var int
     */
    const LEVEL_SECURITY = 4;
    //-----------------------------------------------------------------------------

    /**
     * Log level for warning records.
     *
     * @var int
     */
    const LEVEL_WARNING = 8;
    //-----------------------------------------------------------------------------

    /**
     * Log level for information records.
     * @var int
     */
    const LEVEL_INFO = 16;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------
        
    /**
     * Current kernel.
     *
     * @var Kernel
     */
    protected $cKernel;
    //-----------------------------------------------------------------------------

    /**
     * Configuration parameters of provider.
     *
     * @var array
     */
    protected $aConfigData;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Base constructor.
     * Initialize member fields.
     *
     * @param Kernel\Kernel $cKernel     Current kernel.
     * @param array         $aConfigData Configuration data of provider.
     */
    public function __construct(Kernel\Kernel $cKernel, array $aConfigData)
    {
        $this->cKernel = $cKernel;
        $this->aConfigData = $aConfigData;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Save log data with specified by provider logic.
     *
     * @param array $aData Log data for saving.
     *
     * @return void
     * @abstract
     */
    abstract function save(array $aData);
    //-----------------------------------------------------------------------------

    /**
     * Convert string representation of level to correct numeric constant.
     *
     * @param string $sLevel String level to convert.
     *
     * @return int
     * @static
     */
    public static function getLevelByName($sLevel)
    {
        $aLevelsHash = array(
            'FatalError' => self::LEVEL_FATALERROR,
            'Error'      => self::LEVEL_ERROR,
            'Security'   => self::LEVEL_SECURITY,
            'Warning'    => self::LEVEL_WARNING,
            'Info'       => self::LEVEL_INFO,
        );

        if (!isset($aLevelsHash[$sLevel]))
            $sLevel = 'Warning';

        return $aLevelsHash[$sLevel];

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Convert numeric representation of level to correct string.
     *
     * @param int $nLevel Numeric level to convert.
     *
     * @return string
     * @static
     */
    public static function getNameByLevel($nLevel)
    {
        $aLevelsHash = array(
            self::LEVEL_FATALERROR => 'FatalError',
            self::LEVEL_ERROR      => 'Error',
            self::LEVEL_SECURITY   => 'Security',
            self::LEVEL_WARNING    => 'Warning',
            self::LEVEL_INFO       => 'Info',
        );

        if (!isset($aLevelsHash[$nLevel]))
            $nLevel = self::LEVEL_INFO;

        return $aLevelsHash[$nLevel];

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------
