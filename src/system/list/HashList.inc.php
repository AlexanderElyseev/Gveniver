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
class HashList implements Iterator
{
    /**
     * Adding element setting.
     * Clone objects.
     * 
     * @var int
     */
    const ADD_CLONE = 1;
    //-----------------------------------------------------------------------------

    /**
     * Adding element setting.
     * Add objects by refernece.
     * 
     * @var int
     */   
    const ADD_BY_REF = 2;
    //-----------------------------------------------------------------------------
    
    /**
     * Error handling setting.
     * Return false on error.
     * 
     * @var int
     */   
    const ERROR_RETURN_FALSE = 1;
    //-----------------------------------------------------------------------------

    /**
     * Error handling setting.
     * Throw exception on error.
     * 
     * @var int
     */      
    const ERROR_EXCEPTION = 2;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------
    
    /**
     * Array of elements. Simple array.
     * 
     * @var array
     */
    private $_aData = array();
    //-----------------------------------------------------------------------------
    
    /**
     * Hash-table for quick access to elements.
     * If function {@see HashList::buildHashIndex} returns correct hash index value,
     * then index of element from main list adds to hash as ["hash"] = "index". 
     * 
     * @var array
     */
    private $_aDataHash = array();
    //-----------------------------------------------------------------------------
    
    /**
     * Current method of adding elements.
     * Clone objects by default.
     * 
     * @var int
     */
    private $_addingMethod = HashList::ADD_CLONE;
    //-----------------------------------------------------------------------------
    
    /**
     * Current method of error handling.
     * Return false by default.
     * 
     * @var int
     */
    private $_errorHandlingMethod = HashList::ERROR_RETURN_FALSE;
    //-----------------------------------------------------------------------------
    //-----------------------------------------------------------------------------
    
    /**
     * Base constructor.
     * Initialization by array of elements, if argument is specified.
     * 
     * @param array $array Data for adding to list.
     * 
     * @return void
     */
    public function __construct($array = null)
    {
        // Add each element of array to list.
        if (is_array($array)) 
            foreach ($array as $cElement)
                $this->add($cElement);
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Overloaded clone method for cloning objects in list.
     * 
     * @return void
     */
    public function __clone()
    {
        foreach ($this->_aData as $k => $v)
            if (is_object($v))
                $this->_aData[$k] = clone $v;

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Error report function.
     * Make some specific actions on errors by specified settings.
     * 
     * @param string $sErrorDescription Description of error.
     * 
     * @throws ArgumentException On errors if option ERROR_EXCEPTION is set.
     * @return boolean False if option ERROR_RETURN is set.
     */
    private function _errorHandler($sErrorDescription)
    {
        switch ($this->_errorHandlingMethod) {
        // Return false as operation result on error.
        case HashList::ERROR_RETURN_FALSE:
            return false;
            break;
            
        // Throw exception on error.
        case HashList::ERROR_EXCEPTION:
            throw new ArgumentException(
                sprintf('Error in adding element to list. Reason: %s.', $sErrorDescription)
            );
            break;
            
        // By default, return false as operation result. 
        default:
            return false;
            break;
        }
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Function implements logic of copying elements to main list by specified settings.
     * 
     * @param mixed $cElement Element to copy.
     * 
     * @return mixed Copy of element.
     */
    private function _addCopy($cElement)
    {
        // Specific actions is need for objects only.
        if (!is_object($cElement))
            return $cElement;
        
        switch ($this->_addingMethod) {
        // Do nothing with object.
        case HashList::ADD_BY_REF:
            return $cElement;
            break;
            
        // Clone object.
        case HashList::ADD_CLONE:
            return clone $cElement;
            break;
            
        // By default, clone elements. 
        default:
            return clone $cElement;
            break;
        }

    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Add element to list.
     * Before adding, elements checks by {@see HashList::addCheck} function.
     * After adding, automatically reset current enumeration position.
     * For access to elements by used {@see HashList::buildHash} function.
     * 
     * @param mixed $cElement Element to add. False if option ERROR_RETURN is set.
     * 
     * @throws ArgumentException On errors if option ERROR_EXCEPTION is set.
     * @return boolean True on success.
     */
    public final function add($cElement)
    {
        // Check element before adding.
        if (!$this->addCheck($cElement))
            return $this->_errorHandler('check error');

        // Add to main list.
        $nNewIndex = $this->count();
        $this->_aData[$nNewIndex] = $this->_addCopy($cElement);
        
        // Add to hash-table.
        $sHashValue = $this->buildHashIndex($cElement);
        if ($sHashValue) {
            // Check, is element already added.
            if (isset($this->_aDataHash[$sHashValue]))
                return $this->_errorHandler('hash collision');
            
            $this->_aDataHash[$sHashValue] = $nNewIndex;
            
        } // End if
        
        // Reset current enumeration position.
        $this->reset();
        
        return true;
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Returns element (by reference or not) by specified hash value.
     * 
     * @param string $sHash Hash value for loading element.
     * @param mixed  &$cRef Reference to load result value.
     * 
     * @return Result of operation, if specified refernece variable, or element
     * for loading, if specified only hash value. 
     */
    public final function get($sHash, &$cRef = null)
    {
        // Is need to load result by reference?
        $bByRef = func_num_args() > 1;
        
        // Check existing of element.
        if (!isset($this->_aDataHash[$sHash])) {
            if ($bByRef)
                return false;
                
            return null;
            
        } // End if
        
        // Return result by reference.
        if ($bByRef) {
            $cRef = $this->_aData[$this->_aDataHash[$sHash]];
            return true;
            
        } // End if
        
        // Direct return result.
        return $this->_aData[$this->_aDataHash[$sHash]];
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Checking method before adding elements to list.
     * Function can be overrided at child classes for implements some specific logic.
     * By default, always return true.
     * 
     * @param mixed &$cElement Element to check before adding.
     * 
     * @return boolean True on success.
     */
    protected function addCheck(&$cElement)
    {
        return true;
        
    } // End function
    //-----------------------------------------------------------------------------

    /**
     * Build hash of element.
     * Used for quick acces to element after adding.
     * 
     * @param mixed $cElement Element to build hash before adding.
     * 
     * @return string Hash string for element.
     */
    protected function buildHashIndex($cElement)
    {
        return null;
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Clear list of elements.
     * 
     * @return void
     */
    public final function clear()
    {
        $this->_aData = array();
        $this->_aDataHash = array();
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Iterator.
     * Start from begin of list.
     * 
     * @return void
     */
    public function rewind() 
    {
        reset($this->_aData);
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Setter for configuration parameter: method of copying elements in adding.
     * Use:
     * - {@see HashList::ADD_CLONE}
     * - {@see HashList::ADD_BY_REF}
     * 
     * @param int $nType Type of adding.
     * 
     * @return void
     */
    public final function setAddingMethod($nType)
    {
        $this->_addingMethod = intval($nType);
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Setter for configuration parameter: method of error reporting.
     * Use:
     * - {@see HashList::ERROR_RETURN_FALSE}
     * - {@see HashList::ERROR_EXCEPTION}
     * 
     * @param int $nType Type of error reporting.
     * 
     * @return void
     */
    public final function setErrorHandlingMethod($nType)
    {
        $this->_errorHandlingMethod = intval($nType);
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Iterator.
     * Method return current element.
     * 
     * @return mixed
     */
    public function current() 
    {
        return current($this->_aData);
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Iterator.
     * Method return current index of element.
     * 
     * @return int
     */    
    public function key() 
    {
        return key($this->_aData);
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Iterator.
     * Method return next element in list.
     * 
     * @return mixed
     */    
    public function next() 
    {
        return next($this->_aData);
        
    } // End function
    //-----------------------------------------------------------------------------
    
    /**
     * Iterator.
     * Method for checking boundaries of the list.
     * 
     * @return boolean
     */
    public function valid() 
    {
        return $this->current() !== false;
        
    } // End function
    //-----------------------------------------------------------------------------

} // End class
//-----------------------------------------------------------------------------