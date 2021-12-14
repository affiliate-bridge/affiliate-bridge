<?php
namespace Affiliate_Bridge\Sources\Ebay
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    use \DateTime;
    use \Affiliate_Bridge\Core;
    use \Affiliate_Bridge\Debug;
    use \Affiliate_Bridge\Database\Database;
    use \Affiliate_Bridge\Sources\Source as BaseSource;
    use \Affiliate_Bridge\Sources\Ebay\API;
    use \Affiliate_Bridge\Sources\Ebay\Trading_API;
    use \Affiliate_Bridge\Sources\Ebay\Finding_API;
    use \Affiliate_Bridge\Sources\Ebay\Shopping_API;

    class Source extends BaseSource
    {
        protected $name             = 'eBay';
        protected $description      = 'Show items from Ebay on your website.';
        protected $logo             = AB_IMAGE_URL . 'ebay.png';

        protected $source           = 'ebay';

        protected $api              = [];
        protected $db               = null;

        const OPT                   = 'ebay-settings';
        const APP_ID                = 'DavidLid-1d4e-4f32-83e5-76489b322689';

        public function __construct()
        {
            $this->api = [
                'trading'               => new Trading_API(),
                'finding'               => new Finding_API(),
                'shopping'              => new Shopping_API()
            ];
            $this->db                   = new Database();
        }

        public function form_new()
        {
            $step = ( isset( $_GET[ 'step' ] ) ) ? $_GET[ 'step' ] : '1';

            if( $step < 1 || $step > 1 ) {
                return;
            }

            switch( $step ) {
                case 1:
                    $this->form_new_step_1();
                    break;
            }
        }

        public function form_edit()
        {
            $step = ( isset( $_GET[ 'step' ] ) ) ? $_GET[ 'step' ] : '1';

            if( $step < 1 || $step > 1 ) {
                return;
            }

            switch( $step ) {
                case 1:
                    $this->form_edit_step_1();
                    break;
            }
        }

        public function form_cats()
        {
            $step = ( isset( $_GET[ 'step' ] ) ) ? $_GET[ 'step' ] : '1';

            $token      = $this->api[ 'trading' ]->get_token();

            if( $step < 1 || $step > 1 ) {
                return;
            }

            switch( $step ) {
                case 1:
                    if ( $token === '' ) {
                        $this->form_cats_get_auth();
                    } else {
                        $this->form_cats_step_1();
                    }
                    break;
            }
        }

        public function form_delete()
        {
            $step = ( isset( $_GET[ 'step' ] ) ) ? $_GET[ 'step' ] : '1';

            if( $step < 1 || $step > 1 ) {
                return;
            }

            switch( $step ) {
                case 1:
                    $this->form_delete_step_1();
                    break;
            }
        }

        private function form_new_step_1()
        {
            $categories = $this->categories_as_options( $this->api[ 'trading' ]->get_categories() );
            $conditions = $this->conditions_as_options();
            ?>
                <form method="post" action="admin.php?page=affiliate-bridge-sources">
                    <input type="hidden" name="action" value="new">
                    <input type="hidden" name="step" value="2">
                    <input type="hidden" name="source" value="ebay">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="app_id"><?php echo __( 'App ID', AB_PLUGIN_DOMAIN ); ?></label>
                                </th>
                                <td>
                                    <input name="app_id" type="text" id="app_id" class="regular-text"><br>
                                    <small><?php echo __( 'Find your eBay App ID ', AB_PLUGIN_DOMAIN ); ?><a href="https://developer.ebay.com/my/keys" target="_blank"><?php echo __( 'here', AB_PLUGIN_DOMAIN ) ?></a></small>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="categories"><?php echo __( 'Default Categories', AB_PLUGIN_DOMAIN ); ?></label></th>
                                <td>
                                    <select name="categories[]" id="categories" class="regular-text" multiple size="15">
                                        <?php echo $categories; ?>
                                    </select><br>
                                    <small><?php echo __( 'Select one or more default category for items displayed in search results. No category is selected by default.', AB_PLUGIN_DOMAIN ); ?></small>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="condition"><?php echo __('Default Condition', AB_PLUGIN_DOMAIN); ?></label></th>
                                <td>
                                    <select name="condition[]" id="condition" class="regular-text" multiple size="7">
                                        <?php echo $conditions; ?>
                                    </select><br>
                                    <small><?php echo __('Select one or more default conditions for items displayed in search results. All conditions are selected by default.', AB_PLUGIN_DOMAIN); ?></small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php submit_button( 'Finish' ); ?>
                </form>
            <?php
        }

        private function form_edit_step_1()
        {
            $categories = $this->categories_as_options( $this->api[ 'trading' ]->get_categories(), $this->settings[ 'default_categories' ] );
            $conditions = $this->conditions_as_options( $this->settings[ 'default_condition' ] );
            ?>
                <form method="post" action="admin.php?page=affiliate-bridge-sources">
                    <input type="hidden" name="action" value="edit">
                    <input type="hidden" name="step" value="2">
                    <input type="hidden" name="source-id" value="<?php echo $this->id; ?>">
                    <input type="hidden" name="source" value="ebay">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="app_id"><?php echo __( 'App ID', AB_PLUGIN_DOMAIN ); ?></label>
                                </th>
                                <td>
                                    <input name="app_id" type="text" id="app_id" class="regular-text" value="<?php echo $this->settings[ 'app_id' ]; ?>"><br>
                                    <small><?php echo __( 'Find your eBay App ID ', AB_PLUGIN_DOMAIN ); ?><a href="https://developer.ebay.com/my/keys" target="_blank"><?php echo __( 'here', AB_PLUGIN_DOMAIN ) ?></a></small>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="categories"><?php echo __( 'Default Categories', AB_PLUGIN_DOMAIN ); ?></label></th>
                                <td>
                                    <select name="categories[]" id="categories" class="regular-text" multiple size="15">
                                        <?php echo $categories; ?>
                                    </select><br>
                                    <small><?php echo __( 'Select one or more default category for items displayed in search results. No category is selected by default.', AB_PLUGIN_DOMAIN ); ?></small>
                                </td>
                            </tr>
                            <tr>
                                <th scope="row"><label for="condition"><?php echo __('Default Condition', AB_PLUGIN_DOMAIN); ?></label></th>
                                <td>
                                    <select name="condition[]" id="condition" class="regular-text" multiple size="7">
                                        <?php echo $conditions; ?>
                                    </select><br>
                                    <small><?php echo __('Select one or more default conditions for items displayed in search results. All conditions are selected by default.', AB_PLUGIN_DOMAIN); ?></small>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                    <?php submit_button( 'Finish' ); ?>
                </form>
            <?php
        }

        private function form_cats_get_auth()
        {
            $runame     = Trading_API::RUNAME;
            $session_id = $this->api[ 'trading' ]->get_session_id();
            ?>
                <form method="post" action="admin.php?page=affiliate-bridge-sources">
                    <input type="hidden" name="action" value="cats">
                    <input type="hidden" name="step" value="1">
                    <input type="hidden" name="source-id" value="<?php echo $this->id; ?>">
                    <input type="hidden" name="source" value="ebay">
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

        private function form_cats_step_1()
        {
            $local_version = $this->get_local_category_list_version();
            $ebay_version = $this->api[ 'trading' ]->make_call( 'get_ebay_category_list_version' );
            $ebay_version = $ebay_version->get_payload();

            if ( $local_version != $ebay_version ) {
                $download       = true;
                $title          = 'Step 1';
                $action         = 'cats';
                $step           = '2';
            } else {
                $download       = false;
                $title          = 'Up-To-Date';
                $action         = '';
                $step           = '';
            }

            ?>
                <form method="post" action="admin.php?page=affiliate-bridge-sources">
                    <input type="hidden" name="version" value="<?php echo $ebay_version; ?>">
                    <input type="hidden" name="action" value="<?php echo $action; ?>">
                    <input type="hidden" name="step" value="<?php echo $step; ?>">
                    <input type="hidden" name="source-id" value="<?php echo $this->id; ?>">
                    <input type="hidden" name="source" value="ebay">
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

        private function form_delete_step_1()
        {
            ?>
                <form method="post" action="admin.php?page=affiliate-bridge-sources">
                    <input type="hidden" name="action" value="delete">
                    <input type="hidden" name="step" value="2">
                    <input type="hidden" name="source-id" value="<?php echo $this->id; ?>">
                    <input type="hidden" name="source" value="ebay">
                    <table class="form-table" role="presentation">
                        <tbody>
                            <tr>
                                <th scope="row">
                                    <label for="confirm"><?php echo __( 'Are you sure you want to delete eBay as a source?', AB_PLUGIN_DOMAIN ); ?></label>
                                </th>
                            </tr>
                        </tbody>
                    </table>
                    <?php submit_button( 'Yes', 'primary ab-yesno', 'submit', false ); ?> &nbsp; &nbsp; <?php submit_button( 'No', 'ab-yesno', 'submit', false ); ?>
                </form>
            <?php
        }

        private function get_session_id()
        {
            $args = [
                'call-type'         => 'trading',
                'call'              => 'GetSessionID',
            ];

            $this->api->call( $args );

            return;
        }

        private function get_categories()
        {
            $args = [
                'call-type'         => 'trading',
                'call'              => 'GetCategories',
            ];

            $this->api->call( $args );

            return;
        }

        public function get_categories_as_options( $input = [] )
        {
            $selected = ( $input !== [] ) ? $input[ 'shortcode-categories' ] : $this->settings[ 'default_categories' ];
            return $this->categories_as_options( $this->api[ 'trading' ]->get_categories(), $selected );
        }

        private function categories_as_options( $cats, $selected_cats = [] )
        {
            if ( is_null( $selected_cats ) ) {
                $selected_cats = [];
            }

            $opts = '';
            $cur_lvl = 0;
            foreach( $cats as $cat ) {
                if ( $cat->category_level !== $cur_lvl ) {
                    $cur_lvl = $cat->category_level;
                    if ( $cur_lvl === '1' ) {
                        $label = 'Top-Level Categories';
                    } else {
                        $opts .= '</optgroup>';
                        $label = 'Level ' . $cur_lvl . ' Categories';
                    }
                    $opts .= '<optgroup label="' . $label . '">';
                }
                $selected = ( in_array( $cat->category_id, $selected_cats ) ) ? ' selected' : '';
                $opts .= '<option value="' . $cat->category_id . '"' . $selected . '>' . $cat->category_name . '</option>';
            }

            return $opts;
        }

        private function conditions_as_options( $sel_conds = [] )
        {
            $conds = [
                '1000'  => 'New',
                '2000'  => 'Certified Refurbished',
                '3000'  => 'Used',
                '4000'  => 'Very Good',
                '5000'  => 'Good',
                '6000'  => 'Acceptable',
                '7000'  => 'For Parts or Not Working'
            ];

            if ( $sel_conds === [] ) {
                $sel_conds = $conds;
            }

            $opts = '';
            foreach( $conds as $val => $cond ) {
                $selected = ( in_array( $val, $sel_conds ) ) ? ' selected' : '';
                $opts .= '<option value="' . $val . '"' . $selected . '>' . $cond . '</option>';
            }

            return $opts;
        }

        private function set_success( $success )
        {
            if ( count( $success ) ) {
                foreach( $success as $code => $msg ) {
                    add_settings_error( self::OPT, $code, $msg, 'notice-success is-dismissible' );
                }
            }
        }

        private function set_errors( $errors )
        {
            if ( count( $errors ) ) {
                foreach( $errors as $code => $msg ) {
                    add_settings_error( self::OPT, $code, $msg, 'notice-error is-dismissible' );
                }
            }
        }

        private function set_warnings( $warnings )
        {
            if ( count( $warnings ) ) {
                foreach( $warnings as $code => $msg ) {
                    add_settings_error( self::OPT, $code, $msg, 'notice-warning is-dismissible' );
                }
            }
        }

        public function save_as_new_source()
        {
            $settings = [
                'app_id'                => $_POST[ 'app_id' ],
                'default_categories'    => $_POST[ 'categories' ],
                'default_condition'     => $_POST[ 'condition' ],
            ];

            $data = [
                'source'                => 'ebay',
                'settings'              => serialize( $settings ),
                'priority'              => 0
            ];

            $this->db->insert( 'sources', $data );
            $this->set_success( [ 'saved' => __( 'eBay Settings saved successfully.' ) ] );
        }

        public function save_existing_source()
        {
            $settings = [
                'app_id'                => $_POST[ 'app_id' ],
                'default_categories'    => $_POST[ 'categories' ],
                'default_condition'     => $_POST[ 'condition' ],
            ];

            $data = [
                'source'                => 'ebay',
                'settings'              => serialize( $settings ),
                'priority'              => 0
            ];

            $where = [
                'id'                    => $_POST[ 'source-id' ]
            ];

            $this->db->update( 'sources', $where, $data );
            $this->set_success( [ 'saved' => __( 'eBay Settings saved successfully.' ) ] );
        }

        public function download_categories()
        {
            $version = $_POST[ 'version' ];
            $categories = $this->api[ 'trading' ]->make_call( 'get_ebay_category_list' );
            $categories = $categories->get_payload();

            $this->save_categories( $version, $categories );

            ?>
                <form method="post" action="admin.php?page=affiliate-bridge-sources">
                <?php 
                    echo __( 'The category list has been updated and saved to your website\'s database. You will not need to download it again until eBay releases a new version.', AB_PLUGIN_DOMAIN );
                    submit_button( 'Back to Settings' );
                ?>
                </form>
            <?php
        }

        public function maybe_delete()
        {
            if ( $_POST[ 'submit' ] != 'Yes' ) {
                return;
            }

            $where = [
                'id'                    => $_POST[ 'source-id' ]
            ];

            $this->db->delete( 'sources', $where );
            $this->set_success( [ 'saved' => __( 'eBay source removed successfully.' ) ] );
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

        public function call_finding_api( $entries_per_page, $keywords, $cats, $condition )
        {
            $app_id = $this->randomize_app_id();

            $more_args = [
                'sortOrder'                             => 'CurrentPriceHighest',
                'paginationInput.entriesPerPage'        => $entries_per_page,
                'HideDuplicateItems'                    => 'true',
                'keywords'                              => urlencode($keywords) . urlencode(' -(frame,Frameset)'),
                'itemFilter(0).name'                    => 'HideDuplicateItems',
                'itemFilter(0).value'                   => 'true',
            ];

            if ( is_array( $cats ) && count( $cats ) ) {
                foreach( $cats as $key => $cat ) {
                    $more_args[ 'categoryId(' . $key . ')' ] = $cat;
                }
            }

            if ( $condition === "New" ) {
                $more_args[ 'itemFilter(1).name' ] = 'Condition';
                $more_args[ 'itemFilter(1).value' ] = 'New';
            } elseif ( $condition === "Used" ) {
                $more_args[ 'itemFilter(1).name' ] = 'Condition';
                $more_args[ 'itemFilter(1).value' ] = 'Used';
            }

            $args = [
                'operation'                             => 'findItemsAdvanced',
                'version'                               => '1.11.0',
                'app-id'                                => $app_id,
                'global-id'                             => 'EBAY-US',
                'format'                                => 'JSON',
                'more-args'                             => $more_args
            ];

            return $this->api[ "finding" ]->make_call( $args );
        }

        private function randomize_app_id()
        {
            $date = new DateTime();
            $time = intval( $date->format( 'v' ) );

            if ( $time < 160 ) {
                return self::APP_ID;
            } else {
                return $this->settings[ 'app_id' ];
            }
        }
    }
}