<?php
namespace Affiliate_Bridge
{
    use \Affiliate_Bridge\Database\Database;

    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Setup
    {
        public static function activate()
        {
            $just_installed = Options::get_version();
            // $just_installed = false;
            if ( $just_installed === false ) {
                self::install();
            } elseif ( $just_installed != Options::VERSION ) {
                self::upgrade();
            }
        }

        public static function install()
        {
            Options::create_options();
            Database::create_tables();
        }

        public static function upgrade()
        {
            Options::upgrade_options();
            Database::upgrade_tables();
        }

        public static function deactivate()
        {
            
        }

        public static function uninstall()
        {
            Options::delete_options();
            Database::delete_tables();
        }
    }
}