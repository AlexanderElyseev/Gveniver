<?php
/**
 * File contains exception class for PDO execution error.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Exception;

/**
 * PDO execution exception class.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class PdoExecutionException extends BaseException
{
    /**
     * Statement with error.
     *
     * @var \PDOStatement
     */
    private $_cStatement;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------

    /**
     * Class constructor.
     * Initializes instance of exception by {@see \PDOStatement} intance.
     *
     * @param \PDOStatement $cStatement Statement with error.
     */
    public function __construct(\PDOStatement $cStatement)
    {
        $this->_cStatement = $cStatement;

    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Method returns statement with error.
     *
     * @return \PDOStatement
     */
    public function getStatement()
    {
        return $this->_cStatement;

    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------
