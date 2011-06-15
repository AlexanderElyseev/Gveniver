<?php
/**
 * File contains base abstract class for packing data.
 * 
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver;

/**
 * Base abstract class for packing data.
 *
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 * @abstract
 */
abstract class DataPacker
{
    /**
     * Method for packing data.
     * 
     * @param string $sData Data for packing.
     *
     * @return string
     * @abstract
     */
    abstract public function pack($sData);
    //--------------------------------------------------------------------------------

} // End class
//--------------------------------------------------------------------------------