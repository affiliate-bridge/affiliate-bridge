<?php
namespace Affiliate_Bridge\Admin\Pages
{
    use \DateTime;
    use \Affiliate_Bridge\Core;
    use \Affiliate_Bridge\Debug;
    use \Affiliate_Bridge\Database\Database;
    use \Affiliate_Bridge\Sources\Sources;
    use \Affiliate_Bridge\Sources\Ebay\Trading_API as eBay_API;

    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    //TODO: Change Shortcode Generator to be part of the source.

    class Shortcode_Generator
    {
        const PAGE          = 'affiliate-bridge-shortcode';
        const SECTION       = 'affiliate-bridge-defaults';
        const OPT           = 'affiliate-bridge-settings';

        const SOURCE        = 'shortcode-source';
        const ITEMS         = 'shortcode-items';
        const SIZE          = 'shortcode-size';
        const KEYWORDS      = 'shortcode-keywords';
        const FRAMED        = 'shortcode-framed';
        const CATEGORIES    = 'shortcode-categories';
        const CONDITION     = 'shortcode-condition';
        const DEFIMAGE      = 'shortcode-defimage';

        const DEFAULTS      = [
                                self::SOURCE            => 'ebay',
                                self::ITEMS             => 10,
                                self::SIZE              => 'small',
                                self::KEYWORDS          => '',
                                self::FRAMED            => 'N',
                                self::CATEGORIES        => '',
                                self::CONDITION         => '',
                                self::DEFIMAGE          => ''                                
                              ];

        private $db = null;
        private $sources = null;

        public function __construct()
        {
            $this->db = new Database();
            $this->sources = new Sources();
        }

        public static function init()
        {
            return new Shortcode_Generator();
        }

        public function load_settings()
        {
            return get_option( self::OPT );
        }

        public function display()
        {
            if ( isset( $_POST[ 'submit' ] ) ) {
                $input = $this->do_action();
            } else {
                $input = [];
            }
            ?>
            <div class="wrap">
                <h2 class="ab-admin-title">Affiliate Bridge :: Shortcode Generator</h2>
                <?php settings_errors(); ?>
                <form method="post">
                    <?php 
                    $this->display_shortcode_form( $input );
                    submit_button( 'Generate Shortcode' );
                    echo $this->generate_shortcode( $input );
                    ?>
                </form>
            <?php
        }

        private function do_action()
        {   
            return $this->process_input();
        }

        private function display_shortcode_form( $input = [] )
        {
            $form = '<table class="form-table" role="presentation">';
            $form .= '<tr>';
            $form .= $this->display_source_field( $input );
            $form .= '</tr><tr>';
            $form .= $this->display_items_field( $input );
            $form .= '</tr><tr>';
            $form .= $this->display_size_field( $input );
            $form .= '</tr><tr>';
            $form .= $this->display_keywords_field( $input );
            $form .= '</tr><tr>';
            $form .= $this->display_framed_field( $input );
            $form .= '</tr><tr>';
            $form .= $this->display_categories_field( $input );
            $form .= '</tr><tr>';
            $form .= $this->display_condition_field( $input );
            $form .= '</tr><tr>';
            $form .= $this->display_defimage_field( $input );
            $form .= '</tr></table>';

            echo $form;
        }

        private function display_source_field( $input = [] )
        {
            $field = '<th scope="row"><label class="shortcode-label">Source</label></th>';
            $field .= '<td><select id="' . self::SOURCE . '" name="' . self::SOURCE . '">';
            $field .= $this->source_field_options( $input );
            $field .= '</select><br><small><strong class="required">REQUIRED!</strong> Select the source of the items for the table.</small></td>';

            return $field;
        }

        private function display_items_field( $input = [] )
        {
            $value = ( $input !== [] ) ? $input[ self::ITEMS ] : 0;
            $field = '<th scope="row"><label class="shortcode-label">Number of Items</label></th>';
            $field .= '<td><input type="number" id="' . self::ITEMS . '" name="' . self::ITEMS . '" min="0" max="100" value="' . $value . '">';
            $field .= '<br><small>Number of items to display in the table. Set to 0 to use default set on the general settings page.</small></td>';

            return $field;
        }

        private function display_size_field( $input = [] )
        {
            $field = '<th scope="row"><label class="shortcode-label">Image Size</label></th>';
            $field .= '<td><select id="' . self::SIZE . '" name="' . self::SIZE . '">';
            $field .= '<option value="small"' . ( ( $input !== [] && $input[ self::SIZE ] == 'small') ? ' selected' : '' ) . '>Small</option>';
            $field .= '<option value="medium"' . ( ( $input !== [] && $input[ self::SIZE ] == 'medium') ? ' selected' : '' ) . '>Medium</option>';
            $field .= '<option value="large"' . ( ( $input !== [] && $input[ self::SIZE ] == 'large') ? ' selected' : '' ) . '>Large</option>';
            $field .= '</select><br><small>The size of the images for items in the table.</small></td>';

            return $field;
        }

        private function display_keywords_field( $input = [] )
        {
            $value = ( $input !== [] ) ? $input[ self::KEYWORDS ] : '';
            $field = '<th scope="row"><label class="shortcode-label">Keywords</label></th>';
            $field .= '<td><input type="text" id="' . self::KEYWORDS . '" name="' . self::KEYWORDS . '" value="' . $value . '">';
            $field .= '<br><small>The keywords to search for.</small></td>';

            return $field;
        }

        private function display_framed_field( $input = [] )
        {
            $field = '<th scope="row"><label class="shortcode-label">Image Frame</label></th>';
            $field .= '<td><select id="' . self::FRAMED . '" name="' . self::FRAMED . '">';
            $field .= '<option value="Y"' . ( ( $input !== [] && $input[ self::FRAMED ] == 'Y') ? ' selected' : '' ) . '>Yes</option>';
            $field .= '<option value="N"' . ( ( $input !== [] && $input[ self::FRAMED ] == 'N') ? ' selected' : '' ) . '>No</option>';
            $field .= '<option value="C"' . ( ( $input !== [] && $input[ self::FRAMED ] == 'C') ? ' selected' : '' ) . '>Custom</option>';
            $field .= '</select><br><small>Image frame options: "Yes": Boxed, "Custom": Custom frame, "No": No frame</small></td>';

            return $field;
        }

        private function display_categories_field( $input = [] )
        {
            //TODO: Make this not tied to ebay source
            $ebay = $this->sources->get_source( 'ebay' );
            $options = $ebay->get_categories_as_options( $input );

            $field = '<th scope="row"><label class="shortcode-label">Categories</label></th>';
            $field .= '<td><select id="' . self::CATEGORIES . '" name="' . self::CATEGORIES . '[]" class="regular-text" multiple size="15">';
            $field .= $options;
            $field .= '</select><br><small>Select one or more category for items displayed in search results.</small></td>';

            return $field;
        }

        private function display_condition_field( $input = [] )
        {
            $field = '<th scope="row"><label class="shortcode-label">Condition</label></th>';
            $field .= '<td><select id="' . self::CONDITION . '" name="' . self::CONDITION . '">';
            $field .= '<option value="All">All</option>';
            $field .= '<option value="New">New</option>';
            $field .= '<option value="Used">Used</option>';
            $field .= '</select><br><small>Select the condition for items displayed in search results.</small></td>';

            return $field;
        }

        private function display_defimage_field( $input = [] )
        {
            $value = ( $input !== [] ) ? $input[ self::DEFIMAGE ] : '';
            $field = '<th scope="row"><label class="shortcode-label">Default Image</label></th>';
            $field .= '<td><input type="text" name="' . self::DEFIMAGE . '" id="' . self::DEFIMAGE . '" class="original_mp3 regular_def_image regular-text" value="' . $value . '">';
            $field .= '<input type="button" name="upload-btn-original" id="upload-btn" class="button-secondary upload-default-btn" value="Upload Image">';
            $field .= '<br><small>The URL of the default image used for items that don\'t have an image. Leave blank to use the default set on the general settings page.</small></td>';

            return $field;
        }

        private function generate_shortcode( $input = [] )
        {
            $shortcode = '[affiliate-bridge';

            if ( $input !== [] ) {
                $shortcode .= ' source="' . $input[ self::SOURCE ] . '"';
            }

            if ( isset( $input[ self::ITEMS ] ) ) {
                $shortcode .= ' items="' . $input[ self::ITEMS ] . '"';
            }

            if ( isset( $input[ self::SIZE ] ) ) {
                $shortcode .= ' size="' . $input[ self::SIZE ] . '"';
            }

            if ( isset( $input[ self::KEYWORDS ] ) ) {
                $shortcode .= ' keywords="' . $input[ self::KEYWORDS ] . '"';
            }

            if ( isset( $input[ self::FRAMED ] ) ) {
                $shortcode .= ' framed="' . $input[ self::FRAMED ] . '"';
            }

            if ( isset( $input[ self::CATEGORIES ] ) ) {
                $shortcode .= ' categories="';

                foreach( $input[ self::CATEGORIES ] as $cat ) {
                    $shortcode .= $cat . ',';
                }

                $shortcode = substr( $shortcode, 0, -1 );
                $shortcode .= '"';
            }

            if ( isset( $input[ self::CONDITION ] ) ) {
                $shortcode .= ' condition="' . $input[ self::CONDITION ] . '"';
            }

            if ( isset( $input[ self::DEFIMAGE ] ) ) {
                $shortcode .= ' defimage="' . $input[ self::DEFIMAGE ] . '"';
            }

            $shortcode .= ']';

            return $shortcode;
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

        public function set_success( $success )
        {
            if ( count( $success ) ) {
                foreach( $success as $code => $msg ) {
                    add_settings_error( self::OPT, $code, $msg, 'notice-success is-dismissible' );
                }
            }
        }

        public function process_input()
        {
            if( isset( $_POST[ 'submit' ] ) ) {
                $input = filter_input_array( INPUT_POST, FILTER_SANITIZE_STRING );
            } else {
                return;
            }

            $clean = $this->sanitize_input( $input );
            $check = $this->validate_input( $clean );
            
            if( ! count( $check[ 0 ] ) ) {
                $this->set_success( [ 'shortcode-generated' => 'Shortcode Successfully Generated: </strong><br>' . $this->generate_shortcode( $clean ) ] );
                $clean[ 'status' ] = 'success';
            } else {
                $this->set_errors( $check[ 0 ] );
                $clean[ 'status' ] = 'fail';
            }

            return $clean;
        }

        private function sanitize_input( $input )
        {
            $clean = array();
            $clean[ 'status' ] = 'fail';

            $clean[ self::SOURCE ]      = ( isset( $input[ self::SOURCE ] ) && ! empty( $input[ self::SOURCE ] ) ) ? sanitize_text_field( $input[ self::SOURCE ] ) : null;
            $clean[ self::ITEMS ]       = ( isset( $input[ self::ITEMS ] ) && ! empty( $input[ self::ITEMS ] ) ) ? sanitize_text_field( $input[ self::ITEMS ] ) : null;
            $clean[ self::SIZE ]        = ( isset( $input[ self::SIZE ] ) && ! empty( $input[ self::SIZE ] ) ) ? sanitize_text_field( $input[ self::SIZE ] ) : null;
            $clean[ self::KEYWORDS ]    = ( isset( $input[ self::KEYWORDS ] ) && ! empty( $input[ self::KEYWORDS ] ) ) ? sanitize_text_field( $input[ self::KEYWORDS ] ) : null;
            $clean[ self::FRAMED ]      = ( isset( $input[ self::FRAMED ] ) && ! empty( $input[ self::FRAMED ] ) ) ? sanitize_text_field( $input[ self::FRAMED ] ) : null;
            
            if ( is_array( $input[ self::CATEGORIES ] ) && count( $input[ self::CATEGORIES ] ) ) {
                foreach( $input[ self::CATEGORIES ] as $cat ) {
                    $clean[ self::CATEGORIES ][] = sanitize_text_field( $cat );
                }
            }

            $clean[ self::CONDITION ]   = ( isset( $input[ self::CONDITION ] ) && ! empty( $input[ self::CONDITION ] ) ) ? sanitize_text_field( $input[ self::CONDITION ] ) : null;
            $clean[ self::DEFIMAGE ]    = ( isset( $input[ self::DEFIMAGE ] ) && ! empty( $input[ self::DEFIMAGE ] )  ) ? esc_url( $input[ self::DEFIMAGE ] ) : null;

            return $clean;
        }

        private function validate_input( $input )
        {
            $errors = $warnings = [];

            // Source
            if ( isset( $input[ self::SOURCE ] ) ) {
                if ( ! is_string( $input[ self::SOURCE ] ) ) {
                    $errors[ self::SOURCE ] = __( 'Invalid Source. Please select a valid source.', AB_PLUGIN_DOMAIN );
                }

                if ( $input[ self::SOURCE ] == 'none' ) {
                    $errors[ self::SOURCE ] = __( 'Invalid Source. Please select a valid source.', AB_PLUGIN_DOMAIN );
                }
            } else {
                $errors[ self::SOURCE ] = __( 'No Source Selected. Please select a source.', AB_PLUGIN_DOMAIN );
            }

            // Items
            if ( isset( $input[ self::ITEMS ] ) ) {
                if ( ! is_numeric( $input[ self::ITEMS ] ) ) {
                    $errors[ self::ITEMS ] = __( 'Invalid Number of Items. Please use valid number (0 - 100).', AB_PLUGIN_DOMAIN );
                }
            }

            // Size
            $image_sizes = [ 'small', 'medium', 'large' ];

            if( isset( $input[ self::SIZE ] ) ) {
                if ( ! in_array( $input[ self::SIZE ], $image_sizes ) ) {
                    $errors[ self::SIZE ] = __( 'Invalid Image Size Selected. Please select one of the valid options.', AB_PLUGIN_DOMAIN );
                }
            }

            // Framed
            $frames = [ 'N', 'Y', 'C' ];

            if( isset( $input[ self::FRAMED ] ) ) {
                if ( ! in_array( $input[ self::FRAMED ], $frames ) ) {
                    $errors[ self::FRAMED ] = __( 'Invalid Frame Selected. Please select one of the valid options.', AB_PLUGIN_DOMAIN );
                }
            }
            // Keywords
            if ( isset( $input[ self::KEYWORDS ] ) ) {
                if ( ! is_string( $input[ self::KEYWORDS ] ) ) {
                    $errors[ self::KEYWORDS ] = __( 'Invalid Keywords. Please enter valid keywords.', AB_PLUGIN_DOMAIN );
                }
            }

            // Categories
            if ( isset( $input[ self::CATGEORIES ] ) ) {
                if ( count( $input[ self::CATGEORIES ] ) > 3 ) {
                    $errors[ self::CATGEORIES ] = __( 'Too Many Categories. Please choose a maximum of 3.', AB_PLUGIN_DOMAIN );
                }
            }
            
            // Conditions
            $conditions = [ 'All', 'New', 'Used' ];

            if( isset( $input[ self::CONDITION ] ) ) {
                if ( ! in_array( $input[ self::CONDITION ], $conditions ) ) {
                    $errors[ self::CONDITION ] = __( 'Invalid Condition Selected. Please select one of the valid options.', AB_PLUGIN_DOMAIN );
                }
            }

            // Default Image
            if( isset( $input[ self::DEFIMAGE ] ) ) {
                if ( isset( $input[ self::DEFIMAGE ] ) && ! $this->is_valid_url( $input[ self::DEFIMAGE ] ) ) {
                    $errors[ self::DEFIMAGE ] = __( 'Invalid Default Image URL. Please use a valid URL.', AB_PLUGIN_DOMAIN );
                }
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

        private function source_field_options( $input = [] )
        {
            $options = '<option value="none">Please Select a Source</option>';
            $sources = $this->sources->get_all_sources();

            foreach( $sources as $key => $source ) {
                $selected = ( $input !== [] && $input[ self::SOURCE ] == $key ) ? ' selected' : '';
                $options .= '<option value="' . $key . '"' . $selected . '>' . $source->get_name() . '</option>';
            }

            return $options;
        }
    }
}