<?php

namespace Zed\Engines\Templates\Smarty;

class Configuration {

    ///
    /// Constants
    ///

    const DEFAULT_THEME = "";

    ///
    /// Private members
    ///

    private string $applicationDirectory;

    private string $cacheDirectory;

    private string $theme;

    private string $staticContentURL;

    ///
    /// Singleton
    ///

    private static ?Configuration $instance = null;

    public static function load () : Configuration {
        if (self::$instance === null) {
            self::$instance = self::buildDefaultConfiguration();
        }

        return self::$instance;
    }

    ///
    /// Factory methods
    ///

    public static function buildDefaultConfiguration() : Configuration {
        global $Config;

        return (new self)
            ->setApplicationDirectory(dirname(__DIR__, 3))
            ->setCacheDirectory(CACHE_DIR)
            ->setTheme(self::getDefaultTheme())
            ->setStaticContentURL($Config['StaticContentURL']);
    }

    private static function getDefaultTheme () : string {
        if (defined("THEME")) {
            return THEME;
        }

        return self::DEFAULT_THEME;
    }

    ///
    /// Getters and setters
    ///

    public function getApplicationDirectory () : string {
        return $this->applicationDirectory;
    }

    public function setApplicationDirectory (string $dir) : Configuration {
        $this->applicationDirectory = $dir;

        return $this;
    }

    public function getCacheDirectory () : string {
        return $this->cacheDirectory;
    }

    public function setCacheDirectory (string $dir) : Configuration {
        $this->cacheDirectory = $dir;

        return $this;
    }

    public function getTheme () : string {
        return $this->theme;
    }

    public function setTheme (string $theme) : Configuration {
        $this->theme = $theme;

        return $this;
    }

    public function getStaticContentURL () : string {
        return $this->staticContentURL;
    }

    public function setStaticContentURL (string $url) : Configuration {
        $this->staticContentURL = $url;

        return $this;
    }

    ///
    /// Helper methods
    ///

    public function getTemplateDirectory () : string {
        return $this->applicationDirectory . '/skins/' . $this->theme;
    }

    public function getCompileDirectory () : string {
        return $this->cacheDirectory . '/compiled';
    }

    public function getPluginsDirectory () : string {
        return __DIR__ . '/Plugins';
    }

}
