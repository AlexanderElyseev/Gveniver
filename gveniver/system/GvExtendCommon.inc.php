<?php
/**
 * File contains extended versions on various PHP functions.
 *
 * @category  Gveniver
 * @package   Kernel
 * @author    Elyseev Alexander <alexander.elyseev@gmail.com>
 * @copyright 2008-2011 Elyseev Alexander
 * @license   http://prof-club.ru/license.txt Prof-Club License
 * @link      http://prof-club.ru
 */

/**
 * Recursive merging of arrays.
 * If key for merge is numeric, element append to list, no over write.
 *
 * @return array|mixed
 */
// @codingStandardsIgnoreStart
function array_merge_recursive_distinct()
// @codingStandardsIgnoreEnd

{
    $arrays = func_get_args();
    $base = array_shift($arrays);
    if (!is_array($base))
        $base = empty($base) ? array() : array($base);
    foreach ($arrays as $append) {
        if (!is_array($append))
            $append = array($append);
        foreach ($append as $key => $value) {
            if (!array_key_exists($key, $base) and !is_numeric($key)) {
                $base[$key] = $append[$key];
                continue;
            } // End if
            if (is_array($value) || is_array($base[$key])) {
                if (is_numeric($key))
                    $base[] = array_merge_recursive_distinct(array(), $append[$key]);
                else
                    $base[$key] = array_merge_recursive_distinct($base[$key], $append[$key]);
            } else if (is_numeric($key)) {
                if (!in_array($value, $base)) $base[] = $value;
            } else {
                $base[$key] = $value;
            } // End else
        } // End foreach
    } // End foreach
    return $base;
} // End function
//-----------------------------------------------------------------------------

/**
 * Аналог стандартной php функции explode, но работающая
 * с учетом экранирования спецсимволов - разделителей.
 *
 * <code>
 * <?php
 *        $result = explode_escaped(',', 'string, piece, group\, item\, item2, next\,asd');
 *        print_r($result);
 *        ?>
 *        Will give:
 *        Array
 *        (
 *            [0] => string
 *            [1] => piece
 *            [2] => group, item, item2
 *            [3] => next,asd
 *        )
 * </code>
 * 
 * @param string $delimiter Разделитель.
 * @param string $string    Строка.
 * 
 * @return array
 */
// @codingStandardsIgnoreStart
function explode_ex($delimiter, $string)
// @codingStandardsIgnoreEnd
{
    $exploded = explode($delimiter, $string);
    $fixed = array();
    for ($k = 0, $l = count($exploded); $k < $l; ++$k) {
        if (isset($exploded[$k][strlen($exploded[$k]) - 1]) && $exploded[$k][strlen($exploded[$k]) - 1] == '\\') {
            if ($k + 1 >= $l) {
                $fixed[] = trim($exploded[$k]);
                break;
            }
            $exploded[$k][strlen($exploded[$k]) - 1] = $delimiter;
            $exploded[$k] .= $exploded[$k + 1];
            array_splice($exploded, $k + 1, 1);
            --$l; 
            --$k;
        } else $fixed[] = trim($exploded[$k]);
    }
    return $fixed;
} // End function
//-----------------------------------------------------------------------------

/**
 * Расширенное экранирование символов для документиооборота.
 * 
 * @param string $sText Текст для экранирования.
 * 
 * @return string
 */
// @codingStandardsIgnoreStart
function addslashes_ex($sText)
// @codingStandardsIgnoreEnd
{
    $sText = str_replace(':', '\:', $sText);
    $sText = str_replace(',', '\,', $sText);
    return $sText;
    
} // End function
//-----------------------------------------------------------------------------
 
/**
 * Function to strip tags and attributes, but with allowable attributes.
 * 
 * <code>
 * <?php strip_tags_ex($string,'<strong><em><a>','href,rel'); ?>
 * </code>
 * 
 * @param string $string          Строка для форматирования.
 * @param string $allowtags       Строка разрешенных тегов.
 * @param string $allowattributes Строка разрешенных аттрибутов (через запятую).
 * 
 * @return string
 */
// @codingStandardsIgnoreStart
function strip_tags_ex($string, $allowtags = null, $allowattributes = null)
// @codingStandardsIgnoreEnd
{ 
    if ($allowattributes) {
        if (!is_array($allowattributes)) 
            $allowattributes = explode(",", $allowattributes); 
        if (is_array($allowattributes)) 
            $allowattributes = implode("|", $allowattributes); 
            
        $rep = '/([^>]*) ('.$allowattributes.')(=)(\'.*\'|".*")/i'; 
        $string = preg_replace($rep, '$1 $2_-_-$4', $string); 
        
    } // End if
    
    if (preg_match('/([^>]*) (.*)(=\'.*\'|=".*")(.*)/i', $string) > 0)
        $string = preg_replace('/([^>]*) (.*)(=\'.*\'|=".*")(.*)/i', '$1$4', $string); 
    
    $rep = '/([^>]*) ('.$allowattributes.')(_-_-)(\'.*\'|".*")/i'; 
    
    if ($allowattributes) 
        $string = preg_replace($rep, '$1 $2=$4', $string); 
        
    return strip_tags($string, $allowtags); 
    
} // End function
//-----------------------------------------------------------------------------

/**
 * Extended version for array_shift to use with a-arrays.
 * 
 * @param array &$arr Array to shift.
 * 
 * @return array
 */
// @codingStandardsIgnoreStart
function array_shift_ex(&$arr)
// @codingStandardsIgnoreEnd
{
    list($k) = array_keys($arr);
    unset($arr[$k]);
    return $arr;

} // End function
//-----------------------------------------------------------------------------

/**
 * Альтернативный метод перевода строки в верхний регистр при проблеме
 * с русским языком в строке.
 * 
 * @param string $str Строка для преобразования.
 * 
 * @return string
 */
// @codingStandardsIgnoreStart
function strtolower_ex($str)
// @codingStandardsIgnoreEnd
{
    $aAlphaLower = array(
        'ё', 'й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 'ъ', 
        'ф', 'ы', 'в', 'а', 'п', 'р', 'о', 'л', 'д', 'ж', 'э', 'я', 'ч',
        'с', 'м', 'и', 'т', 'ь', 'б', 'ю'
    );

    $aAlphaUpper = array(
        'Ё', 'Й', 'Ц', 'У', 'К', 'Е', 'Н', 'Г', 'Ш', 'Щ', 'З', 'Х', 'Ъ',
        'Ф', 'Ы', 'В', 'А', 'П', 'Р', 'О', 'Л', 'Д', 'Ж', 'Э', 'Я', 'Ч',
        'С', 'М', 'И', 'Т', 'Ь', 'Б', 'Ю'
    );
    
    return str_replace($aAlphaUpper, $aAlphaLower, strtolower($str));
    
} // End function
//-------------------------------------------------------------------------------

/**
 * Альтернативный метод перевода строки в нижний регистр при проблеме
 * с русским языком в строке.
 * 
 * @param string $str Строка для преобразования.
 * 
 * @return string
 */
// @codingStandardsIgnoreStart
function strtoupper_ex($str)
// @codingStandardsIgnoreEnd
{
    $aAlphaLower = array(
        'ё', 'й', 'ц', 'у', 'к', 'е', 'н', 'г', 'ш', 'щ', 'з', 'х', 'ъ', 
        'ф', 'ы', 'в', 'а', 'п', 'р', 'о', 'л', 'д', 'ж', 'э', 'я', 'ч',
        'с', 'м', 'и', 'т', 'ь', 'б', 'ю'
    );

    $aAlphaUpper = array(
        'Ё', 'Й', 'Ц', 'У', 'К', 'Е', 'Н', 'Г', 'Ш', 'Щ', 'З', 'Х', 'Ъ',
        'Ф', 'Ы', 'В', 'А', 'П', 'Р', 'О', 'Л', 'Д', 'Ж', 'Э', 'Я', 'Ч',
        'С', 'М', 'И', 'Т', 'Ь', 'Б', 'Ю'
    );
    
    return str_replace($aAlphaLower, $aAlphaUpper, strtoupper($str));
    
} // End function
//-------------------------------------------------------------------------------

/**
 * Function cuts string with simple text by the specified number of chars.
 *
 * @param string $string     Text to cut.
 * @param int    $max_length Cutting length.
 * 
 * @return string
 */
// @codingStandardsIgnoreStart
function str_break_text($string, $max_length)
// @codingStandardsIgnoreEnd
{ 
    if (mb_strlen($string) > $max_length) { 
        $string = mb_substr($string, 0, $max_length); 
        $pos = mb_strrpos($string, ' '); 
        if ($pos === false) 
            return mb_substr($string, 0, $max_length)."..."; 
            
        return mb_substr($string, 0, $pos).'...'; 
        
    } // End if
    else
        return $string; 
    
} // End function
//-------------------------------------------------------------------------------

/**
 * Function cuts string with the HTML tags by the specified number of chars and strips 
 * empty HTML tags from the output.
 *
 * @param string $txt   text to cut
 * @param int    $len   number of chars to keep in the resulting string
 * @param string $delim optional string of the stop-chars, used to split the text when 
 * limit reached in the middle of the current word
 * 
 * @return string
 * @author Ilya Lebedev
 */
// @codingStandardsIgnoreStart
function str_break_html($txt, $len, $delim = '\s;,.!?:#')
// @codingStandardsIgnoreEnd
{
    $txt = preg_replace_callback(
        "#(</?[a-z]+(?:>|\s[^>]*>)|[^<]+)#mi",    // TODO mb_
        create_function(
            '$a',
            'static $len = '.$len.';'
            .'$len1 = $len-1;'
            .'$delim = \''.str_replace("#", "\\#", $delim).'\';'
            .'if ("<" == $a[0]{0}) return $a[0];'
            .'if ($len<=0) return "";'
            .'$res = preg_split("#(.{0,$len1}+(?=[$delim]))|(.{0,$len}[^$delim]*)#ms",$a[0],2,PREG_SPLIT_DELIM_CAPTURE);'
            .'if ($res[1]) { $len -= strlen($res[1])+1; $res = $res[1];}'
            .'else         { $len -= strlen($res[2]); $res = $res[2];}'
            .'$res = rtrim($res);/*preg_replace("#[$delim]+$#m","",$res);*/'
            .'return $res;'
        ),
        $txt
    );
                                  
     while (preg_match("#<([a-z]+)[^>]*>\s*</\\1>#mi", $txt))
         $txt = preg_replace("#<([a-z]+)[^>]*>\s*</\\1>#mi", "", $txt);
     
     return $txt;
     
} // End function
//-------------------------------------------------------------------------------