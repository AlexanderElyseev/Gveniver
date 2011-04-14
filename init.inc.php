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
set_include_path('/var/www/lib/Smarty-3.0.7/libs'.PATH_SEPARATOR.get_include_path());

define('GV_EXEC', 1);						                    // Execution flag.
define('GV_DS', DIRECTORY_SEPARATOR);		                    // Directory separator.
define('GV_PATH_BASE', dirname(__FILE__).GV_DS);	            // Server base path.
define('GV_PATH_CACHE', GV_PATH_BASE.'cache'.GV_DS);	        // Server cache path.
define('GV_DATE_NOW', date('Y-m-d H:i:s'));	                    // Current date as string.
define('GV_TIME_NOW', time());	                                // Current time at integer.
define('GV_OS_WIN', stripos(PHP_OS, 'win') !== false);          // Is Windows.
define('GV_CLI', stripos(php_sapi_name(), 'cli') !== false);    // CLI.
define('GV_EOL', GV_CLI ? "\n" : '<br/>');                      // End of line.

require 'gveniver/GvConst.inc.php';
require 'gveniver/GvInclude.inc.php';
require 'gveniver/GvExtendCommon.inc.php';
require 'gveniver/GvKernelConfig.inc.php';
require 'gveniver/GvKernel.inc.php';
require 'gveniver/GvKernelModule.inc.php';
require 'gveniver/GvKernelProfile.inc.php';
require 'gveniver/GvKernelExtension.inc.php';

GvInclude::instance()->skipFile('gveniver/GvConst.inc.php');
GvInclude::instance()->skipFile('gveniver/GvExtendCommon.inc.php');
GvInclude::instance()->skipFile('gveniver/GvInclude.inc.php');
GvInclude::instance()->skipFile('gveniver/GvKernelConfig.inc.php');
GvInclude::instance()->skipFile('gveniver/GvKernel.inc.php');
GvInclude::instance()->skipFile('gveniver/GvKernelModule.inc.php');
GvInclude::instance()->skipFile('gveniver/GvKernelProfile.inc.php');
GvInclude::instance()->skipFile('gveniver/GvKernelExtension.inc.php');

GvInclude::instance()->includeFile('gveniver/system/exception/ArgumentException.inc.php');
GvInclude::instance()->includeFile('gveniver/system/exception/NotImplementedException.inc.php');
GvInclude::instance()->includeFile('gveniver/system/exception/SqlException.inc.php');
