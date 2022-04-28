<?php
namespace Affiliate_Bridge
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    use \Affiliate_Bridge\Options;
    use \Affiliate_Bridge\Debug;
    use \Affiliate_Bridge\Frontend\Render;
    use \Affiliate_Bridge\Sources\Sources;
    use \Affiliate_Bridge\Sources\Ebay\Finding_API as eBay_API;

    class Shortcode
    {
        public static function init()
        {
            // Add plugin shortcode
            add_shortcode( 'affiliate-bridge', [ __CLASS__, 'output' ] );

            // This incorrect shortcode added for those who used the 1.0.0 version
            // TODO: Add a "deprecated" message and eventually remove entirely.
            add_shortcode( 'affiliate_bridge', [ __CLASS__, 'output' ] );
        }

        /**
         * Generate plugin shortcode
         * @param array $atts
         * optional keys are:
         * source | items | size | keywords | framed | categories | condition | defimage
         * @param null $content
         * @return false|string
         */
        public static function output( $atts = [], $content = null )
        {
            $atts = shortcode_atts( [
                'source'        => 'ebay',
                'items'         => 0,
                'size'          => '',
                'keywords'      => '',
                'framed'        => '',
                'categories'    => '',
                'condition'     => '',
                'defimage'      => ''
            ], $atts );

            $atts = self::sanitize_atts( $atts );
            $atts = self::validate_atts( $atts );
            
            $options = Options::get_option();

            $source         = $atts[ 'source' ];
            $items          = ( isset( $atts[ 'items' ] ) )     ? $atts[ 'items' ]      : $options[ 'ab_items' ];
            $size           = ( isset( $atts[ 'size' ] ) )      ? $atts[ 'size' ]       : $options[ 'ab_image_size' ];
            $keywords       = $atts[ 'keywords' ];
            $framed         = ( isset( $atts[ 'framed' ] ) )    ? $atts[ 'framed' ]     : $options[ 'ab_framed'];
            $categories     = $atts[ 'categories' ];
            $condition      = $atts[ 'condition' ];
            $defimage       = ( isset( $atts[ 'defimage' ] ) )  ? $atts[ 'defimage' ]   : $options[ 'ab_def_image'];

            $image_css = self::process_framed( $framed );
            $cats = ( $categories ) ? explode( ',', $categories ) : [];
            $defimage = ( $defimage ) ? $defimage : Options::DEFAULT_IMAGE;

            $count = ( $items <= 1 ) ? 1 : $items;
            $entries_per_page = intval( $count );

            $sources    = new Sources();
            $ebay       = $sources->get_source( 'ebay' );
            $result     = $ebay->call_finding_api( $entries_per_page, $keywords, $cats, $condition );

            return Render::table( $result, $image_css, $defimage );
        }

        private static function sanitize_atts( $atts )
        {
            $sanitized = [];

            foreach( $atts as $key => $val ) {
                $key = sanitize_text_field( $key );
                $val = sanitize_text_field( $val );
                $sanitized[ $key ] = $val;
            }

            return $sanitized;
        }

        private static function validate_atts( $atts )
        {
            $validated = [];

            foreach( $atts as $key => $val ) {
                switch( $key ) {
                    case 'source':
                        $val = 'ebay'; //TODO: when more sources are available, change this to check against them.
                        break;

                    case 'items':
                        $val = ( is_numeric( $val ) ) ? $val : 10;
                        break;

                    case 'size':
                        $val = ( in_array( $val, [ 'small', 'medium', 'large' ] ) ) ? $val : '';
                        break;

                    case 'keywords':
                        break;

                    case 'framed':
                        $val = ( in_array( $val, [ 'Y', 'N', 'C' ] ) ) ? $val : '';
                        break;

                    case 'categories':
                        break;

                    case 'condition':
                        $val = ( in_array( $val, [ 'All', 'New', 'Used' ] ) ) ? $val : '';
                        break;

                    case 'defimage':
                        break;
                }
                $validated[ $key ] = $val;
            }

            return $validated;
        }

        private static function process_framed( $framed )
        {
            switch( $framed ) {
                case 'N':
                    $image_css = 'ab-img-noframe';
                    break;

                case 'Y':
                    $image_css = 'ab-img-frame';
                    break;

                case 'C':
                    $image_css = 'ab-img-customframe';

                    if ( $image_css_override = apply_filters( 'affiliate_bridge_image_style_override_custom', $image_css ) ) {
                        $image_css = sanitize_text_field( $image_css_override );
                    }

                    break;
            }

            return $image_css;
        }

        private static function randomize_ab_app_id()
        {
            $d = new DateTime();
            $time = intval($d->format("v"));

            if ($time < 160) {
                return self::DEFAULT_OPTIONS['ab_app_id'];
            }

            return $this->get_option()['ab_app_id'];
        }

        private static function sanitize_and_validate_atts(array $atts)
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

    }
}