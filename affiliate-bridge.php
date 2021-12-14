<?php
/**
 * Plugin Name: Affiliate Bridge
 * Plugin URI:  https://affiliate-bridge.com
 * Description: Easily add product images from affiliate programs using shortcodes.
 * Version:     1.1.0
 * Author:      David Lidor
 * Author URI:  https://www.bicycle-riding.com
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: affiliate-bridge
 * Domain Path: /languages
 *
 */
namespace Affiliate_Bridge
{
    if ( ! defined( 'ABSPATH' ) ) {
        exit;
    }

    // Require the Loader and then use that to load everything else
    require_once( 'config.php' );
    require_once( 'includes/debug.php' );
    require_once( 'includes/loader.php' );
    Loader::load_required();

    register_activation_hook(   __FILE__, [ '\Affiliate_Bridge\Setup', 'activate' ] );
    register_deactivation_hook( __FILE__, [ '\Affiliate_Bridge\Setup', 'deactivate' ] );
    register_uninstall_hook(    __FILE__, [ '\Affiliate_Bridge\Setup', 'uninstall' ] );

    // runs the plugin.
    Core::init();
}