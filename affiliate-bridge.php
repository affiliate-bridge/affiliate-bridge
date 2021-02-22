<?php
/**
 * Plugin Name: Affiliate Bridge
 * Plugin URI:
 * Description: Affiliate Bridge.
 * Version:     1.0.13
 * Author:      David Lidor
 * Author URI:  http://www.bicycle-riding.com
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain: affiliate_bridge
 */

if (! class_exists('affiliate_bridge')) {
	
    class affiliate_bridge
    {
        var $plugin_name = "";
        public function __construct()
        {
            $this->plugin_name = "ebay-affiliate-bridge";
            add_shortcode('affiliate_bridge', array($this, 'affiliate_bridge_output'));
            // Add Btn after 'Media'
            add_action('admin_menu', array($this, 'affiliate_bridge_admin_menu'));
            add_action('admin_enqueue_scripts', array( $this, 'admin_style' ));

            add_filter('plugin_action_links_'.plugin_basename(__FILE__), array( $this, 'affiliate_bridge_settings_link'));
        }
        public function affiliate_bridge_settings_link($links)
        {
            $links[] = '<a href="' .
                admin_url('options-general.php?page=affiliate-bridge-backend') .
                '">' . __('Settings') . '</a>';
            return $links;
        }
        // Update CSS within in Admin
        public function admin_style()
        {
            wp_enqueue_media();
            wp_enqueue_script('media-upload');
            // admin always last
            wp_enqueue_script('ab-admin', plugin_dir_url(__FILE__) . 'assets/ab-admin.js', array( 'jquery' ), null, true);
        }
        public function affiliate_bridge_admin_menu()
        {
            add_options_page(__('Affiliate Bridge', ''), __('Affiliate Bridge', ''), 'manage_options', 'affiliate-bridge-backend', array($this, 'affiliate_bridge_backend'));
        }
        public function affiliate_bridge_backend()
        {
            $data = [];
            if (isset($_POST['ab_submit'])) {
                $data['ab_source'] = (isset($_POST['ab_source']) ? $_POST['ab_source'] : '');
                $data['ab_app_id'] = (isset($_POST['ab_app_id']) ? $_POST['ab_app_id'] : '');
                $data['ab_keywords'] = (isset($_POST['ab_keywords']) ? $_POST['ab_keywords'] : '');
                $data['ab_framed'] = (isset($_POST['ab_framed']) ? $_POST['ab_framed'] : '');
                $data['ab_categories'] = (isset($_POST['ab_categories']) ? $_POST['ab_categories'] : '');
                $data['ab_image_size'] = (isset($_POST['ab_image_size']) ? $_POST['ab_image_size'] : '');
                $data['ab_items'] = (isset($_POST['ab_items']) ? $_POST['ab_items'] : '');
                $data['ab_def_image'] = (isset($_POST['ab_def_image']) ? $_POST['ab_def_image'] : '');
                $data['ab_condition'] = (isset($_POST['ab_condition']) ? $_POST['ab_condition'] : '');
                foreach ($data as $key => $value) {
                    update_option($key, $value);
                }
            }
            $def_image = plugin_dir_url(__FILE__) . 'images/affiliate-bridge-default-image.jpg';
            ob_start();
            include_once('admin/affiliate_bridge_backend.php');
            $content = ob_get_clean();
            echo $content;
        }
        /**
         * Output engraving field.
         */

//=====================================================================================================

        public function affiliate_bridge_output($atts = [], $content = null)
        {
            extract(shortcode_atts(array(
                'source' => 'ebay',
                'items' => 0,
                'size' => '',
                'keywords' => '',
                'framed' => '',
                'categories' => '',
                'condition' => '',
                'defimage' => ''
            ), $atts));

            $source = 'eBay(US)';
			$sourceURL = 'http://www.ebay.com';
            $size = (($size) ? $size : (get_option("ab_image_size") ? get_option("ab_image_size") : 'large'));
            $categories = (($categories) ? $categories : (get_option("ab_categories") ? get_option("ab_categories") : ''));
            $cats = (($categories) ? explode(",", $categories) : []);
            $keywords = (($keywords) ? $keywords : (get_option("ab_keywords") ? get_option("ab_keywords") : ''));
            $items = (($items) ? $items : (get_option("ab_items") ? get_option("ab_items") : 1));
            $defimage = (($defimage) ? $defimage : (get_option("ab_def_image") ? get_option("ab_def_image") : ''));
            $defimage = (($defimage) ? $defimage : plugin_dir_url(__FILE__) . 'images/affiliate-bridge-default-image.jpg');
            $ab_app_id = (get_option("ab_app_id") ? get_option("ab_app_id") : 'DavidLid-1d4e-4f32-83e5-76489b322689');

            ob_start();

			if ($condition == TRUE) {
				$condition = $condition;
			}
			else {
				if (get_option("ab_condition") == TRUE) {
					$condition = get_option("ab_condition");
				}
				else {
					$condition = 'All';
				}	
			}

            if (!($framed === "Y" || $framed === "N" || $framed === "C")) {
				$framed = get_option("ab_framed");
			}
			if (strcasecmp($framed, "N") == 0) {
				$imageCss = '';
			}
			elseif (strcasecmp($framed, "C") == 0) {
				$imageCss = 'border:3px solid gray;border-collapse: separate; -moz-border-radius: 8px; border-radius: 8px; box-shadow: 0px 0px 10px #888;';
			}
			else {
				$framed = 'Y';			
				$imageCss = 'padding:2px; border: 2px solid gray;';
			}

            $count = (( $items <= 1 ) ? '1' : $items);
            $entriesPerPage = intval($count) * 2;
			if (strcasecmp($count, "1") == 0) {
				$entriesPerPage = 1;
			}
			
            $link = "https://svcs.ebay.com/services/search/FindingService/v1";
            $link .= "?OPERATION-NAME=findItemsAdvanced";
            $link .= "&SERVICE-VERSION=1.11.0";
            $link .= "&SECURITY-APPNAME=". $ab_app_id;
            $link .= "&GLOBAL-ID=EBAY-US";
            $link .= "&RESPONSE-DATA-FORMAT=JSON";
            $link .= "&sortOrder=CurrentPriceHighest";
            $link .= "&REST-PAYLOAD";
            $link .= "&paginationInput.entriesPerPage=" . $entriesPerPage;
            $link .= "&HideDuplicateItems=true";
            $link .= '&keywords=' . urlencode($keywords) . urlencode(' -(frame,Frameset)');
            if (!empty($cats)) {
                foreach ($cats as $key => $cat) {
                    $link .= '&categoryId('. $key .')='. $cat ;
                }
            }
			$link .= '&itemFilter(0).name=HideDuplicateItems';
			$link .= '&itemFilter(0).value=true';
			if (strcasecmp($condition, "New") == 0) {
				$link .= '&itemFilter(1).name=Condition';
				$link .= '&itemFilter(1).value=New';
			}
			elseif (strcasecmp($condition, "Used") == 0) {
				$link .= '&itemFilter(1).name=Condition';
				$link .= '&itemFilter(1).value=Used';
            }
/*
            echo $link;
            die;
*/

            $response = wp_remote_request(
                $link,
                array(
                    'method'     => 'GET'
                )
            );

            $body = wp_remote_retrieve_body($response);
            $result = json_decode($body, true);

            $list_items = [];
            if (!empty($result['findItemsAdvancedResponse'][0]['searchResult']) && isset($result['findItemsAdvancedResponse'][0]['searchResult'][0]) && isset($result['findItemsAdvancedResponse'][0]['searchResult'][0]['item'])) {
                foreach ($result['findItemsAdvancedResponse'][0]['searchResult'][0]['item'] as $item) {
                    if (!empty($item['itemId']) && !empty($item['viewItemURL'])) {
                        $list_items[] = $item['itemId'][0];
                    }
                }
            }

            if (!empty($list_items)) {
				$failMsg = 'No ' . '"' . $keywords . '" (' . $condition . ' condition) found on <a href="' . $sourceURL . '" target="_blank">' . $source . '</a>';
				$find_items = implode(",", $list_items);
				if ($items <= 1) {
                    echo $this->get_single_item($find_items, $failMsg, $size, $defimage, $imageCss);
                } 
				else {
                    echo $this->get_multiple_item($find_items, $failMsg, $size, $defimage, $imageCss, $count);
                }
            } 
			else {
				if ($count == 1) {
					$failMsg = 'No ' . '"' . $keywords . '" (' . $condition . ' condition) found on <a href="' . $sourceURL . '" target="_blank">' . $source . '</a>';
					if ($size == "small") {
						$size = "120";
						echo '<p><center><img style="' . $imageCss . '" src="' . $defimage . '" border="0" width="' . $size . '" alt="Default Image" title="Default Image"></center></p>';
					} elseif ($size == "medium") {
						$size ="180";
						echo '<p><center><img style="' . $imageCss . '" src="' . $defimage . '" border="0" width="' . $size . '" alt="Default Image" title="Default Image"></center></p>';
					} elseif ($size == "large") {
						$size ="400";
						echo '<p><center><img style="' . $imageCss . '" src="' . $defimage . '" border="0" width="' . $size . '" alt="Default Image" title="Default Image"></center><center><b>' . $failMsg .'</b></center></p>';
					} else {
						echo '<p><center><img style="' . $imageCss . '" src="' . $defimage . '" border="0" width="' . $size . '" alt="Default Image" title="Default Image"></center><center><b>' . $failMsg .'</b></center></p>';
					}
				}
				else {
					$failMsg = 'No ' . '"' . $keywords . '" (' . $condition . ' condition) found on <a href="' . $sourceURL . '" target="_blank">' . $source . '</a>';
					echo '<p><table style="width:100%" align:center border="3" cellspacing="0" cellpadding="3">';
					echo '<head>';
					echo '<colgroup><col style="width: 20%;"><col></colgroup>';
					echo '</head>';
					echo '<tbody>';
					$pic = $defimage;
					echo '<tr>';
					echo '<td align=center valign=top style="text-align: center; width: 25%; vertical-align: middle !important; border:1px solid #dedede; padding: 10px;"><img style="margin: 5px auto 5px auto; display: block;' . $imageCss . '" src="' . $pic . '" border="0" alt=""></td>';
					echo '<td align="center" valign="bottom" style="padding: 10px; vertical-align: middle !important; border:1px solid #dedede;"><b>' . $failMsg . '</b></td>';
					echo '</tr>';
					echo '</tr>';
					echo '</tbody></table></p>';
				}
            }
            return ob_get_clean();
        }

//=====================================================================================================

        public function get_single_item($find_items = "", $failMsg, $size = "", $defimage, $imageCss = "")
       {
            ob_start();
            $ab_app_id = (get_option("ab_app_id")) ? get_option("ab_app_id") : 'DavidLid-1d4e-4f32-83e5-76489b322689';
            $script_url = "https://open.api.ebay.com/shopping";
            $script_url .= "?callname=GetSingleItem";
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
                    'method'     => 'GET'
                )
            );
			$body = wp_remote_retrieve_body($response);
            $result = json_decode($body, true);

            if (!empty($result) && isset($result['Item'])) {
                $item = $result['Item'];
                $ConditionDisplayName = isset($item['ConditionDisplayName']) ? $item['ConditionDisplayName'] . ' - ' : '';
                $CurrentPrice = $item['CurrentPrice'];
                $priceTitle = $ConditionDisplayName . $CurrentPrice['Value'] . ' ' . $CurrentPrice['CurrencyID'];
                $title = $item['Title'];
                if ($item['PictureURL'] != null) {
                    $pic = $item['PictureURL'][0];
                } else {
                    $pic = $item['GalleryURL'];
                }
                if (!$pic && $defimage) {
                    $pic = $defimage;
                }

                $itemURL = $item['ViewItemURLForNaturalSearch'];

                if ($title && $itemURL && $size == "small") {
                    echo '<p><center><a href="' . $itemURL . '" target="_blank"><img style="' . $imageCss . '" src="' . $pic . '" border="0" width="110" height="90" alt="' . $title . ' (' . $priceTitle . ')" title="' . $title . ' (' . $priceTitle . ')"></a></center></p>';
                } elseif ($title && $itemURL && $size == "medium") {
                    echo '<p><center><a href="' . $itemURL . '" target="_blank"><img style="' . $imageCss . '" src="' . $pic . '" border="0" width="200" height="120" alt="' . $title . ' (' . $priceTitle . ')" title="' . $title . ' (' . $priceTitle . ')"></a></center></p>';
                } elseif ($title && $itemURL && $size == "large") {
                    echo '<p><center><a href="' . $itemURL . '" target="_blank"><img style="' . $imageCss . '" src="' . $pic . '" border="0" width="400" alt="' . $title . ' (' . $priceTitle . ')" title="' . $title . ' (' . $priceTitle . ')"></a></center><center><a href="' . $itemURL . '" target="_blank">' . $title . '<br><b>(' . $priceTitle . ')</b></a></center></p>';
                } elseif ($title && $itemURL) {
                    echo '<p><center><a href="' . $itemURL . '" target="_blank"><img style="' . $imageCss . '" width="' . $size . '" src="' . $pic . '" border="0" alt="' . $title . ' (' . $priceTitle . ')" title="' . $title . ' (' . $priceTitle . ')"></a></center><center><a href="' . $itemURL . '" target="_blank">' . $title . '<br><b>(' . $priceTitle . ')</b></a></center></p>';
                }
            }
			else {
                $pic = $defimage;
                if ($size == "small") {
                    echo '<p><center><img style="' . $imageCss . '" src="' . $pic . '" border="0" width="110" height="90" alt=""></a></center></p>';
                } elseif ($size == "medium") {
                    echo '<p><center><a href="' . $itemURL . '" target="_blank"><img style="' . $imageCss . '" src="' . $pic . '" border="0" width="200" height="120" alt=""></a></center></p>';
                } elseif ($size == "large") {
                    echo '<p><center><a href="' . $itemURL . '" target="_blank"><img style="' . $imageCss . '" src="' . $pic . '" border="0" width="400" alt=""></a></center><center><b>' . $failMsg . '</b></center></p>';
                } else {
                    echo '<p><center><a href="' . $itemURL . '" target="_blank"><img style="' . $imageCss . '" width="' . $size . '" src="' . $pic . '" border="0" alt=""></a></center><center><b>' . $failMsg . '</b></center></p>';
                }
			}
            return ob_get_clean();
        }

//=====================================================================================================

        public function get_multiple_item($find_items = "", $failMsg, $size = "", $defimage, $imageCss = "", $count)
        {
            ob_start();
            $ab_app_id = (get_option("ab_app_id")) ? get_option("ab_app_id") : 'DavidLid-1d4e-4f32-83e5-76489b322689';
            $script_url = "https://open.api.ebay.com/shopping";
            $script_url .= "?callname=GetMultipleItems";
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
                    'method'     => 'GET'
                )
            );

            $body = wp_remote_retrieve_body($response);
            $result = json_decode($body, true);
            if (!empty($result) && $result['Ack'] == "Success" && isset($result['Item'])) {
                $items = $result['Item'];
                if (!empty($items)) {
                    echo '<p><table style="width:100%" align:center border="3" cellspacing="0" cellpadding="3">';
					echo '<head>';
                    echo '<colgroup><col style="width: 20%;"><col></colgroup>';
                    echo '</head>';
                    echo '<tbody>';
 					$titles = [];
 					$priceTitles = [];
					foreach ($items as $item) {
						if ((sizeof($titles) >= $count) && (sizeof($titles) > 0)){
							break;
						}
                        $ConditionDisplayName = isset($item['ConditionDisplayName']) ? $item['ConditionDisplayName'] . ' - ' : '';
                        $CurrentPrice = $item['CurrentPrice'];
                        $priceTitle = $ConditionDisplayName . $CurrentPrice['Value'] . ' ' . $CurrentPrice['CurrencyID'];
                        $title = $item['Title'];
						if ((in_array($title, $titles)) || (in_array($priceTitle, $priceTitles))) {
							continue;
						}
                        if (!empty($item['PictureURL'])) {
                            $pic = $item['PictureURL'][0];
                        } else {
                            $pic = $item['GalleryURL'];
                        }
                        if (!$pic && $defimage) {
                            $pic = $defimage;
                        }
                        $itemURL = $item['ViewItemURLForNaturalSearch'];

                        echo '<tr>';
				
						echo '<td align=center valign=top style="text-align: center; width: 25%; vertical-align: middle !important; border:1px solid #dedede; padding: 10px;"><a href="' . $itemURL . '" target="_blank">' . '<img style="margin: 5px auto 5px auto; display: block;' . $imageCss . '" src="' . $pic . '" border="0" alt="' . $title . ' (' . $priceTitle . ')" title="' . $title . ' (' . $priceTitle . ')"></a></td>';
                        echo '<td align="center" valign="bottom" style="padding: 10px; vertical-align: middle !important; border:1px solid #dedede;"><a href="' . $itemURL . '" target="_blank">' . $title . '<br><b>(' . $priceTitle . ')</b></a></td>';
                        echo '</tr>';
                        echo '</tr>';
						array_push($titles, $title);
						array_push($priceTitles, $priceTitle);
					}
					echo '</tbody></table></p>';
                }
            }

			else {
				echo '<p><table style="width:100%" align:center border="3" cellspacing="0" cellpadding="3">';
				echo '<head>';
				echo '<colgroup><col style="width: 20%;"><col></colgroup>';
				echo '</head>';
				echo '<tbody>';
				$pic = $defimage;
				echo '<tr>';
				echo '<td align=center valign=top style="text-align: center; width: 25%; vertical-align: middle !important; border:1px solid #dedede; padding: 10px;"><img style="margin: 5px auto 5px auto; display: block;' . $imageCss . '" src="' . $pic . '" border="0" alt=""></td>';
				echo '<td align="center" valign="bottom" style="padding: 10px; vertical-align: middle !important; border:1px solid #dedede;"><b>' . $failMsg . '</b></td>';
				echo '</tr>';
				echo '</tr>';
				echo '</tbody></table></p>';
			}

            return ob_get_clean();
        }

//=====================================================================================================
       
    }

    $affiliate_bridge = new affiliate_bridge();
    
    if(!function_exists('deactivate_affiliate_bridge')){
	    register_uninstall_hook( __FILE__ , 'deactivate_affiliate_bridge');
	    function deactivate_affiliate_bridge()
	    {
	        $data = ['ab_source', 'ab_app_id', 'ab_keywords', 'ab_framed', 'ab_categories', 'ab_condition', 'ab_image_size', 'ab_items', 'ab_def_image'];
	        foreach ($data as $value) {
	            delete_option($value);
	        }
	    }
    }
}

