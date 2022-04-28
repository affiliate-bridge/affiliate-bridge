<?php
namespace Affiliate_Bridge\Sources
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    abstract class API
    {
        protected $url;

        abstract public function make_call( $args );
    }
}