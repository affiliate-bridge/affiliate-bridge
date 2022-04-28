<?php
namespace Affiliate_Bridge
{
    use \Affiliate_Bridge\Options;
    
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Hooks
    {
        public static function init()
        {
            self::add_actions();
            self::add_filters();
        }

        public static function add_actions()
        {
            // Add frontend style
            add_action('wp_enqueue_scripts', [ __CLASS__, 'add_styles' ] );
        }

        public static function add_filters()
        {
            // Add link to settings from plugins page
            add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), [ __CLASS__, 'settings_link' ] );
        }

        public static function add_styles()
        {
            wp_enqueue_style( 'ab-style', AB_STYLE_URL . 'styles.css', [], Options::get_version(), 'all' );
        }

        public static function settings_link($links)
        {
            $links[] = '<a href="' .
                admin_url( 'options-general.php?page=affiliate-bridge-backend' ) .
                '">' . __( 'Settings', AB_PLUGIN_DOMAIN ) . '</a>';
            return $links;
        }
    }
}