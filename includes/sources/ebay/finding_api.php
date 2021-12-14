<?php
namespace Affiliate_Bridge\Sources\Ebay
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    use \DateTime;
    use \Affiliate_Bridge\Debug;
    use \Affiliate_Bridge\Sources\API as BaseAPI;
    use \Affiliate_Bridge\Sources\Ebay\Response;

    class Finding_API extends BaseAPI
    {
        const URL = 'https://svcs.ebay.com/services/search/FindingService/v1';

        public function make_call( $params )
        {
            $args = [
                'operation'             => $params[ 'operation' ],
                'version'               => '1.11.0',
                'app-id'                => $params[ 'app-id' ],
                'global-id'             => 'EBAY-US',
                'format'                => 'JSON'
            ];

            $more_args = false;
            if ( isset( $params[ 'more-args' ] ) && count( $params[ 'more-args' ] ) ) {
                $more_args = true;
                foreach( $params[ 'more-args' ] as $key => $val ) {
                    $args[ $key ] = $val;
                }
            }

            $url = self::URL;
            $url .= '?OPERATION-NAME=' . $args[ 'operation' ];
            $url .= '&SERVICE-VERSION=' . $args[ 'version' ];
            $url .= '&SECURITY-APPNAME=' . $args[ 'app-id' ];
            $url .= '&GLOBAL-ID=' . $args[ 'global-id' ];
            $url .= '&RESPONSE-DATA-FORMAT=' . $args[ 'format' ];

            if ( $more_args ) {
                foreach( $args as $key => $val ) {
                    $no = [ 'operation', 'version', 'app-id', 'global-id', 'format' ];
                    if ( ! in_array( $key, $no ) ) {
                        $url .= '&' . $key . '=' . $val;
                    }
                }
            }

            $raw_response = wp_remote_request( $url, ['method' => 'GET'] );
            $body = wp_remote_retrieve_body( $raw_response );

            $response = new Response( $url, $body );
            $response->process();

            return $response->get_payload();
        }
    }

}