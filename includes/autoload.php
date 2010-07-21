<?php

/**
 * Autoloader
 *
 * Zed. The immensity of stars. The HyperShip. The people.
 * 
 * (c) 2010, Dereckson, some rights reserved.
 * Released under BSD license.
 *
 * This file provides an __autoload function to help loading objects files.
 *
 * This function is autogenerated by the TCL script dev/scripts/autoload.tcl
 * 
 * @package     Zed
 * @subpackage  Keruald
 * @author      Sébastien Santoro aka Dereckson <dereckson@espace-win.org>
 * @copyright   2010 Sébastien Santoro aka Dereckson
 * @license     http://www.opensource.org/licenses/bsd-license.php BSD
 * @version     0.1
 * @link        http://scherzo.dereckson.be/doc/zed
 * @link        http://zed.dereckson.be/
 * @filesource
 */

/**
 * This magic method is called when a class can't be loaded
 *
 * @param string $className the class to load
 */
function __autoload ($className) {  
    //Classes
    $classes['Cache'] = 'includes/cache/cache.php';
    $classes['CacheMemcached'] = 'includes/cache/memcached.php';
    $classes['CacheVoid'] = 'includes/cache/void.php';
 
    $classes['GeoBody'] = 'includes/geo/body.php';
    $classes['GeoGalaxy'] = 'includes/geo/galaxy.php';
    $classes['GeoLocation'] = 'includes/geo/location.php';
    $classes['GeoPlace'] = 'includes/geo/place.php';
    $classes['GeoPoint3D'] = 'includes/geo/point3D.php';
    $classes['GeoScene'] = 'includes/geo/scene.php';
 
    $classes['Application'] = 'includes/objects/application.php';
    $classes['Content'] = 'includes/objects/content.php';
    $classes['Invite'] = 'includes/objects/invite.php';
    $classes['Message'] = 'includes/objects/message.php';
    $classes['MOTD'] = 'includes/objects/motd.php';
    $classes['Perso'] = 'includes/objects/perso.php';
    $classes['Port'] = 'includes/objects/port.php';
    $classes['Profile'] = 'includes/objects/profile.php';
    $classes['ProfileComment'] = 'includes/objects/profilecomment.php';
    $classes['ProfilePhoto'] = 'includes/objects/profilephoto.php';
    $classes['Ship'] = 'includes/objects/ship.php';
    $classes['User'] = 'includes/objects/user.php';
 
    $classes['SettingsPage'] = 'includes/settings/page.php';
    $classes['Setting'] = 'includes/settings/setting.php';
    $classes['Settings'] = 'includes/settings/settings.php';
 
    $classes['StoryChoice'] = 'includes/story/choice.php';
    $classes['StoryHook'] = 'includes/story/hook.php';
    $classes['DemoStoryHook'] = 'includes/story/hook_demo.php';
    $classes['SpatioportStoryHook'] = 'includes/story/hook_spatioport.php';
    $classes['StorySection'] = 'includes/story/section.php';
    $classes['Story'] = 'includes/story/story.php';
 
    $classes['TravelPlace'] = 'includes/travel/place.php';
    $classes['Travel'] = 'includes/travel/travel.php';

    //Loader
    if (array_key_exists($className, $classes)) {
        require_once($classes[$className]);
    }
}

?>