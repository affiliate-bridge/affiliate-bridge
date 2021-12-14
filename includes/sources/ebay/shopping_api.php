<?php
namespace Affiliate_Bridge\Sources\Ebay
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    use \DateTime;
    use \Affiliate_Bridge\Sources\API as BaseAPI;
    use \Affiliate_Bridge\Sources\Ebay\Response;

    class Shopping_API extends BaseAPI
    {
        const URL       = 'https://open.api.ebay.com/shopping';
        const APP_ID    = 'DavidLid-1d4e-4f32-83e5-76489b322689';

        public function make_call( $args )
        {

        }
    }
}