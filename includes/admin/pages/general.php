<?php
namespace Affiliate_Bridge\Admin\Pages
{
    use \DateTime;
    use \Affiliate_Bridge\Core;
    use \Affiliate_Bridge\Debug;
    use \Affiliate_Bridge\Database\Database;
    use \Affiliate_Bridge\Sources\Ebay\Trading_API as eBay_API;

    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class General
    {
        const PAGE          = 'affiliate-bridge-backend';
        const SECTION       = 'affiliate-bridge-defaults';
        const OPT           = 'affiliate-bridge-settings';

        const IMAGE_SIZE    = 'ab_image_size';
        const DEFAULT_IMAGE = 'ab_def_image';
        const FRAMED        = 'ab_framed';
        const ITEMS         = 'ab_items';

        const DEFAULTS      = [
                                self::IMAGE_SIZE        => 'large',
                                self::DEFAULT_IMAGE     => '',
                                self::FRAMED            => 'N',
                                self::ITEMS             => 1
                              ];

        private $db = null;

        public function __construct()
        {
            $this->db = new Database();
        }

        public static function init()
        {
            return new General();
        }

        public function settings()
        {
            add_settings_section( 
                self::SECTION, 
                'Default Settings', 
                [ $this, 'display_section' ], 
                self::PAGE
            );

            add_settings_field( 
                self::IMAGE_SIZE, 
                'Default Image Size', 
                [ $this, 'display_image_size' ], 
                self::PAGE, 
                self::SECTION,
                []
            );

            add_settings_field( 
                self::DEFAULT_IMAGE, 
                'Default Image', 
                [ $this, 'display_default_image' ], 
                self::PAGE, 
                self::SECTION,
                []
            );

            add_settings_field( 
                self::FRAMED, 
                'Default Image Frame', 
                [ $this, 'display_framed' ], 
                self::PAGE, 
                self::SECTION,
                []
            );

            add_settings_field( 
                self::ITEMS, 
                'Default Number of Items', 
                [ $this, 'display_items' ], 
                self::PAGE, 
                self::SECTION,
                []
            );

            $args = [
                'type'              => 'array',
                'description'       => 'Default options for Affiliate Bridge',
                'sanitize_callback' => [ $this, 'process_input' ],
                'default'           => self::DEFAULTS
            ];

            register_setting( 
                self::OPT, 
                self::OPT, 
                $args
            );
        }

        public function load_settings()
        {
            return get_option( self::OPT );
        }

        public function display()
        {
            if ( isset( $_GET[ 'action' ] ) && isset( $_GET[ 'step' ] ) ) {
                $this->do_action( $_GET[ 'action'], $_GET[ 'step' ] );
            } else {
            ?>
            <div class="wrap">
                <h2 class="ab-admin-title">Affiliate Bridge :: Default Settings</h2>
                <?php settings_errors(); ?>
                <form method="post" action="options.php">
                    <?php 
                    settings_fields( self::OPT );
                    do_settings_sections( self::PAGE );
                    submit_button();
                    $this->display_shortcode_help();
                    ?>
                </form>
            <?php
            }
        }

        private function do_action( $action = null, $step = 0 )
        {
            if ( is_null( $action ) ) {
                add_settings_error( self::OPT, 'action-not-valid', 'Something went wrong. Please go back and try again.', 'notice-error is-dismissible' );
                return;
            }

            if ( $step === 0 ) {
                add_settings_error( self::OPT, 'action-not-valid', 'Something went wrong. Please go back and try again.', 'notice-error is-dismissible' );
                return;
            }

            switch( $action ) {
                case 'download-ebay-cats':
                    $this->download_ebay_cats( $step );
            }
        }

        public function display_section()
        {
            echo "These are the default settings for Affiliate Bridge. They can be overridden by the settings for specific sources.";
        }

        public function display_image_size()
        {
            $settings = $this->load_settings();

            ?>
            <select name="ab_image_size" id="image_size" class="regular-text" >
                <option value="small" <?php echo esc_attr( $settings[ 'ab_image_size' ] ) == "small" ? 'selected' : ''; ?>><?php echo __( 'Small (110px)', AB_PLUGIN_DOMAIN ); ?></option>
                <option value="medium" <?php echo esc_attr( $settings[ 'ab_image_size' ] ) == "medium" ? 'selected' : ''; ?>><?php echo __( 'Medium (200px)', AB_PLUGIN_DOMAIN ); ?></option>
                <option value="large" <?php echo esc_attr( $settings[ 'ab_image_size' ] ) == "large" || esc_attr( $settings[ 'ab_image_size' ] ) == "" ? 'selected' : ''; ?>><?php echo __( 'Large (400px)', AB_PLUGIN_DOMAIN ); ?></option>
            </select><br />
            <small><?php echo __( 'Default image size.', AB_PLUGIN_DOMAIN ); ?></small>
            <?php
        }

        public function display_default_image()
        {
            $settings = $this->load_settings();

            ?>
            <input
                    type="text"
                    name="ab_def_image"
                    class="original_mp3 regular_def_image regular-text"
                    value="<?php echo (esc_url($settings[ 'ab_def_image' ] )) ? esc_url($settings[ 'ab_def_image' ] ) : esc_attr( $defimage); ?>"
            />
            <input
                    type="button"
                    name="upload-btn-original"
                    id="upload-btn"
                    class="button-secondary upload-default-btn"
                    value="Upload Image"
            />
            <br />
            <img
                    id="def_def_image"
                    src="<?php echo esc_url($settings[ 'ab_def_image' ] ) ? esc_url($settings[ 'ab_def_image' ] ) : esc_attr( $defimage); ?>"
                    alt="<?php echo __( 'def', AB_PLUGIN_DOMAIN ) ?>"
                    title="<?php echo __( 'Default Image (if eBay item does not have an image)', AB_PLUGIN_DOMAIN ) ?>"
                    style="margin-top:10px; width: 120px;"
            />
            <?php
        }

        public function display_framed()
        {
            $settings = $this->load_settings();

            ?>
            <select name="ab_framed" id="framed" class="regular-text" >
                <option value="Y" <?php echo esc_attr( $settings[ 'ab_framed' ] ) == 'Y' || esc_attr( $settings[ 'ab_framed' ] ) == "" ? 'selected' : ''; ?>><?php echo __( 'Yes', AB_PLUGIN_DOMAIN ); ?></option>
                <option value="N" <?php echo esc_attr( $settings[ 'ab_framed' ] ) == 'N' ? 'selected' : ''; ?>><?php echo __( 'No', AB_PLUGIN_DOMAIN ); ?></option>
                <option value="C" <?php echo esc_attr( $settings[ 'ab_framed' ] ) == 'C' ? 'selected' : ''; ?>><?php echo __( 'Custom', AB_PLUGIN_DOMAIN ); ?></option>
            </select><br />
            <small><?php echo __( 'Image frame options: "Yes": Boxed, "Custom": Custom frame, "No": No frame', AB_PLUGIN_DOMAIN ); ?></small>
            <?php
        }

        public function display_items()
        {
            $settings = $this->load_settings();

            ?>
            <input name="ab_items" type="text" id="items" value="<?php echo esc_attr( $settings[ 'ab_items' ] ) ? esc_attr( $settings[ 'ab_items' ] ) : 1; ?>" class="regular-text" /><br />
            <small><?php echo __( 'If items > 1, Multiple Items will be shown in a table', AB_PLUGIN_DOMAIN ); ?></small>
            <?php
        }

        private function display_download_ebay_categories_button()
        {
            $wheres = [
                0 => [
                    'column'    => 'id',
                    'operator'  => '=',
                    'condition' => '1'
                ]
            ];

            $results = $this->db->get( 'ebay_cats', $wheres );

            $version = 'N/A';
            $date = 'Never';

            if ( is_array( $results ) && ( isset( $results[ 0 ]->version ) && isset( $results[ 0 ]->category_name ) ) ) {
                $version = $results[ 0 ]->version;
                $date = $results[ 0 ]->category_name;
            }

            ?>
                    <hr>
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="download-ebay-cats"><?php echo __( 'Download eBay Category List', AB_PLUGIN_DOMAIN ); ?></label>
                                </th>
                                <td>
                                    <a href="admin.php?page=affiliate-bridge-backend&action=download-ebay-cats&step=1" class="button button-primary"  id="download-ebay-cats-btn"><?php echo __( 'Download', AB_PLUGIN_DOMAIN ); ?></a><br>
                                    <?php if ( $date === 'Never' ) {
                                        ?>
                                    <small><?php echo 'Last Downloaded: <span class="ab-red">Never</span> - Current Version: <span class="ab-red">N/A</span>'; ?><br><?php echo __( 'You must download the eBay Category List before you can use eBay as a Source in Affiliate Bridge.', AB_PLUGIN_DOMAIN ); ?></small>
                                        <?php
                                    } else {
                                        ?>
                                    <small><?php echo 'Last Downloaded: <b>' . $date . '</b> - Current Version: <b>' . $version . '</b>'; ?><br><?php echo __( 'Since you have already downloaded the category list, clicking this button will check to see if a newer version of the category list exists on eBay. If so, it will download it. Otherwise, it will not.', AB_PLUGIN_DOMAIN ); ?></small>
                                        <?php
                                    } ?>
                                </td>
                            </tr>
                        </tbody>
                    </table>
            <?php
        }

        private function display_shortcode_help()
        {
            echo '<hr>TODO: Shortcode Help goes here!<br>';
        }

        public function set_errors( $errors )
        {
            if ( count( $errors ) ) {
                foreach( $errors as $code => $msg ) {
                    add_settings_error( self::OPT, $code, $msg, 'notice-error is-dismissible' );
                }
            }
        }

        public function set_warnings( $warnings )
        {
            if ( count( $warnings ) ) {
                foreach( $warnings as $code => $msg ) {
                    add_settings_error( self::OPT, $code, $msg, 'notice-warning is-dismissible' );
                }
            }
        }

        public function set_success()
        {
            $code = 'saved';
            $msg  = __( 'Default Options Saved Successfully', AB_PLUGIN_DOMAIN );

            add_settings_error( self::OPT, $code, $msg, 'notice-success is-dismissible' );
        }

        public function process_input()
        {
            if( isset( $_POST[ 'submit' ] ) ) {
                $input = [
                    self::IMAGE_SIZE        => $_POST[ self::IMAGE_SIZE ],
                    self::DEFAULT_IMAGE     => $_POST[ self::DEFAULT_IMAGE ],
                    self::FRAMED            => $_POST[ self::FRAMED ],
                    self::ITEMS             => $_POST[ self::ITEMS ]
                ];
            } else {
                return;
            }

            $data = $this->sanitize_input( $input );
            $check = $this->validate_input( $data );

            $current = get_option( self::OPT );

            if( ! count( $check[ 0 ] ) ) {
                $this->set_success();
                return $data;
            } else {
                $this->set_errors( $check[ 0 ] );
                return $current;
            }
        }

        private function sanitize_input( $input )
        {
            $clean = array();

            $clean[ self::IMAGE_SIZE ] = ( isset( $input[ self::IMAGE_SIZE ] ) ) ? sanitize_text_field( $input[ self::IMAGE_SIZE ] ) : '';
            $clean[ self::DEFAULT_IMAGE ] = ( isset( $input[ self::DEFAULT_IMAGE ] ) ) ? sanitize_text_field( $input[ self::DEFAULT_IMAGE ] ) : '';
            $clean[ self::FRAMED ] = ( isset( $input[ self::FRAMED ] ) ) ? sanitize_text_field( $input[ self::FRAMED ] ) : '';
            $clean[ self::ITEMS ] = ( isset( $input[ self::ITEMS ] ) ) ? sanitize_text_field( $input[ self::ITEMS ] ) : '';

            return $clean;
        }

        private function validate_input( $input )
        {
            $errors = $warnings = [];

            $image_sizes = [ 'small', 'medium', 'large' ];
            if( ! isset( $input[ self::IMAGE_SIZE ] ) || ! in_array( $input[ self::IMAGE_SIZE ], $image_sizes ) ) {
                $errors[ self::IMAGE_SIZE ] = __( 'Invalid Default Image Size Selected. Please select one of the valid options.', AB_PLUGIN_DOMAIN );
            }

            if( ! isset( $input[ self::DEFAULT_IMAGE ] ) || isset( $input[ self::DEFAULT_IMAGE ] ) && ! $this->is_valid_url( $input[ self::DEFAULT_IMAGE ] ) ) {
                $errors[ self::DEFAULT_IMAGE ] = __( 'Invalid Default Image URL. Please use a valid URL.', AB_PLUGIN_DOMAIN );
            }

            $frames = [ 'N', 'Y', 'C' ];
            if( ! isset( $input[ self::FRAMED ] ) || ! in_array( $input[ self::FRAMED ], $frames ) ) {
                $errors[ self::FRAMED ] = __( 'Invalid Default Frame Selected. Please select on of the valid options.', AB_PLUGIN_DOMAIN );
            }

            if( ! isset( $input[ self::ITEMS ] ) || isset( $input[ self::ITEMS ] ) && ! is_numeric( $input[ SELF::ITEMS ] ) ) {
                $errors[ self::ITEMS ] = __( 'Invalid Default Number of Items. Please use valid number.', AB_PLUGIN_DOMAIN );
            }

            return [ $errors, $warnings ];
        }

        private function is_valid_url( $file )
        {
            $fp = @fopen( $file, 'r' );

            if( $fp !== false ) {
                fclose( $fp );
            }

            return $fp;
        }

        private function download_ebay_cats( $step )
        {
            $api        = new eBay_API();
            $session_id = $api->get_session_id();
            $token      = $api->get_token();

            switch( $step ) {
                case 1:
                    if ( $token === '' ) {
                        $this->download_ebay_cats_get_auth( $api, $session_id );
                    } else {
                        $this->download_ebay_cats_step_1( $api );
                    }
                    break;
                case 2:
                    $submit = $_POST[ 'submit' ];
                    if ( $submit === 'Back to Settings' ) {
                        wp_safe_redirect( 'admin.php?page=affiliate-bridge-backend' );
                        exit();
                    } elseif ( $submit === 'Download Category List') {
                        $this->download_ebay_cats_step_2( $api );
                    }
                    break;
            }
        }       

        private function download_ebay_cats_get_auth( &$api, $session_id )
        {
            $runame     = Trading_API::RUNAME;
            ?>
            <div class="wrap">
                <h2 class="ab-admin-title">Affiliate Bridge :: Download eBay Category List :: Authorization</h2>
                <?php settings_errors(); ?>
                <form method="post" action="admin.php?page=affiliate-bridge-backend&action=download-ebay-cats&step=1">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="ebay-authorize"><?php echo __( 'Authorize Ebay Account', AB_PLUGIN_DOMAIN ); ?></label>
                                </th>
                                <td>
                                    <a href="https://signin.ebay.com/ws/eBayISAPI.dll?SignIn&RUName=<?php echo $runame; ?>&SessID=<?php echo rawurlencode( $session_id ); ?>" class="button button-primary" target="ebay-auth-popup" title="This link will open a new popup window." id="ebay-auth-btn"><?php echo __( 'Authorize', AB_PLUGIN_DOMAIN ); ?></a><br>
                                    <small><?php echo __( 'Any time you edit eBay source settings, you will need to authorize Affiliate Bridge to use your account. This will open a popup window and redirect you to login on eBay. This is only used to download the current category list from eBay and is not used for any other purpose.', AB_PLUGIN_DOMAIN ); ?></small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php submit_button( 'Next', 'primary', 'submit', 'true', [ 'disabled' => 'disabled' ] ); ?>
                </form>
            <?php
        }

        private function download_ebay_cats_step_1( &$api )
        {
            $local_version = $this->get_local_category_list_version();
            $ebay_version = $api->make_call( 'get_ebay_category_list_version' );
            $ebay_version = $ebay_version->get_payload();

            if ( $local_version != $ebay_version ) {
                $download       = true;
                $title          = 'Step 1';
                $url            = 'admin.php?page=affiliate-bridge-backend&action=download-ebay-cats&step=2';
            } else {
                $download       = false;
                $title          = 'Up-To-Date';
                $url            = 'admin.php?page=affiliate-bridge-backend';
            }

            ?>
            <div class="wrap">
                <h2 class="ab-admin-title">Affiliate Bridge :: Download eBay Category List :: <?php echo $title; ?></h2>
                <?php settings_errors(); ?>
                <form method="post" action="<?php echo $url; ?>">
                    <input type="hidden" name="version" value="<?php echo $ebay_version; ?>">
                <?php 
                    if ( $download ) {
                        echo __( 'Just click the button below to download the category list directly from eBay.', AB_PLUGIN_DOMAIN );
                        submit_button( 'Download Category List' );
                    } else {
                        echo __( 'It loooks like you do not need to download the eBay Category List again, since what you have saved is already up-to-date', AB_PLUGIN_DOMAIN );
                        submit_button( 'Back to Settings' );
                    }
                ?>
                </form>
            <?php
        }

        private function download_ebay_cats_step_2( &$api )
        {
            $version = $_POST[ 'version' ];
            $categories = $api->make_call( 'get_ebay_category_list' );
            $categories = $categories->get_payload();

            $this->save_categories( $version, $categories );

            ?>
            <div class="wrap">
                <h2 class="ab-admin-title">Affiliate Bridge :: Download eBay Category List :: Complete</h2>
                <?php settings_errors(); ?>
                <form method="post" action="admin.php?page=affiliate-bridge-backend">
                <?php 
                    echo __( 'The category list has been updated and saved to your website\'s database. You will not need to download it again until eBay releases a new version.', AB_PLUGIN_DOMAIN );
                    submit_button( 'Back to Settings' );
                ?>
                </form>
            <?php
        }

        private function get_local_category_list_version()
        {
            $wheres = [
                0 => [
                    'column'    => 'id',
                    'operator'  => '=',
                    'condition' => '1'
                ]
            ];

            $results = $this->db->get( 'ebay_cats', $wheres );

            if ( is_array( $results ) && isset( $results[ 0 ]->version ) ) {
                return $results[ 0 ]->version;
            } else {
                return false;
            }
        }

        private function save_categories( $version, $categories )
        {
            $now = new DateTime();
            
            $data = [
                'category_id'       => '',
                'category_level'    => '',
                'category_name'     => $now->format( 'Y-m-d H:i:s' ),
                'parent_id'         => '',
                'version'           => $version,
            ];
            
            $this->db->insert( 'ebay_cats', $data );

            $data = [];
            foreach( $categories as $cat ) {
                $data = [];

                $data = [
                    'category_id'       => $cat[ 'category_id' ],
                    'category_level'    => $cat[ 'category_level' ],
                    'category_name'     => $cat[ 'category_name' ],
                    'parent_id'         => $cat[ 'parent_id' ],
                    'version'           => $version
                ];

                $this->db->insert( 'ebay_cats', $data );
            }
        }
    }
}