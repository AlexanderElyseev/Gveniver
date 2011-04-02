<?php
/**
 *
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
define('GV_EXEC', 1);						                    // Execution flag.
define('GV_DS', DIRECTORY_SEPARATOR);		                    // Directory separator.
define('GV_PATH_BASE', dirname(__FILE__).GV_DS);	            // Server base path.
define('GV_PATH_CACHE', GV_PATH_BASE.'cache'.GV_DS);	        // Server cache path.
define('GV_DATE_NOW', date('Y-m-d H:i:s'));	                    // Current time.
define('GV_OS_WIN', stripos(PHP_OS, 'win') !== false);          // Is Windows.
define('GV_CLI', stripos(php_sapi_name(), 'cli') !== false);    // CLI.
define('GV_EOL', GV_CLI ? "\n" : '<br/>');                      // End of line.

require 'src/GvKernelConst.inc.php';
require 'src/GvKernelInclude.inc.php';
require 'src/GvKernelExtendCommon.inc.php';
require 'src/GvKernelConfig.inc.php';
require 'src/GvKernel.inc.php';
require 'src/GvKernelModule.inc.php';
require 'src/GvKernelProfile.inc.php';
require 'src/GvKernelExtension.inc.php';

GvKernelInclude::instance()->skipFile('src/GvKernelConst.inc.php');
GvKernelInclude::instance()->skipFile('src/GvKernelExtendCommon.inc.php');
GvKernelInclude::instance()->skipFile('src/GvKernelInclude.inc.php');
GvKernelInclude::instance()->skipFile('src/GvKernelConfig.inc.php');
GvKernelInclude::instance()->skipFile('src/GvKernel.inc.php');
GvKernelInclude::instance()->skipFile('src/GvKernelModule.inc.php');
GvKernelInclude::instance()->skipFile('src/GvKernelProfile.inc.php');
GvKernelInclude::instance()->skipFile('src/GvKernelExtension.inc.php');

GvKernelInclude::instance()->includeFile('src/system/exception/ArgumentException.inc.php');
GvKernelInclude::instance()->includeFile('src/system/exception/NotImplementedException.inc.php');
GvKernelInclude::instance()->includeFile('src/system/exception/SqlException.inc.php');
