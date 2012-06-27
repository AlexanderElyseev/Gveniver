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

namespace Gveniver;

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
            if (!array_key_exists($key, $base) && !is_numeric($key)) {
                $base[$key] = $append[$key];
                continue;
            }
            if (is_array($value) || is_array($base[$key])) {
                if (is_numeric($key))
                    $base[] = array_merge_recursive_distinct(array(), $append[$key]);
                else
                    $base[$key] = array_merge_recursive_distinct($base[$key], $append[$key]);
            } else if (is_numeric($key)) {
                if (!in_array($value, $base))
                    $base[] = $value;
                else
                    $base[$key] = $value;
            } else {
                $base[$key] = $value;
            }
        }
    }
    return $base;
    
} // End function
//-----------------------------------------------------------------------------

/**
 * Analogue of standard PHP function with support of escaped delimiters.
 *
 * <code>
 * <?php
 *        $result = explode_ex(',', 'string, piece, group\, item\, item2, next\,asd');
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
 * @param string $delimiter Delimiter for exploding.
 * @param string $string    String for analyze.
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
 * Function to strip tags and attributes, but with allowable attributes.
 * 
 * <code>
 * <?php strip_tags_ex($string,'<strong><em><a>','href,rel'); ?>
 * </code>
 * 
 * @param string $string          String to format.
 * @param string $allowtags       Accepatable HTML tags.
 * @param string $allowattributes Accepatable HTML attributes.
 * @param int    $nMaxLength      Maximal length of text.
 * @param string $sCropStr        Crop text (for mark crop point).
 * 
 * @return string
 */
// @codingStandardsIgnoreStart
function strip_tags_ex($string, $allowtags = null, $allowattributes = null, $nMaxLength = null, $sCropStr = '...')
// @codingStandardsIgnoreEnd
{
    $cTidy = new \Tidy();
    $cTidy->parseString(
        $string,
        array(
             'clean'          => true,
             'output-xml'     => true,
             'show-body-only' => true,
             'wrap' => 0
        ),
        'UTF8'
    );
    $cTidy->cleanRepair();

    $string = $cTidy->repairString(
        $string,
        array(
            'output-xhtml'    => true,
            'show-body-only'  => true,
            //'alt-text'        => '',
            //'drop-font-tags'  => true,
            'escape-cdata'    => true,
            'hide-comments'   => true,
            'join-styles'     => true,
            'join-classes'    => true,
            'quote-ampersand' => true,
            'quote-marks'     => true,
            'indent-spaces'   => 0,
            'newline'         => 'LF',
            'quote-nbsp'      => true,
            'wrap'            => 0
        ),
        'UTF8'
    );

    // Build array of allowed attributes.
    $aAllowedAttriburtes = array();
    if ($allowattributes) {
        if (is_string($allowattributes))
            $aAllowedAttriburtes = explode(',', $allowattributes);
        elseif (is_array($allowattributes))
            $aAllowedAttriburtes = $allowattributes;
    }

    // Build array of allowed tags.
    $aAllowedTags = $allowtags ? explode(' ', $allowtags) : array();
    for ($i = 0; $i < count($aAllowedTags); $i++) {
        $aAllowedTags[$i] = str_replace('>', '', $aAllowedTags[$i]);
        $aAllowedTags[$i] = str_replace('<', '', $aAllowedTags[$i]);
        $aAllowedTags[$i] = trim($aAllowedTags[$i]);
    }

    $string = html_convert_entities($string);

    $cDom = new \DomDocument();
    $cDom->loadXML("<xml_main>$string</xml_main>");
    $cXml = $cDom->getElementsByTagName('xml_main')->item(0);

    // Function recursive walking over DOM tree and removing not allowed tags and attributes.
    $checkTree = function(\DOMNode &$cElement) use (&$aAllowedAttriburtes, &$aAllowedTags, &$checkTree) {

        $aNodesForDelete = array();
        foreach ($cElement->childNodes as $cNode) {
            /** @var $cNode \DOMElement */
            if ($cNode->nodeType != XML_ELEMENT_NODE)
                continue;

            // Remove not allowed tags.
            if (!in_array($cNode->tagName, $aAllowedTags)) {
                $aNodesForDelete[] = $cNode;
                continue;
            }

            // Remove not allowed attributes.
            foreach ($cNode->attributes as $cAttribute) {

                /** @var $cAttribute \DOMAttr */

                if (!in_array($cAttribute->name, $aAllowedAttriburtes))
                    $cNode->removeAttribute($cAttribute->name);
            }

            // Parse childs.
            $checkTree($cNode);
        }

        // Lazy deleting of marked nodes.
        foreach ($aNodesForDelete as $cNode)
            $cElement->removeChild($cNode);
    };
    $checkTree($cXml);

    // Check maximla length of text.
    if ($nMaxLength) {
        $nLength = 0;
        $bStop = false;
        $fCropText = function(\DOMNode &$cElement) use (&$nLength, &$fCropText, &$bStop, $sCropStr, $nMaxLength) {

            // Recursive walking over DOM tree, while length of text less then specified.
            // After exceeding the limit of length, deleting all other nodes.
            $aNodesForDelete = array();
            foreach ($cElement->childNodes as $cNode) {
                /** @var $cNode \DOMElement */
                if ($bStop) {
                    $aNodesForDelete[] = $cNode;
                    continue;
                }

                if ($cNode->nodeType == XML_TEXT_NODE) {
                    $nStartLength = $nLength;
                    $nLength += mb_strlen(trim($cNode->textContent));
                    if ($nLength > $nMaxLength) {
                        $bStop = true;
                        $cNode->nodeValue = str_break_text($cNode->nodeValue, $nMaxLength - $nStartLength, $sCropStr);
                    }
                }

                if ($cNode->nodeType == XML_ELEMENT_NODE)
                    $fCropText($cNode);
            }

            // Lazy deleting of marked nodes.
            foreach ($aNodesForDelete as $cNode)
                $cElement->removeChild($cNode);
        };
        $fCropText($cXml);

    } // End if
    
    $string = html_entity_decode($cDom->saveHTML($cXml), ENT_COMPAT, 'UTF-8');
    $string = substr($string, 10, $string - 11);
    return $string;

} // End function
//-----------------------------------------------------------------------------

/**
 * Convert HTML entities to XML entities.
 *
 * @param string $string String to convert.
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function html_convert_entities($string)
// @codingStandardsIgnoreEnd
{
    return preg_replace_callback(
        '/&([a-zA-Z][a-zA-Z0-9]+);/',
        '\\Gveniver\\convert_entity',
        $string
    );

} // End function
//-----------------------------------------------------------------------------

/**
 * Swap HTML named entity with its numeric equivalent. If the entity
 * isn't in the lookup table, this function returns a blank, which
 * destroys the character in the output - this is probably the
 * desired behaviour when producing XML.
 *
 * @param string $matches String to replace.
 *
 * @link http://inanimatt.com/php-convert-entities.html
 * @return string
 */
// @codingStandardsIgnoreStart
function convert_entity($matches)
// @codingStandardsIgnoreEnd
{
    static $table = array(
        'quot' => '&#34;',
        'amp' => '&#38;',
        'lt' => '&#60;',
        'gt' => '&#62;',
        'OElig' => '&#338;',
        'oelig' => '&#339;',
        'Scaron' => '&#352;',
        'scaron' => '&#353;',
        'Yuml' => '&#376;',
        'circ' => '&#710;',
        'tilde' => '&#732;',
        'ensp' => '&#8194;',
        'emsp' => '&#8195;',
        'thinsp' => '&#8201;',
        'zwnj' => '&#8204;',
        'zwj' => '&#8205;',
        'lrm' => '&#8206;',
        'rlm' => '&#8207;',
        'ndash' => '&#8211;',
        'mdash' => '&#8212;',
        'lsquo' => '&#8216;',
        'rsquo' => '&#8217;',
        'sbquo' => '&#8218;',
        'ldquo' => '&#8220;',
        'rdquo' => '&#8221;',
        'bdquo' => '&#8222;',
        'dagger' => '&#8224;',
        'Dagger' => '&#8225;',
        'permil' => '&#8240;',
        'lsaquo' => '&#8249;',
        'rsaquo' => '&#8250;',
        'euro' => '&#8364;',
        'fnof' => '&#402;',
        'Alpha' => '&#913;',
        'Beta' => '&#914;',
        'Gamma' => '&#915;',
        'Delta' => '&#916;',
        'Epsilon' => '&#917;',
        'Zeta' => '&#918;',
        'Eta' => '&#919;',
        'Theta' => '&#920;',
        'Iota' => '&#921;',
        'Kappa' => '&#922;',
        'Lambda' => '&#923;',
        'Mu' => '&#924;',
        'Nu' => '&#925;',
        'Xi' => '&#926;',
        'Omicron' => '&#927;',
        'Pi' => '&#928;',
        'Rho' => '&#929;',
        'Sigma' => '&#931;',
        'Tau' => '&#932;',
        'Upsilon' => '&#933;',
        'Phi' => '&#934;',
        'Chi' => '&#935;',
        'Psi' => '&#936;',
        'Omega' => '&#937;',
        'alpha' => '&#945;',
        'beta' => '&#946;',
        'gamma' => '&#947;',
        'delta' => '&#948;',
        'epsilon' => '&#949;',
        'zeta' => '&#950;',
        'eta' => '&#951;',
        'theta' => '&#952;',
        'iota' => '&#953;',
        'kappa' => '&#954;',
        'lambda' => '&#955;',
        'mu' => '&#956;',
        'nu' => '&#957;',
        'xi' => '&#958;',
        'omicron' => '&#959;',
        'pi' => '&#960;',
        'rho' => '&#961;',
        'sigmaf' => '&#962;',
        'sigma' => '&#963;',
        'tau' => '&#964;',
        'upsilon' => '&#965;',
        'phi' => '&#966;',
        'chi' => '&#967;',
        'psi' => '&#968;',
        'omega' => '&#969;',
        'thetasym' => '&#977;',
        'upsih' => '&#978;',
        'piv' => '&#982;',
        'bull' => '&#8226;',
        'hellip' => '&#8230;',
        'prime' => '&#8242;',
        'Prime' => '&#8243;',
        'oline' => '&#8254;',
        'frasl' => '&#8260;',
        'weierp' => '&#8472;',
        'image' => '&#8465;',
        'real' => '&#8476;',
        'trade' => '&#8482;',
        'alefsym' => '&#8501;',
        'larr' => '&#8592;',
        'uarr' => '&#8593;',
        'rarr' => '&#8594;',
        'darr' => '&#8595;',
        'harr' => '&#8596;',
        'crarr' => '&#8629;',
        'lArr' => '&#8656;',
        'uArr' => '&#8657;',
        'rArr' => '&#8658;',
        'dArr' => '&#8659;',
        'hArr' => '&#8660;',
        'forall' => '&#8704;',
        'part' => '&#8706;',
        'exist' => '&#8707;',
        'empty' => '&#8709;',
        'nabla' => '&#8711;',
        'isin' => '&#8712;',
        'notin' => '&#8713;',
        'ni' => '&#8715;',
        'prod' => '&#8719;',
        'sum' => '&#8721;',
        'minus' => '&#8722;',
        'lowast' => '&#8727;',
        'radic' => '&#8730;',
        'prop' => '&#8733;',
        'infin' => '&#8734;',
        'ang' => '&#8736;',
        'and' => '&#8743;',
        'or' => '&#8744;',
        'cap' => '&#8745;',
        'cup' => '&#8746;',
        'int' => '&#8747;',
        'there4' => '&#8756;',
        'sim' => '&#8764;',
        'cong' => '&#8773;',
        'asymp' => '&#8776;',
        'ne' => '&#8800;',
        'equiv' => '&#8801;',
        'le' => '&#8804;',
        'ge' => '&#8805;',
        'sub' => '&#8834;',
        'sup' => '&#8835;',
        'nsub' => '&#8836;',
        'sube' => '&#8838;',
        'supe' => '&#8839;',
        'oplus' => '&#8853;',
        'otimes' => '&#8855;',
        'perp' => '&#8869;',
        'sdot' => '&#8901;',
        'lceil' => '&#8968;',
        'rceil' => '&#8969;',
        'lfloor' => '&#8970;',
        'rfloor' => '&#8971;',
        'lang' => '&#9001;',
        'rang' => '&#9002;',
        'loz' => '&#9674;',
        'spades' => '&#9824;',
        'clubs' => '&#9827;',
        'hearts' => '&#9829;',
        'diams' => '&#9830;',
        'nbsp' => '&#160;',
        'iexcl' => '&#161;',
        'cent' => '&#162;',
        'pound' => '&#163;',
        'curren' => '&#164;',
        'yen' => '&#165;',
        'brvbar' => '&#166;',
        'sect' => '&#167;',
        'uml' => '&#168;',
        'copy' => '&#169;',
        'ordf' => '&#170;',
        'laquo' => '&#171;',
        'not' => '&#172;',
        'shy' => '&#173;',
        'reg' => '&#174;',
        'macr' => '&#175;',
        'deg' => '&#176;',
        'plusmn' => '&#177;',
        'sup2' => '&#178;',
        'sup3' => '&#179;',
        'acute' => '&#180;',
        'micro' => '&#181;',
        'para' => '&#182;',
        'middot' => '&#183;',
        'cedil' => '&#184;',
        'sup1' => '&#185;',
        'ordm' => '&#186;',
        'raquo' => '&#187;',
        'frac14' => '&#188;',
        'frac12' => '&#189;',
        'frac34' => '&#190;',
        'iquest' => '&#191;',
        'Agrave' => '&#192;',
        'Aacute' => '&#193;',
        'Acirc' => '&#194;',
        'Atilde' => '&#195;',
        'Auml' => '&#196;',
        'Aring' => '&#197;',
        'AElig' => '&#198;',
        'Ccedil' => '&#199;',
        'Egrave' => '&#200;',
        'Eacute' => '&#201;',
        'Ecirc' => '&#202;',
        'Euml' => '&#203;',
        'Igrave' => '&#204;',
        'Iacute' => '&#205;',
        'Icirc' => '&#206;',
        'Iuml' => '&#207;',
        'ETH' => '&#208;',
        'Ntilde' => '&#209;',
        'Ograve' => '&#210;',
        'Oacute' => '&#211;',
        'Ocirc' => '&#212;',
        'Otilde' => '&#213;',
        'Ouml' => '&#214;',
        'times' => '&#215;',
        'Oslash' => '&#216;',
        'Ugrave' => '&#217;',
        'Uacute' => '&#218;',
        'Ucirc' => '&#219;',
        'Uuml' => '&#220;',
        'Yacute' => '&#221;',
        'THORN' => '&#222;',
        'szlig' => '&#223;',
        'agrave' => '&#224;',
        'aacute' => '&#225;',
        'acirc' => '&#226;',
        'atilde' => '&#227;',
        'auml' => '&#228;',
        'aring' => '&#229;',
        'aelig' => '&#230;',
        'ccedil' => '&#231;',
        'egrave' => '&#232;',
        'eacute' => '&#233;',
        'ecirc' => '&#234;',
        'euml' => '&#235;',
        'igrave' => '&#236;',
        'iacute' => '&#237;',
        'icirc' => '&#238;',
        'iuml' => '&#239;',
        'eth' => '&#240;',
        'ntilde' => '&#241;',
        'ograve' => '&#242;',
        'oacute' => '&#243;',
        'ocirc' => '&#244;',
        'otilde' => '&#245;',
        'ouml' => '&#246;',
        'divide' => '&#247;',
        'oslash' => '&#248;',
        'ugrave' => '&#249;',
        'uacute' => '&#250;',
        'ucirc' => '&#251;',
        'uuml' => '&#252;',
        'yacute' => '&#253;',
        'thorn' => '&#254;',
        'yuml' => '&#255;'

    );
    // Entity not found? Destroy it.
    return isset($table[$matches[1]]) ? $table[$matches[1]] : '';

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
 * Function cuts string with simple text by the specified number of chars.
 *
 * @param string $string     Text to cut.
 * @param int    $nMaxLength Cutting length.
 * @param string $sCropStr   Text for appending (for mark crop point).
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function str_break_text($string, $nMaxLength, $sCropStr = '...')
// @codingStandardsIgnoreEnd
{ 
    if (mb_strlen($string) > $nMaxLength) {
        $string = mb_substr($string, 0, $nMaxLength);
        $pos = mb_strrpos($string, ' '); 
        if ($pos === false) 
            return mb_substr($string, 0, $nMaxLength).$sCropStr;
            
        return mb_substr($string, 0, $pos).$sCropStr;
        
    } // End if
    
    return $string;
    
} // End function
//-------------------------------------------------------------------------------

/**
 * Format text for outputting.
 *
 * @param string $sText              Text to output.
 * @param int    $nMaxLength         Maximal length.
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function output_text($sText, $nMaxLength = null)
// @codingStandardsIgnoreEnd
{
    if ($nMaxLength)
        $sText = str_break_text($sText, $nMaxLength);

    return strip_tags_ex($sText);

} // End function
//-------------------------------------------------------------------------------

/**
 * Format HTML content for outputting.
 *
 * @param string $sText              Text to output.
 * @param int    $nMaxLength         Maximal length.
 * @param string $sAllowedTags       List of allowed HTML tags.
 * @param string $sAllowedAttributes List of allowed HTML attributes.
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function output_html($sText, $nMaxLength = null, $sAllowedTags = null, $sAllowedAttributes = null)
// @codingStandardsIgnoreEnd
{
    return strip_tags_ex($sText, $sAllowedTags, $sAllowedAttributes, $nMaxLength);

} // End function
//-------------------------------------------------------------------------------

/**
 * Check url correctness.
 *
 * @param string $sUrl Url to check.
 *
 * @return boolean True if correct.
 */
// @codingStandardsIgnoreStart
function is_correct_url($sUrl)
// @codingStandardsIgnoreEnd
{
    $sPattern = '{
        (?:
        (\w+://)
        |
        www\.
        )
        [\w-]+(\.[\w-]+)*
        \S*
        (?:
        (?<! [[:punct:]])
        | (?<= [-/&+*])
        )
    }xis';

    return preg_match($sPattern, $sUrl);

} // End function
//-----------------------------------------------------------------------------

/**
 * Replace special symbols for XML CDATA.
 *
 * @param string $sContent Text for replacing.
 *
 * @return string
 */
function cdata($sContent)
{
    return '<![CDATA['.str_replace(']]>', ']]]]><![CDATA[>', $sContent).']]>';

} // End function
//-----------------------------------------------------------------------------

/**
 * Send the mail.
 *
 * @param string $sFrom    Author of message.
 * @param string $sTo      Recipient of message.
 * @param string $sSubject Subject of message.
 * @param string $sText    Text of message.
 *
 * @return bool True on success
 */
// @codingStandardsIgnoreStart
function mail($sFrom, $sTo, $sSubject, $sText)
// @codingStandardsIgnoreEnd
{
    // Build e-mail headers.
    $sHeaders = sprintf("From: %s\r\n", $sFrom);
    $sHeaders .= "MIME-Version: 1.0\r\n";
    $sHeaders .= "Content-Type: text/plain; charset=\"UTF-8\"\r\n";

    // Standart sending.
    // TODO: check address.
    return \mail(
        $sTo,
        $sSubject,
        $sText,
        $sHeaders
    );

} // End function
//-----------------------------------------------------------------------------

/**
 * Function to canonicalize a URL containing relative paths.
 *
 * @param string $sAddress Address for analyzing.
 *
 * @return string
 */
// @codingStandardsIgnoreStart
function realurl($sAddress)
// @codingStandardsIgnoreEnd
{
    $address = explode('/', $sAddress);
    $keys = array_keys($address, '..');

    foreach($keys AS $keypos => $key)
        array_splice($address, $key - ($keypos * 2 + 1), 2);

    $address = implode('/', $address);
    return str_replace('./', '', $address);

} // End function
//-----------------------------------------------------------------------------

/**
 * Check, is this path is absolute.
 *
 * @param string $sPath Path to check.
 *
 * @return bool True if absolute.
 */
function isAbsolutePath($sPath)
{
    if (defined('GV_OS_WIN') && GV_OS_WIN)
        return preg_match('/^[a-z]:/i', $sPath);

    return isset($sPath[0]) && $sPath[0] === '/';

} // End function
//-----------------------------------------------------------------------------

/**
 * Correction method of the directory separator character.
 * Replace all wrong characters to correct directory separator.
 *
 * @param string $sFileName        Name of file.
 * @param bool   $bAddDirSeparator Is need to append directory separator to end of path.
 *
 * @return string
 */
function correctPath($sFileName, $bAddDirSeparator = false)
{
    $sFileName = (GV_DS === '/')
        ? str_replace('\\', GV_DS, $sFileName)
        : str_replace('/', GV_DS, $sFileName);

    if (!isAbsolutePath($sFileName))
        $sFileName = GV_PATH_BASE.$sFileName;

    $sRealFileName = realpath($sFileName);
    $sFileName =  $sRealFileName ? $sRealFileName : $sFileName;

    if (($bAddDirSeparator || is_dir($sFileName)) && substr($sFileName, -1) != GV_DS)
        $sFileName = $sFileName.GV_DS;

    return $sFileName;

} // End function
//-----------------------------------------------------------------------------

/**
 * Method deletes specified dirrectory with all subdurrectories and files.
 *
 * @param string $sDirectoryPath Path to directory for removing.
 *
 * @return boolean
 */
function rrmdir($sDirectoryPath)
{
    if (!file_exists($sDirectoryPath) || !is_dir($sDirectoryPath))
        return false;

    $bResult = true;
    foreach (glob($sDirectoryPath . '/*') as $sFileName) {
        if (is_dir($sFileName))
            $bResult = $bResult && rrmdir($sFileName);
        else
            $bResult = $bResult && unlink($sFileName);

        if (!$bResult)
            break;
    }
    return $bResult && rmdir($sDirectoryPath);

} // End function
//-----------------------------------------------------------------------------