<?php
/**
 * File contains class for packing JavaScript data.
 * 
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

namespace Gveniver\Cache\Packer;
require_once 'Lib/class.JavaScriptPacker.php';

/**
 * Class for packing JavaScript data.
 * 
 * @category  Gveniver
 * @package   Cache
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */
class ScriptPacker extends BaseDataPacker
{
    /**
     * Method for packing data.
     * Pack JavaScript content with Dean Edward's packer.
     *
     * @param string $sData Data for packing.
     *
     * @return string
     */
    public function pack($sData)
    {
        $cPacker = new \JavaScriptPacker($sData, 0);
        return $cPacker->pack().";\n";

    } // End function
    //--------------------------------------------------------------------------------

} // End class
//--------------------------------------------------------------------------------