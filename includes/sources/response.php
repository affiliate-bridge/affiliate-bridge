<?php
namespace Affiliate_Bridge\Sources
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    abstract class Response
    {
        protected $raw;

        abstract public function __construct( $call, $raw );

        abstract public function is_error();

        abstract public function is_XML();

        abstract public function is_JSON();

        abstract public function process();

        abstract public function __toString();
    }
}