<?php

namespace Zed\Engines\Templates\Smarty;

use Smarty;

class Engine {

    ///
    /// Private members
    ///

    /**
     * @var Smarty
     */
    private $smarty;

    /**
     * @var Configuration
     */
    private $config;

    ///
    /// Singleton
    ///

    /**
     * @var Engine
     */
    private static $instance = null;

    public static function load () : Engine {
        if (self::$instance === null) {
            self::$instance = new self;
        }

        return self::$instance;
    }

    ///
    /// Constructor
    ///

    public function __construct (
        ?Smarty $smarty = null,
        ?Configuration $config = null
    ) {
        $this->config = $config ?? Configuration::load();
        $this->smarty = $smarty ?? $this->buildDefaultSmarty();
    }

    ///
    /// Factory methods
    ///

    private function buildDefaultSmarty () : Smarty {
        $smarty = new Smarty();

        $smarty
            ->setCacheDir($this->config->getCacheDirectory())
            ->setCompileDir($this->config->getCompileDirectory())
            ->setTemplateDir($this->config->getTemplateDirectory())
            ->setConfigDir($this->config->getApplicationDirectory())
            ->addPluginsDir($this->config->getPluginsDirectory());

        self::initializeDefaultVariables($smarty);

        $smarty->config_vars += [
            'StaticContentURL' => $this->config->getStaticContentURL(),
        ];

        return $smarty;
    }

    private static function initializeDefaultVariables (Smarty $smarty) {
        $smarty->assign("PAGE_CSS", []);
        $smarty->assign("PAGE_JS", []);
    }

    ///
    /// Getters
    ///

    public function getSmarty () : Smarty {
        return $this->smarty;
    }

}
