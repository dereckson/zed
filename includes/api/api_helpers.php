<?php

/**
 * API helper functions
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This file provides a functions to output the API message in several formats.
 *
 * The supported formats are preview (PHP dump), XML, PHP serialize, WDDX
 * and json.
 *
 * The XML outputs code uses the following codes:
 *     - http://www.thedeveloperday.com/xml-beautifier-tool/
 *     - http://snipplr.com/view/3491/convert-php-array-to-xml-or-simple-xml-object-if-you-wish/
 * 
 * @package     Zed
 * @subpackage  API
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * The main function for converting to an XML document.
 * 
 * Pass in a multi dimensional array and this recursively loops through
 * and builds up an XML document.
 *
 * @param mixed $data
 * @param string $rootNodeName What you want the root node to be - defaultsto data.
 * @param SimpleXMLElement $xml Should only be used recursively
 * @param string $unknownNodeName Name to give to unknown (numeric) keys
 * @return string XML
 */
function toXml($data, $rootNodeName = 'data', $xml = null, $unknownNodeName = 'unknownNode')
{
  if (!$rootNodeName) $rootNodeName = 'data';
  if (!$unknownNodeName) $unknownNodeName = 'unknownNode';
  
    // turn off compatibility mode as simple xml throws a wobbly if you don't.
    if (ini_get('zend.ze1_compatibility_mode') == 1)
        ini_set('zend.ze1_compatibility_mode', 0);
    
    if ($xml == null) {
        if (!is_array($data) && !is_object($data)) {
            //We've a singleton
            if (is_bool($data)) $data = $data ? 'true' : 'false';
            return "<?xml version='1.0' encoding='utf-8'?><$rootNodeName>$data</$rootNodeName>";
        }

        //Starts with simple document
        $xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
    }
    
    // loop through the data passed in.
    foreach($data as $key => $value) {
        // no numeric keys in our xml please!
        if (is_numeric($key)) {
            // make string key...
            $key = $unknownNodeName . '_'. (string) $key;
        }
        
        // replace anything not alpha numeric
        $key = preg_replace('/[^a-z]/i', '', $key);
        
        //If there is another array found recrusively call this function
        if (is_array($value)) {
            $node = $xml->addChild($key);
            //Recursive call.
            toXml($value, $rootNodeName, $node, $unknownNodeName);
        } elseif (is_object($value)) {
            $node = $xml->addChild($key);
            foreach ($value as $subkey => $subvalue) {
                if ($subkey == "lastError") continue;
                if ($subvalue === null) {
                    //Ignore null values
                    continue;
                } elseif (is_array($subvalue) || is_object($subvalue)) {
                    //TODO: test this
                    //Recursive call.
                    $subnode = $node->addChild($subkey);
                    toXml($subvalue, $rootNodeName, $subnode, $unknownNodeName);
                } elseif (is_bool($subvalue)) {
                    $node->addChild($subkey, $subvalue ? 'true' : 'false');
                } else {
                    $node->addChild($subkey, htmlentities($subvalue));
                }
            }
            //die();
            //$array = array();
            //$node = $xml->addChild($key);
            //toXml($value, $rootNodeName, $node, $unknownNodeName);
        } elseif (is_bool($value)) {
            $xml->addChild($key, $value ? 'true' : 'false');
        } else {
            //Adds single node.
            if ($value || $value === 0) {
                $value = htmlentities($value);
                $xml->addChild($key,$value);
            }
        }
        
    }
    // pass back as string. or simple xml object if you want!
    return $xml->asXML();
}

/**
 * Outputs API reply, printing it in the specified format.
 *
 * The format will be read form $_REQUEST['format'].
 *
 * @param mixed $reply the reply to format
 * @param string $xmlRoot the XML root element name (optionnal, default value is 'data').
 * @param string $xmlChildren the XML children elements name (optionnal, will be deducted from the context if ommited, or, if not posssible, will be unknownNode)
 */
function api_output ($reply, $xmlRoot = null, $xmlChildren = null) {
    $format = isset($_REQUEST['format']) ? $_REQUEST['format'] : 'preview';
    switch ($format) {
        case 'preview':
            echo '<pre>';
            print_r($reply);
            echo '</pre>';
            break;
        
        case 'php':
            echo serialize($reply);
            break;
        
        case 'wddx':
            require_once('BeautyXML.class.php');
            $bc = new BeautyXML();
            echo $bc->format(wddx_serialize_value($reply));
            break;
        
        case 'json':
            echo json_encode($reply);
            break;
        
        case 'xml':
            require_once('BeautyXML.class.php');
            $bc = new BeautyXML();
            echo '<?xml version="1.0" encoding="utf-8"?>';
            echo "\n";
            echo $bc->format(toXml($reply, $xmlRoot, null, $xmlChildren));
            break;
        
        case 'string':
            echo $reply;
            break;
        
        default:
            echo "Unknown API format: $_GET[format]";
            break;
    }
}

?>