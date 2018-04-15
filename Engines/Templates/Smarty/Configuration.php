<?php

namespace Zed\Engines\Templates\Smarty;

class Configuration {

    ///
    /// Private members
    ///

    /**
     * @var string
     */
    private $applicationDirectory;

    /**
     * @var string
     */
    private $cacheDirectory;

    /**
     * @var string
     */
    private $theme;

    /**
     * @var string
     */
    private $staticContentURL;

    ///
    /// Singleton
    ///

    /**
     * @var Engine
     */
    private static $instance = null;

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
            ->setStaticContentURL($Config['StaticContentURL'])
            ->setTheme(THEME);
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
