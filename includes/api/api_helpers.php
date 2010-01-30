<?php
//
// API helpers function
//
// XML outputs code uses:
//  - http://www.thedeveloperday.com/xml-beautifier-tool/
//  - http://snipplr.com/view/3491/convert-php-array-to-xml-or-simple-xml-object-if-you-wish/

/**
 * The main function for converting to an XML document.
 * Pass in a multi dimensional array and this recrusively loops through and builds up an XML document.
 *
 * @param mixed $data
 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
 * @param SimpleXMLElement $xml - should only be used recursively
 * @param string $unknownNodeName - name to give to unknown (numeric) keys
 * @return string XML
 */
function toXml($data, $rootNodeName = 'data', $xml = null, $unknownNodeName = 'unknownNode')
{
  if (!$rootNodeName) $rootNodeName = 'data';
  if (!$unknownNodeName) $unknownNodeName = 'unknownNode';
  
	// turn off compatibility mode as simple xml throws a wobbly if you don't.
	if (ini_get('zend.ze1_compatibility_mode') == 1)
		ini_set ('zend.ze1_compatibility_mode', 0);
	
	if ($xml == null) {
        if (!is_array($data) && !is_object($data)) {
            //We've a singleton
            return "<?xml version='1.0' encoding='utf-8'?><$rootNodeName>$data<$rootNodeName>";
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
                } else {
                    $node->addChild($subkey, htmlentities($subvalue));
                }
            }
            //die();
            //$array = array();
            //$node = $xml->addChild($key);
            //toXml($value, $rootNodeName, $node, $unknownNodeName);
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