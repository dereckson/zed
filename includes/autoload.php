<?php

spl_autoload_register(function (string $className) {
    //Classes
    $classes['IAuthentication'] = './includes/auth/IAuthentication.php';
    $classes['UserPasswordAuthentication'] = './includes/auth/UserPasswordAuthentication.php';
    $classes['YubiCloudAuthentication'] = './includes/auth/YubiCloudAuthentication.php';

    $classes['Cache'] = './includes/cache/cache.php';
    $classes['CacheMemcached'] = './includes/cache/memcached.php';
    $classes['CacheVoid'] = './includes/cache/void.php';

    $classes['ContentFile'] = './includes/content/file.php';
    $classes['ContentLocation'] = './includes/content/location.php';
    $classes['ContentZone'] = './includes/content/zone.php';

    $classes['GeoBody'] = './includes/geo/body.php';
    $classes['GeoGalaxy'] = './includes/geo/galaxy.php';
    $classes['GeoLocation'] = './includes/geo/location.php';
    $classes['GeoPlace'] = './includes/geo/place.php';
    $classes['GeoScene'] = './includes/geo/scene.php';
    $classes['GeoSceneIndex'] = './includes/geo/sceneindex.php';

    $classes['Application'] = './includes/objects/application.php';
    $classes['Content'] = './includes/objects/content.php';
    $classes['Invite'] = './includes/objects/invite.php';
    $classes['Message'] = './includes/objects/message.php';
    $classes['MOTD'] = './includes/objects/motd.php';
    $classes['Perso'] = './includes/objects/perso.php';
    $classes['Port'] = './includes/objects/port.php';
    $classes['Profile'] = './includes/objects/profile.php';
    $classes['ProfileComment'] = './includes/objects/profilecomment.php';
    $classes['ProfilePhoto'] = './includes/objects/profilephoto.php';
    $classes['Request'] = './includes/objects/request.php';
    $classes['RequestReply'] = './includes/objects/requestreply.php';
    $classes['Ship'] = './includes/objects/ship.php';
    $classes['User'] = './includes/objects/user.php';

    $classes['SettingsPage'] = './includes/settings/page.php';
    $classes['Setting'] = './includes/settings/setting.php';
    $classes['Settings'] = './includes/settings/settings.php';

    $classes['StoryChoice'] = './includes/story/choice.php';
    $classes['StoryHook'] = './includes/story/hook.php';
    $classes['DemoStoryHook'] = './includes/story/hook_demo.php';
    $classes['SpatioportStoryHook'] = './includes/story/hook_spatioport.php';
    $classes['StorySection'] = './includes/story/section.php';
    $classes['Story'] = './includes/story/story.php';

    $classes['TravelPlace'] = './includes/travel/place.php';
    $classes['Travel'] = './includes/travel/travel.php';

    //Loader
    if (array_key_exists($className, $classes)) {
        require_once($classes[$className]);
    }
});
