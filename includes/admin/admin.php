<?php
namespace Affiliate_Bridge\Admin
{
    use \Affiliate_Bridge\Admin\Pages\General;
    use \Affiliate_Bridge\Admin\Pages\SourceList;
    use \Affiliate_Bridge\Admin\Pages\Shortcode_Generator;
    use \Affiliate_Bridge\Admin\Objects\Source;
    use \Affiliate_Bridge\Admin\Objects\Table;

    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Admin
    {
        public function __construct()
        {
            $this->general      = General::init();
            $this->source_list  = SourceList::init();
            $this->generator    = Shortcode_Generator::init();

            $this->admin_actions();
        }

        public static function init()
        {
            new Admin();
        }

        private function admin_actions()
        {
            add_action( 'admin_init', [ $this->general, 'settings' ] );

            add_action( 'admin_menu', [ $this, 'add_admin_pages' ] );

            add_action( 'admin_enqueue_scripts', [ $this, 'enqueue_styles' ] );
        }

        public function add_admin_pages()
        {
            $icon_url = '';
        
            add_menu_page( 
                __( 'Affiliate Bridge', AB_PLUGIN_DOMAIN ),     /* Page Title */
                __( 'Affiliate Bridge', AB_PLUGIN_DOMAIN ),     /* Menu Title */
                'manage_options',                               /* Capability Requred */
                'affiliate-bridge-backend',                     /* Menu Slug */
                [ $this->general, 'display' ],                  /* Function to display page content */
                $icon_url,                                      /* Icon URL */
                '98'                                            /* Menu Position */
            );

            add_submenu_page( 
                'affiliate-bridge-backend',                     /* Parent Slug (matches Menu Slug) */
                __( 'Affiliate Bridge', AB_PLUGIN_DOMAIN ),     /* Page Title */
                __( 'Settings', AB_PLUGIN_DOMAIN ),             /* Menu Title */
                'manage_options',                               /* Capability Required */
                'affiliate-bridge-backend',                     /* Menu Slug */
                [ $this->general, 'display' ],                  /* Function to display page content */
            );

            add_submenu_page( 
                'affiliate-bridge-backend',                     /* Parent Slug (matches Menu Slug) */
                __( 'Affiliate Bridge', AB_PLUGIN_DOMAIN ),     /* Page Title */
                __( 'Sources', AB_PLUGIN_DOMAIN ),              /* Menu Title */
                'manage_options',                               /* Capability Required */
                'affiliate-bridge-sources',                     /* Menu Slug */
                [ $this->source_list, 'display' ],              /* Function to display page content */
            );

            add_submenu_page( 
                'affiliate-bridge-backend',                     /* Parent Slug (matches Menu Slug) */
                __( 'Affiliate Bridge', AB_PLUGIN_DOMAIN ),     /* Page Title */
                __( 'Shortcode Generator', AB_PLUGIN_DOMAIN ),  /* Menu Title */
                'manage_options',                               /* Capability Required */
                'affiliate-bridge-shortcode',                   /* Menu Slug */
                [ $this->generator, 'display' ],                /* Function to display page content */
            );

            $this->add_admin_menu_separator( 97, 'affiliate-bridge' );
        }

        private function add_admin_menu_separator( $position, $slug )
        {
            global $menu;

            $menu[ $position ] = array(
                0   =>  '',
                1   =>  'read',
                2   =>  'separator-' . $slug,
                3   =>  '',
                4   =>  'wp-menu-separator'
            );

            ksort( $menu );
        }

        public function enqueue_styles()
        {
            wp_register_style( 'ab-admin', AB_STYLE_URL . 'admin.css', false, '1.1.0' );

            wp_register_script( 'ab-admin', AB_SCRIPT_URL . 'ab-admin.js' , [ 'jquery' ], '1.1.0', false );
            wp_register_script( 'ab-ebay-auth', AB_SCRIPT_URL . 'ebay-auth.js' , [ 'jquery' ], '1.1.0', false );

            wp_enqueue_style( 'ab-admin' );

            wp_enqueue_script( 'ab-admin' );
            wp_enqueue_script( 'ab-ebay-auth' );
        }
    }
}