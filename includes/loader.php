<?php
namespace Affiliate_Bridge
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Loader
    {
        const INC_DIR   = __DIR__ . '/';
        const EXT       = '.php';
        
        public static function load( $class )
        {
            require_once( self::INC_DIR . $class . self::EXT );
        }

        public static function load_required()
        {
            $classes = [
                'core',
                'debug',
                'hooks',
                'options',
                'setup',
                'shortcode',
                'imports/'      =>
                [
                    'wp-list-table',
                ],
                'frontend/'      =>
                [
                    'render',
                ],
                'database/'     =>
                [
                    'database',
                ],
                'sources/'      =>
                [
                    'api',
                    'source',
                    'sources',
                    'response',
                    'ebay/'         =>
                    [
                        'response',
                        'trading_api',
                        'finding_api',
                        'shopping_api',
                        'source',
                    ]
                ],
                'admin/'        =>
                [
                    'pages/'        =>
                    [
                        'general',
                        'sourcelist',
                        'shortcode_generator',
                    ],
                    'objects/'      =>
                    [
                        'source',
                        'table',
                        'sources-table',
                    ],
                    'admin',
                ]
            ];

            self::parse_classes( $classes );
        }

        private static function parse_classes( $classes, $folder = '' )
        {
            if ( is_array( $classes ) ) {
                foreach( $classes as $key => $val ) {
                    if ( is_string( $val ) ) {
                        if ( $folder !== '' ) {
                            self::load( $folder . $val );
                        } else {
                            self::load( $val );
                        }
                    } elseif ( is_array( $val ) ) {
                        self::parse_classes( $val, $folder . $key );
                    }
                }
            } elseif ( is_string( $classes ) ) {
                self::load( $classes );
            }
        }
    }
}