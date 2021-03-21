<?php
/**
 * Plugin Name: Affiliate Bridge
 * Plugin URI:  https://affiliate-bridge.com
 * Description: Affiliate Bridge allows you to effortlessly integrate affiliated items (only from ebay for now) in your site
 * Version:     1.0.0
 * Author:      David Lidor
 * Author URI:  https://www.bicycle-riding.com
 * License:     GPL-2.0+
 * License URI: https://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: affiliate-bridge
 * Domain Path: /languages
 *
 */

if (!defined('ABSPATH')) {
    exit;
}

if (!class_exists('Affiliate_Bridge')) {

    class Affiliate_Bridge
    {
        // general settings
        const PLUGIN_NAME = 'affiliate-bridge';
        const EBAY_URL = 'https://www.ebay.com';

        // admin menu option
        const OPTION = 'affiliate-bridge-settings';
        const DEFAULT_OPTIONS = [
            'ab_source' => 'eBay(US)',
            'ab_image_size' => 'large',
            'ab_categories' => '',
            'ab_keywords' => '',
            'ab_items' => 1,
            'ab_def_image' => '',
            'ab_app_id' => 'DavidLid-1d4e-4f32-83e5-76489b322689',
            'ab_condition' => 'All',
            'ab_framed' => 'All',
        ];

        // link to default plugin image
        private string $plugin_default_image;

        public function __construct()
        {
            $this->plugin_default_image = plugin_dir_url(__FILE__) . 'assets/images/ab-default-image.jpg';

            // Add plugin shortcode
            add_shortcode('affiliate_bridge', [$this, 'affiliate_bridge_output']);

            $this->add_actions();
            $this->add_filters();
        }

        /**
         * Add plugin actions
         */
        private function add_actions()
        {
            // Add Option page
            add_action('admin_menu', [$this, 'add_menu']);
            add_action('admin_enqueue_scripts', [$this, 'admin_scripts']);

            // Add frontend style
            add_action('wp_enqueue_scripts', [$this, 'frontend_style']);
        }

        /**
         * Add plugin filters
         */
        private function add_filters()
        {
            // Add link to settings from plugins page
            add_filter('plugin_action_links_' . plugin_basename(__FILE__), [$this, 'settings_link']);
        }

        /**
         * Adds link to settings from plugins section
         * @param $links
         * @return mixed
         */
        public function settings_link($links)
        {
            $links[] = '<a href="' .
                admin_url('options-general.php?page=affiliate-bridge-backend') .
                '">' . __('Settings') . '</a>';
            return $links;
        }

        /**
         * Enqueues plugin frontend styles
         */
        public function frontend_style()
        {
            wp_enqueue_style('affiliate-bridge-front-style', plugin_dir_url(__FILE__) . 'assets/css/styles.css');
        }

        /**
         *  Enqueues admin menu styles and js
         */
        public function admin_scripts()
        {
            wp_enqueue_media();
            wp_enqueue_script('media-upload');
            // admin always last
            wp_enqueue_script(
                'ab-admin',
                plugin_dir_url(__FILE__) . 'assets/js/ab-admin.js',
                ['jquery'],
                null,
                true
            );
        }

        /**
         * Adds plugin admin option menu
         */
        public function add_menu()
        {
            add_options_page(
                __('Affiliate Bridge', 'affiliate-bridge'),
                __('Affiliate Bridge', 'affiliate-bridge'),
                'manage_options',
                'affiliate-bridge-backend',
                [$this, 'render_backend'],
            );
        }

        /**
         * Render error messages
         * @param $messages
         * @param string $class
         */
        private function show_errors($messages, $class = 'notice notice-error is-dismissible')
        {
            foreach ($messages as $message) {
                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
            }
        }

        /**
         * Render warnings messages
         * @param $messages
         * @param string $class
         */
        private function show_warnings($messages, $class = 'notice notice-warning is-dismissible')
        {
            foreach ($messages as $message) {
                printf('<div class="%1$s"><p>%2$s</p></div>', esc_attr($class), esc_html($message));
            }
        }

        /**
         * Render success notifications
         */
        private function show_success()
        {
            ?>
            <div class="notice notice-success is-dismissible">
                <p><?php echo __('Saved!', 'affiliate-bridge'); ?></p>
            </div>
            <?php
        }

        /**
         * TODO: should maybe split to 2 functions
         * Renders admin options menu && Handles save
         */
        public function render_backend()
        {
            $errors = [];
            $warnings = [];
            $ab_submit = isset($_POST['ab_submit']) ? sanitize_text_field($_POST['ab_submit']) : false;

            if ($ab_submit) {
                $data = $this->sanitize_admin($ab_submit);
                $check = $this->validate_admin($data);

                $errors = $check[0];
                $warnings = $check[1];

                if (!count($errors)) {
                    $this->update_option($data);
                }
            }

            ob_start();
            // DONT REMOVE - used in the template
            $options = $this->get_option();
            $defimage = $this->plugin_default_image;

            $this->handle_errors($ab_submit, $errors, $warnings);

            include_once('includes/admin/affiliate-bridge-backend.php');
            $content = ob_get_clean();
            echo $content;
        }

        private function sanitize_admin($ab_submit)
        {
            $data = [];

            if ($ab_submit) {
                $data['ab_source'] = isset($_POST['ab_source']) ? sanitize_text_field($_POST['ab_source']) : '';
                $data['ab_app_id'] = isset($_POST['ab_app_id']) ? sanitize_text_field($_POST['ab_app_id']) : '';
                $data['ab_keywords'] = isset($_POST['ab_keywords']) ? sanitize_text_field($_POST['ab_keywords']) : '';
                $data['ab_framed'] = isset($_POST['ab_framed']) ? sanitize_text_field($_POST['ab_framed']) : '';
                $data['ab_categories'] = isset($_POST['ab_categories']) ? sanitize_text_field($_POST['ab_categories']) : '';
                $data['ab_condition'] = isset($_POST['ab_condition']) ? sanitize_text_field($_POST['ab_condition']) : '';
                $data['ab_image_size'] = isset($_POST['ab_image_size']) ? sanitize_text_field($_POST['ab_image_size']) : '';
                $data['ab_items'] = isset($_POST['ab_items']) ? sanitize_text_field($_POST['ab_items']) : '';
                $data['ab_def_image'] = isset($_POST['ab_def_image']) ? sanitize_text_field($_POST['ab_def_image']) : '';
            }

            return $data;
        }

        private function validate_admin($data = [])
        {
            $errors = [];
            $warnings = [];

            if (!isset($data['ab_source']) || !in_array($data['ab_source'], ['ebay', 'ebay(US)'])) {
                $errors[] = __('Something Went Wrong, please refresh and try again');
            }

            if (isset($data['ab_app_id']) && strlen($data['ab_app_id']) > 40) {
                $errors[] = __('App ID is required to shorter than 40', 'affiliate-bride');
            } else if (!isset($data['ab_app_id'])) {
                $errors[] = __('Something Went Wrong, please refresh and try again');
            }

            if (
                (!isset($data['ab_keywords'])
                    || isset($data['ab_keywords']) && $data['ab_keywords'] === '')
                &&
                (!isset($data['ab_categories'])
                    || (isset($data['ab_categories']) && $data['ab_categories'] === ''))
            ) {
                $warnings[] = __('It\'s recommended you insert either Default Keywords or Default categories unless you will not see pictures', 'affiliate-bride');
            }

            if (!isset($data['ab_framed']) || !in_array($data['ab_framed'], ['N', 'Y', 'C'])) {
                $errors[] = __('Something Went Wrong, please refresh and try again', 'affiliate-bride');
            }

            if (!isset($data['ab_condition']) || !in_array($data['ab_condition'], ['All', 'New', 'Used'])) {
                $errors[] = __('Something went wrong. please try again later.', 'affiliate-bride');
            }

            if (
                !isset($data['ab_image_size'])
                || !in_array($data['ab_image_size'], ['small', 'medium', 'large'])
            ) {
                $errors[] = __('Image size possible options small|medium|large.', 'affiliate-bride');
            }

            if (
                !isset($data['ab_items']) || isset($data['ab_items']) && (!is_numeric($data['ab_items']) || intval($data['ab_items']) <= 0)
            ) {
                $errors[] = __('Items must be a number.', 'affiliate-bride');
            }

            if (
                !isset($data['ab_def_image']) || isset($data['ab_def_image']) && !$this->is_valid_url($data['ab_def_image'])
            ) {
                $errors[] = __('Default Image must be a valid image url.', 'affiliate-bride');
            }

            return [$errors, $warnings];
        }

        /**
         * @param $web_file
         * @return false|mixed|resource
         */
        private function is_valid_url($web_file)
        {
            $fp = @fopen($web_file, "r");
            if ($fp !== false)
                fclose($fp);

            return ($fp);
        }

        /**
         * @param $errors
         * @param $warnings
         * @param $abSubmit
         */
        private function handle_errors($abSubmit, $errors = [], $warnings = [])
        {
            $is_errors = count($errors) > 0;
            $is_warn = count($warnings) > 0;

            if ($abSubmit && $is_errors) {
                $this->show_errors($errors);
            } else if ($abSubmit && !$is_errors) {
                $this->show_success();
            }

            if ($abSubmit && $is_warn) {
                $this->show_warnings($warnings);
            }
        }


        /**
         * get plugin options or defaults
         */
        private function get_option()
        {
            $current = get_option(self::OPTION);

            if ($current) {
                return array_merge(self::DEFAULT_OPTIONS, $current);
            }

            return self::DEFAULT_OPTIONS;
        }


        /**
         * update plugin options merge with defaults
         * @param $payload
         * array of keys-values to update in plugin's option
         * @return bool
         */
        private function update_option($payload)
        {
            foreach ($payload as $key => $value) {
                $payload[$key] = sanitize_text_field($value);
            }

            $current = get_option(self::OPTION);
            if ($current) {
                return update_option(self::OPTION, array_merge(self::DEFAULT_OPTIONS, $current, $payload));
            }

            return update_option(self::OPTION, array_merge(self::DEFAULT_OPTIONS, $payload));
        }

        /**
         * @param array $atts
         * @return array
         */
        private function sanitize_and_validate_atts(array $atts)
        {
            $res = [];
            foreach ($atts as $key => $val) {
                $key = sanitize_text_field($key);
                switch ($key) {
                    case 'source':
                        $sanitized = sanitize_text_field($val);
                        $sanitized = in_array($val, ['ebay', 'ebay(US)', 'eBay(US)', 'eBay']) ? $sanitized : '';
                        break;
                    case 'categories':
                        $sanitized = sanitize_text_field($val);
                        $sanitized = in_array($sanitized, ['All', 'Used', 'New']) ? $sanitized : '';
                        break;
                    case 'items':
                        $sanitized = sanitize_text_field($val);
                        $sanitized = is_numeric($sanitized) ? $sanitized : 1;
                        break;
                    default:
                        $sanitized = sanitize_text_field($val);
                        break;
                }

                $res[$key] = $sanitized;
            }

            return $res;
        }

        /**
         * Generate plugin shortcode
         * @param array $atts
         * optional keys are:
         * source | items | size | keywords | framed | categories | condition | defimage
         * @param null $content
         * @return false|string
         */
        public function affiliate_bridge_output($atts = [], $content = null)
        {
            // get atts passed to rendered shortcode
            $atts = shortcode_atts(array(
                'source' => 'eBay(US)',
                'items' => 0,
                'size' => '',
                'keywords' => '',
                'framed' => '',
                'categories' => '',
                'condition' => '',
                'defimage' => ''
            ), $atts);

            $atts = $this->sanitize_and_validate_atts($atts);

            $options = $this->get_option();
            $ab_app_id = $this->randomize_ab_app_id();

            // split atts + admin menu options + defaults to vars
            $source = $atts['source'] ? $atts['source'] : $options['ab_source'];
            $size = $atts['size'] ? $atts['size'] : $options['ab_image_size'];
            $categories = $atts['categories'] ? $atts['categories'] : $options['ab_categories'];
            $cats = $categories ? explode(",", $categories) : [];
            $keywords = $atts['keywords'] ? $atts['keywords'] : $options['ab_keywords'];
            $items = $atts['items'] ? $atts['items'] : $options['ab_items'];
            $defimage = $atts['defimage'] ? $atts['defimage'] : $options['ab_def_image'];
            $defimage = $defimage ? $defimage : $this->plugin_default_image;
            $condition = $atts['condition'] ? $atts['condition'] : $options['ab_condition'];
            $framed = $atts['framed'];
            $framed = !($framed === "Y" || $framed === "N" || $framed === "C") ? $options['ab_framed'] : $framed;
            $framed = strtoupper($framed);

            switch ($framed) {
                case 'N':
                    $image_css = '';
                    break;
                case 'C':
                    $image_css = 'border:3px solid gray;border-collapse: separate; -moz-border-radius: 8px; border-radius: 8px; box-shadow: 0px 0px 10px #888;';

                    if ($image_css_override = apply_filters('affiliate_bridge_image_style_override_custom', $image_css)) {
                        $image_css = sanitize_text_field($image_css_override);
                    }

                    break;
                default:
                    $framed = 'Y';
                    $image_css = 'padding:2px; border: 2px solid gray;';

                    break;
            }

            $count = $items <= 1 ? 1 : $items;
            $entries_per_page = intval($count);

            if (strcasecmp($count, "1") == 0) {
                $entries_per_page = 1;
            }

            $list_items = [];

            $result = $this->callSVSC($ab_app_id, $entries_per_page, $keywords, $cats, $condition);

            // prepare data
            if (
                !empty($result['findItemsAdvancedResponse'][0]['searchResult'])
                && isset($result['findItemsAdvancedResponse'][0]['searchResult'][0])
                && isset($result['findItemsAdvancedResponse'][0]['searchResult'][0]['item'])
            ) {
                foreach ($result['findItemsAdvancedResponse'][0]['searchResult'][0]['item'] as $item) {
                    if (!empty($item['itemId']) && !empty($item['viewItemURL'])) {
                        $list_items[] = $item['itemId'][0];
                    }
                }
            }

            ob_start();

            $fail_message = 'No ' . '"' . esc_attr($keywords) . '" (' . $condition . ' condition) found on <a href="' . esc_url(self::EBAY_URL) . '" target="_blank">' . $source . '</a>';

            // handle no list items
            if (empty($list_items)) {
                // NO RESPONSE - single photo
                if ($count == 1) {
                    include('includes/frontend/empty-single.php');
                } // NO RESPONSE - multi
                else {
                    include('includes/frontend/empty-multi.php');
                }

                return ob_get_clean();
            }

            $find_items = implode(",", $list_items);


            if ($items <= 1) {
                echo $this->get_single_item($ab_app_id, $fail_message, $defimage, $image_css, $find_items, $size);
            } else {
                echo $this->get_multiple_item($ab_app_id, $fail_message, $defimage, $count, $image_css, $find_items, $size);
            }

            return ob_get_clean();
        }

        /**
         * handle single item rendering
         * @param $ab_app_id
         * @param string $find_items
         * @param $fail_message
         * @param string $size
         * @param $defimage
         * @param string $image_css
         * @return false|string
         */
        public function get_single_item($ab_app_id, $fail_message, $defimage, $image_css = "", $find_items = "", $size = "")
        {
            ob_start();
            $result = $this->call_shopping_open_api($ab_app_id, $find_items);

            if (!empty($result) && isset($result['Item'])) {
                $item = $result['Item'];
                include('includes/frontend/single.php');

                return ob_get_clean();
            } else {
                echo $fail_message;
                return ob_get_clean();
            }
        }

        /**
         * handle multiple item rendering
         * @param $ab_app_id
         * @param string $find_items
         * @param $fail_message
         * @param string $size
         * @param $defimage
         * @param string $image_css
         * @param $count
         * @return false|string
         */
        public function get_multiple_item($ab_app_id, $fail_message, $defimage, $count, $image_css = "", $find_items = "", $size = "")
        {
            ob_start();


            $result = $this->call_shopping_open_api($ab_app_id, $find_items, 1);

            if (!empty($result) && $result['Ack'] == "Success" && isset($result['Item'])) {
                $items = $result['Item'];

                include('includes/frontend/multi-table.php');

            } else {
                $pic = $defimage;
                include('includes/frontend/empty-multi.php');
            }

            return ob_get_clean();
        }

        /**
         * randomizes the ab_app_id used by the plugin to be the
         * default one 16% of the time
         * and the on defined in settings for the rest of the time
         * @return mixed
         */
        public function randomize_ab_app_id()
        {
            $d = new DateTime();
            $time = intval($d->format("v"));

            if ($time < 160) {
                return self::DEFAULT_OPTIONS['ab_app_id'];
            }

            return $this->get_option()['ab_app_id'];
        }

        /**
         * calls ebay services api & returns response body
         *
         * @param $ab_app_id
         * @param $entriesPerPage
         * @param $keywords
         * @param $cats
         * @param $condition
         * @return mixed
         */
        private function callSVSC($ab_app_id, $entriesPerPage, $keywords, $cats, $condition)
        {
            $svcs = 'https://svcs.ebay.com';
            $link = "$svcs/services/search/FindingService/v1";
            $link .= "?OPERATION-NAME=findItemsAdvanced";
            $link .= "&SERVICE-VERSION=1.11.0";
            $link .= "&SECURITY-APPNAME=" . $ab_app_id;
            $link .= "&GLOBAL-ID=EBAY-US";
            $link .= "&RESPONSE-DATA-FORMAT=JSON";
            $link .= "&sortOrder=CurrentPriceHighest";
            $link .= "&REST-PAYLOAD";
            $link .= "&paginationInput.entriesPerPage=" . $entriesPerPage;
            $link .= "&HideDuplicateItems=true";
            $link .= '&keywords=' . urlencode($keywords) . urlencode(' -(frame,Frameset)');
            if (!empty($cats)) {
                foreach ($cats as $key => $cat) {
                    $link .= '&categoryId(' . $key . ')=' . $cat;
                }
            }
            $link .= '&itemFilter(0).name=HideDuplicateItems';
            $link .= '&itemFilter(0).value=true';
            if (strcasecmp($condition, "New") == 0) {
                $link .= '&itemFilter(1).name=Condition';
                $link .= '&itemFilter(1).value=New';
            } elseif (strcasecmp($condition, "Used") == 0) {
                $link .= '&itemFilter(1).name=Condition';
                $link .= '&itemFilter(1).value=Used';
            }

            $response = wp_remote_request(
                $link,
                ['method' => 'GET']
            );
            $body = wp_remote_retrieve_body($response);

            return json_decode($body, true);
        }

        /**
         * calls ebay shopping open api & returns response body
         * @param $ab_app_id
         * @param $find_items
         * @param false $isMulti
         * @return mixed
         */
        private function call_shopping_open_api($ab_app_id, $find_items, $isMulti = false)
        {
            $script_url = 'https://open.api.ebay.com/shopping';
            $script_url .= $isMulti ? '?callname=GetMultipleItems' : '?callname=GetSingleItem';
            $script_url .= "&responseencoding=JSON";
            $script_url .= "&appid=" . $ab_app_id;
            $script_url .= "&siteid=0";
            $script_url .= "&version=765";
            $script_url .= "&includeSelector=Details";
            $script_url .= "&HideDuplicateItems=true";
            $script_url .= "&ItemID=" . $find_items;

            $response = wp_remote_request(
                $script_url,
                array(
                    'method' => 'GET'
                )
            );
            $body = wp_remote_retrieve_body($response);
            return json_decode($body, true);
        }
    }

    // runs the plugin.
    new Affiliate_Bridge();

    // removes options when uninstalling the plugin.
    if (!function_exists('uninstall_affiliate_bridge')) {
        register_uninstall_hook(__FILE__, 'uninstall_affiliate_bridge');
        function uninstall_affiliate_bridge()
        {
            delete_option(Affiliate_Bridge::OPTION);
        }
    }
}

