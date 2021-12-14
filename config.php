<?php
// Plugin Information
define( 'AB_PLUGIN_NAME',                   'Affiliate Bridge' );
define( 'AB_PLUGIN_DESCRIPTION',            'Easily add product images from affiliate programs using shortcodes.' );
define( 'AB_PLUGIN_VERSION',                '1.1.0' );
define( 'AB_PLUGIN_AUTHOR',                 'David Lidor' );
define( 'AB_PLUGIN_AUTHOR_URI',             'https://www.bicycle-riding.com' );
define( 'AB_PLUGIN_DOMAIN',                 'affiliate-bridge' );

define( 'AB_PLUGIN_FILE',                   plugin_basename( 'affiliate-bridge.php' ) );

// Paths
define( 'AB_PLUGIN_DIR',                    plugin_dir_path( __FILE__ ) );
define( 'AB_INCLUDE_DIR',                   AB_PLUGIN_DIR . 'includes/' );

// URLs
define( 'AB_PLUGIN_URL',                    plugin_dir_url( __FILE__ ) );
define( 'AB_IMAGE_URL',                     AB_PLUGIN_URL . 'assets/images/' );
define( 'AB_STYLE_URL',                     AB_PLUGIN_URL . 'assets/css/' );
define( 'AB_SCRIPT_URL',                    AB_PLUGIN_URL . 'assets/js/' );

// Options
define( 'AB_DEBUG_ENABLED',                 false );