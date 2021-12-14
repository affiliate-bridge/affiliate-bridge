<?php
namespace Affiliate_Bridge
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Options
    {
        const OPTION            = 'affiliate-bridge-settings';
        const OPT_VER           = 'ab-version';

        const DEFAULT_OPTIONS   = [
            'ab_image_size'         => 'large',
            'ab_items'              => 1,
            'ab_def_image'          => AB_IMAGE_URL . 'ab-default-image.jpg',
            'ab_framed'             => 'N',
        ];

        const DEFAULT_IMAGE     = AB_IMAGE_URL . 'ab-default-image.jpg';

        private static $options = [];

        public static function init()
        {
            self::refresh();
        }

        private static function refresh()
        {
            self::$options = get_option( self::OPTION );
        }

        public static function get_version()
        {
            if ( ( is_array( self::$options ) && count( self::$options ) ) || self::$options !== false ) {
                if ( isset( self::$options[ self::OPT_VER ] ) ) {
                    return self::$options[ self::OPT_VER ];    
                }
            }
            return false;
        }

        public static function get_option( $key = null )
        {
            if ( is_null( $key ) ) {
                return self::$options;
            } else {
                return self::$options[ $key ];
            }
        }

        public static function create_options()
        {
            self::update_option( self::OPTION, self::DEFAULT_OPTIONS );
        }

        public static function update_option( $opt, $val )
        {
            update_option( $opt, $val );
        }

        public static function delete_options()
        {
            delete_option( self::OPTION );
        }

        public static function upgrade_options()
        {
            do {
                $ver = self::get_version();
                $ver = str_replace( '.', '_', $ver );
                $method = 'upgrade_from_' . $ver;
                self::{$method}();
                self::refresh();
            } while ( self::get_version() != AB_PLUGIN_VERSION );
        }

        private static function upgrade_from_1_0_2()
        {
            self::delete_options();
            self::create_options();

            /* Old Default Plugin Settings to New Default Plugin Settings */
            $new_default_options = [
                'ab_image_size'         => self::$options[ 'ab_image_size' ],
                'ab_items'              => self::$options[ 'ab_items' ],
                'ab_def_image'          => self::$options[ 'ab_def_image' ],
                'ab_framed'             => self::$options[ 'ab_framed' ],
            ];

            self::update_option( self::OPTION, $new_default_options );

            /* Old Ebay Settings to New Ebay Settings */
            $ebay = [
                'app_id'                => self::$options[ 'ab_app_id' ],
                'default_categories'    => self::$options[ 'ab_categories' ],
                'default_condition'     => self::$options[ 'ab_condition' ],
            ];

            $ebay_data = [
                'source'                => 'ebay',
                'settings'              => serialize( $ebay ),
                'priority'              => 0
            ];

            $db     = new Database();
            $db->insert( 'sources', $ebay_data );
        }
    }
}