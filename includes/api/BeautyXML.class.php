<?php

/**
 * XML beautifer
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 *
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 *
 * This class is simple XML beautifer
 * it's very, very, very simple - feature version will be better :-)
 *
 * IMPORTANT NOTE
 * there is no warranty, implied or otherwise with this software.
 *
 * version 0.1 | August 2004
 *
 * released under a LGPL licence.
 *
 * Slawomir Jasinski,
 * http://www.jasinski.us (polish only - my home page)
 * http://www.cgi.csd.pl (english & polish)
 * contact me - sj@gex.pl
 *
 * @package     Zed
 * @subpackage  API
 * @author      Slawomir Jasinski <sj@gex.pl>
 * @copyright   2004 Slawomir Jasinski, 2010 Sébastien Santoro aka Dereckson
 * @license     http://www.gnu.org/licenses/lgpl.html LGPL
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 *
 * @todo Contact Slawomir Jasinski and ask it if the current code could be
 * relicensed under BSD license. If not, rewrite from scratch under BSD license.
 */

/**
 * This class is simple XML beautifer.
 * It's very, very, very simple - feature version will be better :-)
 *
 * @author      Slawomir Jasinski <sj@gex.pl>
 */
class BeautyXML {
    /**
     * Indicates what characters to use to indent.
     *
     * If you wish a regular tabulation, the suggested value is \t ;
     * If you wish spaces instead, put the correct amount of spaces as value.
     *
     * @var string
     */
    var $how_to_ident = "    "; // you can user also \t or more/less spaces

    /**
     * Determines if long text have to be wrapped.
     *
     * If true, the text will be wrapped ; otherwise, long lines will be kept.
     *
     * @var bool
     */
    var $wrap = false;

    /**
     * If $wrap is true, determines the line lenght.
     *
     * After this lenght, any text will be wrapped.
     *
     * @see $wrap
     * @var @int
     */
    var $wrap_cont = 80; // where wrap words

    /**
     * Idents the specified string.
     *
     * @param string $str the string to indent
     * @param int $level the ident level, ie the number of identation to prepend the string with
     */
    function ident (&$str, $level) {
        $spaces = '';
        $level--;
        for ($a = 0; $a < $level; $a++)
            $spaces .= $this->how_to_ident;
        return $spaces .= $str;
    }

    /**
     * Formats the specified string, beautifying it, with proper indent.
     *
     * This is the main class method.
     *
     * @param $str the XML fragment to beautify
     * @return string the beautified XML fragment
     */
    function format ($str) {

        $str = preg_replace("/<\?[^>]+>/", "", $str);

		$tmp = explode("\n", $str); // extracting string into array

        // cleaning string from spaces and other stuff like \n \r \t
        for ($a = 0, $c = count($tmp); $a < $c; $a++)
            $tmp[$a] = trim($tmp[$a]);

        // joining to string ;-)
        $newstr = join("", $tmp);

        $newstr = preg_replace("/>([\s]+)<\//", "></", $newstr);

		// adding \n lines where tags ar near
        $newstr = str_replace("><", ">\n<", $newstr);

		// exploding - each line is one XML tag
        $tmp = explode("\n", $newstr);

        // preparing array for list of tags
        $stab = array('');

        // lets go :-)
        for ($a = 0, $c = count($tmp); $a <= $c; $a++) {

            $add = true;

            preg_match("/<([^\/\s>]+)/", $tmp[$a], $match);

            $lan = trim(strtr($match[0], "<>", "  "));

			$level = count($stab);

            if (in_array($lan, $stab) && substr_count($tmp[$a], "</$lan") == 1) {
                $level--;
                $s = array_pop($stab);
                $add = false;
            }

            if (substr_count($tmp[$a], "<$lan") == 1 && substr_count($tmp[$a], "</$lan") == 1)
                $add = false;

			if (preg_match("/\/>$/", $tmp[$a], $match))
				$add = false;

			$tmp[$a] = $this->ident($tmp[$a], $level);

            if ($this->wrap) $tmp[$a] = wordwrap($tmp[$a], $this->wrap_cont, "\n" . $this->how_to_ident . $this->how_to_ident . $this->how_to_ident);

            if ($add && !@in_array($lan, $stab) && $lan != '') array_push($stab, $lan);

        }

        return join("\n", $tmp);
    }

}
