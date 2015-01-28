<?php

return Symfony\CS\Config\Config::create()
    ->finder(
        Symfony\CS\Finder\DefaultFinder::create()
            ->exclude('cache/compiled')
            ->exclude('includes/Smarty')
            ->exclude('js/FCKeditor')
            ->exclude('js/dojo')
            ->exclude('apps/hotglue')
            ->in(__DIR__)
    )
;
