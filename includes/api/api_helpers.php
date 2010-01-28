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
 * @param array $data
 * @param string $rootNodeName - what you want the root node to be - defaultsto data.
 * @param SimpleXMLElement $xml - should only be used recursively
 * @param string $unknownNodeName - name to give to unknown (numeric) keys
 * @return string XML
 */
function toXml($data, $rootNodeName = 'data', $xml = null, $unknownNodeName = 'unknownNode')
{
  if ($rootNodeName == null) $rootNodeName = 'data';
  if ($unknownNodeName == null) $unknownNodeName = 'unknownNode';
  
	// turn off compatibility mode as simple xml throws a wobbly if you don't.
	if (ini_get('zend.ze1_compatibility_mode') == 1)
	{
		ini_set ('zend.ze1_compatibility_mode', 0);
	}
	
	if ($xml == null)
	{
		$xml = simplexml_load_string("<?xml version='1.0' encoding='utf-8'?><$rootNodeName />");
	}
	
	// loop through the data passed in.
	foreach($data as $key => $value)
	{
		// no numeric keys in our xml please!
		if (is_numeric($key))
		{
			// make string key...
			$key = $unknownNodeName . '_'. (string) $key;
		}
		
		// replace anything not alpha numeric
		$key = preg_replace('/[^a-z]/i', '', $key);
		
		// if there is another array found recrusively call this function
		if (is_array($value))
		{
			$node = $xml->addChild($key);
			// recrusive call.
			toXml($value, $rootNodeName, $node, $unknownNodeName);
		}
		else 
		{
			// add single node.
                              $value = htmlentities($value);
			$xml->addChild($key,$value);
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
			echo $bc->format(toXml($reply, $xmlRoot, null, $xmlChildren));
            break;
		
		default:
    		echo "Unknown API format: $_GET[format]";
            break;
	}
}

?>