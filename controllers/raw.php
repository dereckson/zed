<?php

/**
 * Raw text ou HTML content
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This is a controller printing HTML content as is.
 *
 * It prints the raw.tpl view containing two variables:
 * - PAGE_TITLE (optionnal), to add a H1 page title
 * - CONTENT (should be defined), the content to print
 *
 * To use it:
 * <code>
 * //(1) Assign your HTML content in CONTENT smarty variable:
 * $content = "Hello World!";
 * $smarty->assign('CONTENT', $content);
 *
 * //(2) Call the raw controller
 * include('controllers/raw.php');
 *
 * //That's all folk.
 * </code>
 *
 * To add a title:
 * <code>
 * $content = "Hello World";
 * $title = "Raw sample";
 *
 * $smarty->assign('PAGE_TITLE', $title);
 * $smarty->assign('CONTENT', $content);
 * </code>
 *
 * @package     Zed
 * @subpackage  Controllers
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

//
// HTML output
//

//Serves header
$smarty->assign('PAGE_TITLE', $title);à 
include('header.php'); 

//Serves content
$smarty->display('raw.tpl');

//Serves footer
include('footer.php');
 
?>