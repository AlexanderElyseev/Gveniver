<?php
/**
 * System initialization file.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver;
require_once 'Gveniver/ExtendCommon.inc.php';

define('GV_EXEC', 1);                                           // Execution flag.
define('GV_DS', DIRECTORY_SEPARATOR);                           // Directory separator.
define('GV_PATH_BASE', __DIR__.GV_DS);                // Gveniver base path.
define('GV_PATH_CACHE', GV_PATH_BASE.'cache'.GV_DS);            // Server cache directory path.
define('GV_DATE_NOW', date('Y-m-d H:i:s'));                     // Current date as string.
define('GV_TIME_NOW', time());                                  // Current time at integer.
define('GV_OS_WIN', stripos(PHP_OS, 'win') !== false);          // Is running on Windows?
define('GV_CLI', stripos(php_sapi_name(), 'cli') !== false);    // Is running on CLI?
define('GV_EOL', GV_CLI ? "\n" : '<br/>');                      // End of line.
if (!defined('GV_APPLICATION_PATH_BASE'))
    define('GV_APPLICATION_PATH_BASE', null);                   // Base application path.

// @codingStandardsIgnoreStart
spl_autoload_register(
    function ($sClassName) {

        // Load from default system directory.
        $sRelativeFilePath = str_replace('\\', GV_DS, $sClassName).'.inc.php';
        $sAbsoluteFilePath = GV_PATH_BASE.$sRelativeFilePath;
        if (!file_exists($sAbsoluteFilePath)) {

            // Load files from .
            if (defined('GV_APPLICATION_PATH_BASE') && GV_APPLICATION_PATH_BASE) {
                $sAbsoluteFilePath = GV_APPLICATION_PATH_BASE.$sRelativeFilePath;
                if (!file_exists($sAbsoluteFilePath))
                    return false;
            } else {
                return false;
            }
        }
        
        /** @noinspection PhpIncludeInspection */
        include_once $sAbsoluteFilePath;
        return true;
    }
);
// @codingStandardsIgnoreEnd