<?php
namespace Affiliate_Bridge\Sources\Ebay
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    use \DateTime;
    use \Affiliate_Bridge\Debug;
    use \Affiliate_Bridge\Database\Database;
    use \Affiliate_Bridge\Sources\API as BaseAPI;
    use \Affiliate_Bridge\Sources\Ebay\Response;

    class Trading_API extends BaseAPI
    {
        const URL               = 'https://api.ebay.com/ws/api.dll';
        const RUNAME            = 'Mattheu_Farmer-MattheuF-Affili-arieixhfb';
        const DEV_ID            = '8d80b9f5-25e8-4410-84de-aeda3ab028aa';
        const APP_ID            = 'MattheuF-Affiliat-PRD-3a5965275-92bf9fc2';
        const CERT_ID           = 'PRD-a5965275a9cf-acee-4187-a21d-5cd6';
        const VERSION           = '1209';
        const SITE_ID           = '0';

        private $session_id     = null;     // The Session ID for the user
        private $token          = null;     // User Token
        private $call           = null;     // The action being requested of the API
        private $auth_expires   = null;

        private $db             = null;

        public function __construct()
        {
            $this->db = Database::init();
        }

        public function randomize_app_id()
        {
            $date = new DateTime();
            $time = intval( $date->format( 'v' ) );

            if ( $time < 160 ) {
                return self::APP_ID;
            } else {
                // TODO: Return other app-id here
                return self::APP_ID;
            }
        }

        public function get_session_id()
        {
            $date = new DateTime();

            $wheres = [
                0 => [
                    'column'    => 'date_expires',
                    'operator'  => '>',
                    'condition' => $date->format( 'Y-m-d H:i:s' )
                ]
            ];

            $results = $this->db->get( 'ebay_auth', $wheres );

            if ( is_array( $results ) && count( $results ) ) {
                if ( isset( $results[ 0 ]->session_id ) && $results[ 0 ]->session_id !== '' ) {
                    $this->session_id = $results[ 0 ]->session_id;
                }
            }

            if ( is_null( $this->session_id ) ) {
                $raw = $this->make_call( 'get_session_id' );
                $this->session_id = $raw->get_payload();
                $this->save_session_id();
            }

            return $this->session_id;
        }

        public function get_token()
        {
            if ( is_null( $this->session_id ) ) {
                $this->get_session_id();
            }

            $now = new DateTime();

            $wheres = [
                0 => [
                    'column'    => 'session_id',
                    'operator'  => '=',
                    'condition' => $this->session_id
                ],
                1 => [
                    'column'    => 'date_expires',
                    'operator'  => '>',
                    'condition' => $now->format( 'Y-m-d H:i:s' )
                ],
            ];

            $results = $this->db->get( 'ebay_auth', $wheres );

            if ( is_array( $results ) && count( $results ) ) {
                if ( isset( $results[ 0 ]->auth_token ) && $results[ 0 ]->auth_token !== '' ) {
                    $this->token = $results[ 0 ]->auth_token;
                    $this->auth_expires = $results[ 0 ]->date_expires;
                    $this->save_token();
                }
            }

            if ( is_null( $this->token ) ) {
                $raw = $this->make_call( 'fetch_token' );
                $this->token = $raw->get_payload();

                $exp = new DateTime( '+1 Year' );
                $this->auth_expires = $exp->format( 'Y-m-d H:i:s' );
                $this->save_token();
            }

            return $this->token;
        }

        public function get_auth_expire()
        {
            return $this->auth_expires;
        }

        public function get_categories()
        {
            return $this->get_category_list();
        }

        public function make_call( $call )
        {
            $this->call = $call;

            $body = $this->build_body();
            $headers = $this->build_headers();

            $connection = curl_init();
            // Set url being called
            curl_setopt( $connection, CURLOPT_URL, self::URL );
            
            // Keep CURL from verifying the peer's certificate
            curl_setopt( $connection, CURLOPT_SSL_VERIFYPEER, 0 );
            curl_setopt( $connection, CURLOPT_SSL_VERIFYHOST, 0 );
            
            // Set headers
            curl_setopt( $connection, CURLOPT_HTTPHEADER, $headers );
            
            // Use POST
            curl_setopt( $connection, CURLOPT_POST, 1 );
            
            // Set request's body
            curl_setopt( $connection, CURLOPT_POSTFIELDS, $body );
            
            // Return a string
            curl_setopt( $connection, CURLOPT_RETURNTRANSFER, 1 );
            
            // Send Request
            $response = new Response( $this->call, curl_exec( $connection ) );
            
            // Close connection
            curl_close( $connection );

            $response->process();

            return $response;
        }

        private function build_body()
        {
            if( $this->call === 'get_session_id' ) {
                $body = '<?xml version="1.0" encoding="utf-8" ?>';
                $body .= '<GetSessionIDRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
                $body .= '<RuName>' . self::RUNAME . '</RuName>';
                $body .= '</GetSessionIDRequest>';
            } elseif( $this->call === 'fetch_token' ) {
                $body = '<?xml version="1.0" encoding="utf-8" ?>';
                $body .= '<FetchTokenRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
                $body .= '<SessionID>' . $this->get_session_id() . '</SessionID>';
                $body .= '<Version>' . self::VERSION . '</Version>';
                $body .= '</FetchTokenRequest>';
            } elseif( $this->call === 'get_ebay_category_list_version' ) {
                $body = '<?xml version="1.0" encoding="utf-8" ?>';
                $body .= '<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
                $body .= '<RequesterCredentials><eBayAuthToken>' . $this->get_token() . '</eBayAuthToken></RequesterCredentials>';
                $body .= '<Version>' . self::VERSION . '</Version>';
                $body .= '<ViewAllNodes>true</ViewAllNodes>';
                $body .= '</GetCategoriesRequest>';
            } elseif( $this->call === 'get_ebay_category_list' ) {
                $body = '<?xml version="1.0" encoding="utf-8" ?>';
                $body .= '<GetCategoriesRequest xmlns="urn:ebay:apis:eBLBaseComponents">';
                $body .= '<RequesterCredentials><eBayAuthToken>' . $this->get_token() . '</eBayAuthToken></RequesterCredentials>';
                $body .= '<Version>' . self::VERSION . '</Version>';
                $body .= '<ViewAllNodes>true</ViewAllNodes>';
                $body .= '<DetailLevel>ReturnAll</DetailLevel>';
                $body .= '</GetCategoriesRequest>';
            }

            return $body;
        }

        private function build_headers()
        {
            return [
                'X-EBAY-API-COMPATIBILITY-LEVEL: ' . self::VERSION,
                'X-EBAY-API-DEV-NAME: ' . self::DEV_ID,
                'X-EBAY-API-APP-NAME: ' . self::APP_ID,
                'X-EBAY-API-CERT-NAME: ' . self::CERT_ID,
                'X-EBAY-API-CALL-NAME: ' . $this->build_call(),
                'X-EBAY-API-SITEID: ' . self::SITE_ID
            ];
        }

        private function build_call()
        {
            switch( $this->call ) {
                case 'get_session_id':
                    $call = 'GetSessionID';
                    break;
                case 'fetch_token':
                    $call = 'FetchToken';
                    break;
                case 'get_ebay_category_list_version':
                case 'get_ebay_category_list':
                    $call = 'GetCategories';
                    break;
            }

            return $call;
        }

        private function get_category_list_version()
        {
            $wheres = [
                0 => [
                    'column'    => 'id',
                    'operator'  => '=',
                    'condition' => '0'
                ]
            ];

            $results = $this->db->get( 'ebay_cats', $wheres );

            if ( is_array( $results ) && isset( $results[ 0 ][ 'version' ] ) ) {
                return $results[ 0 ][ 'version' ];
            } else {
                return false;
            }
        }

        private function get_category_list()
        {
            $wheres = [
                0 => [
                    'column'    => 'id',
                    'operator'  => '>',
                    'condition' => '1'
                ]
            ];

            $results = $this->db->get( 'ebay_cats', $wheres, '`category_level` ASC, `category_name` ASC' );

            if ( is_array( $results ) ) {
                return $results;
            } else {
                return false;
            }
        }

        private function get_ebay_category_list_version()
        {
            return $this->make_call( 'get_ebay_category_list_version' );
        }

        private function get_ebay_category_list()
        {
            return $this->make_call( 'get_ebay_category_list' );
        }

        private function save_session_id()
        {
            $now = new DateTime();
            $exp = new DateTime( '+1 Year' );

            $this->db->insert( 
                'ebay_auth', 
                [
                    'session_id'        => $this->session_id,
                    'auth_token'        => '',
                    'date_created'      => $now->format( 'Y-m-d H:i:s' ),
                    'date_expires'      => $exp->format( 'Y-m-d H:i:s' )
                ] 
            );
        }

        private function save_token()
        {
            $this->db->update( 
                'ebay_auth', 
                [ 'session_id'    => $this->session_id ], 
                [ 'auth_token'    => $this->token ] 
            );
        }

        private function save_category_list( $version, $list )
        {
            
        }
    }
}