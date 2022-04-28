<?php
namespace Affiliate_Bridge
{
    use \Affiliate_Bridge\Hooks;
    use \Affiliate_Bridge\Options;
    use \Affiliate_Bridge\Shortcode;
    use \Affiliate_Bridge\Admin\Admin;

    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Core
    {
        // general settings
        const PLUGIN_NAME   = AB_PLUGIN_DOMAIN;
        const DOMAIN        = AB_PLUGIN_DOMAIN;

        // link to default plugin image
        private string $plugin_default_image;

        public function __construct()
        {
            Hooks::init();
            Options::init();
            Shortcode::init();

            if ( is_admin() ) {
                Admin::init();
            }
        }

        public static function init()
        {
            new Core();
        }
    }
}