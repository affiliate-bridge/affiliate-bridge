<?php
namespace Affiliate_Bridge\Sources\Ebay
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    use \DomDocument;
    use \SimpleXMLElement;
    use \Affiliate_Bridge\Debug;
    use \Affiliate_Bridge\Sources\Response as BaseResponse;

    class Response extends BaseResponse
    {
        protected $raw;
        protected $errors;
        protected $payload;

        public function __construct( $call, $raw )
        {
            $this->call = $call;
            $this->raw = $raw;
            $this->errors = [];
            $this->payload = '';
        }

        public function is_error()
        {
            if( stristr( $this->raw, 'HTTP 404' ) || $this->raw === '' ) {
                return true;
            } else {
                return false;
            }
        }

        public function is_XML()
        {
            if( stristr( $this->raw, '?xml version="1.0"' ) ) {
                return true;
            } else {
                return false;
            }
        }

        public function is_JSON()
        {
            if( ! is_null( json_decode( $this->raw ) ) ) {
                return true;
            } else {
                return false;
            }
        }

        public function process()
        {
            if ( $this->is_XML() ) {
                $this->process_XML();
            } elseif ( $this->is_JSON() ) {
                $this->process_JSON();
            }
        }

        private function process_XML()
        {
            $xml = new SimpleXMLElement( $this->raw );
            $ack = '' . $xml->Ack;

            $errors = false;
            if ( $ack !== 'Success' ) {
                $errors = $this->get_errors( $xml );
            }

            if ( $ack !== 'Failure' ) {
                $this->set_payload( $xml );
            }   
        }

        private function process_JSON()
        {
            $json = json_decode( $this->raw );
            $ack = $json->findItemsAdvancedResponse[0]->ack[0];

            $errors = false;
            if ( $ack !== 'Success' ) {
                $errors = $this->get_errors( $json );
            }

            if ( $ack !== 'Failure' ) {
                $this->set_payload( $json );
            }  
        }

        public function __toString()
        {
            return $this->payload;
        }

        public function get_payload()
        {
            return $this->payload;
        }

        private function get_errors( &$response )
        {
            if ( $this->is_XML() ) {
                $this->get_XML_errors( $response );
            } elseif ( $this->is_JSON() ) {
                $this->get_JSON_errors( $response );
            }
        }

        private function get_XML_errors( &$xml )
        {
            $errors = $xml->Errors;

            $response = [
                'status'    => 'error',
                'message'   => 'eBay returned the following errors:',
                'errors'    => [
                    [
                        'code'              => '' . $errors->ErrorCode,
                        'shortmessage'      => '' . $errors->ShortMessage,
                        'longmessage'       => '' . $errors->LongMessage,
                        'classification'    => '' . $errors->ErrorClassification,
                    ]
                ]
            ];

            return $response;
        }

        private function get_JSON_errors( &$json )
        {

        }

        private function set_payload( &$response )
        {
            if ( $this->is_XML() ) {
                $this->set_XML_payload( $response );
            } elseif ( $this->is_JSON() ) {
                $this->set_JSON_payload( $response );
            }
        }

        private function set_XML_payload( &$xml )
        {
            if( $this->call === 'get_session_id' ) {
                $this->session_id_XML_payload( $xml );
            } elseif( $this->call === 'fetch_token' ) {
                $this->token_XML_payload( $xml );
            } elseif( $this->call === 'get_ebay_category_list_version' ) {
                $this->category_list_version_XML_payload( $xml );
            } elseif( $this->call === 'get_ebay_category_list' ) {
                $this->categories_XML_payload( $xml );
            }
        }

        private function session_id_XML_payload( &$xml )
        {
            $this->payload = '' . $xml->SessionID;
        }

        private function token_XML_payload( &$xml )
        {
            $this->payload = '' . $xml->eBayAuthToken;
        }

        private function category_list_version_XML_payload( &$xml )
        {
            $this->payload = '' . $xml->CategoryVersion;
        }

        private function categories_XML_payload( &$xml )
        {
            $cats = [];
            foreach( $xml->CategoryArray->Category as $cat ) {
                $cats[] = [
                    'category_id'       => ( isset( $cat->CategoryID ) ) ? '' . $cat->CategoryID : '',
                    'category_level'    => ( isset( $cat->CategoryLevel ) ) ? '' . $cat->CategoryLevel : '',
                    'category_name'     => ( isset( $cat->CategoryName ) ) ? '' . $cat->CategoryName : '',
                    'parent_id'         => ( isset( $cat->CategoryParentID ) ) ? '' . $cat->CategoryParentID : '',
                ];
            }
            $this->payload = $cats;
        }

        private function set_JSON_payload( &$json )
        {
            if( isset( $json->findItemsAdvancedResponse ) ) {
                $this->list_items_JSON_payload( $json );
            }
        }

        private function list_items_JSON_payload( &$json )
        {
            $raw_items = $json->findItemsAdvancedResponse[ 0 ]->searchResult[ 0 ]->item;
            $items = [];
            foreach( $raw_items as $raw ) {
                $display_name       = ( isset ( $raw->condition[ 0 ]->conditionDisplayName[ 0 ] ) ) ? $raw->condition[ 0 ]->conditionDisplayName[ 0 ] . ' - ' : '';
                $current_price      = number_format( $raw->sellingStatus[ 0 ]->convertedCurrentPrice[ 0 ]->__value__, 2 );
                $title              = $raw->title[ 0 ];
                $price_title        = $display_name . $current_price . ' ' . $raw->sellingStatus[ 0 ]->convertedCurrentPrice[ 0 ]->{'@currencyId'};
                $url                = $raw->viewItemURL[ 0 ];
                $image              = $raw->galleryURL[ 0 ];

                $items[] = [
                    'display-name'              => $display_name,
                    'current-price'             => $current_price,
                    'title'                     => $title,
                    'price-title'               => $price_title,
                    'url'                       => $url,
                    'image'                     => $image,
                ];
            }

            $this->payload = $items;
        }
    }
}