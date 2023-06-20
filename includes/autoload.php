<?php

spl_autoload_register(function (string $className) {
    //Classes
    //The autoload for classes in PSR-4 namespaces is handled by Composer
    //This function takes care of the classes still in root namespace.

    $classes['IAuthentication'] = './includes/auth/IAuthentication.php';
    $classes['UserPasswordAuthentication'] = './includes/auth/UserPasswordAuthentication.php';
    $classes['YubiCloudAuthentication'] = './includes/auth/YubiCloudAuthentication.php';

    $classes['Cache'] = './includes/cache/cache.php';
    $classes['CacheMemcached'] = './includes/cache/memcached.php';
    $classes['CacheVoid'] = './includes/cache/void.php';

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
