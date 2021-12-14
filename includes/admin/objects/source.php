<?php
namespace Affiliate_Bridge\Admin\Objects
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    use \Affiliate_Bridge\Debug;
    use \Affiliate_Bridge\Sources\Sources;

    class Source
    {
        public static function form_new()
        {
            $step = ( isset( $_GET[ 'step' ] ) ) ? $_GET[ 'step' ] : '1';

            switch( $step ) {
                case '1':
                default:
                    self::form_new_step_1();
                    break;
            }
        }

        public static function form_edit()
        {
            $step = ( isset( $_GET[ 'step' ] ) ) ? $_GET[ 'step' ] : '1';

            switch( $step ) {
                case '1':
                default:
                    self::form_edit_step_1();
                    break;
            }
        }

        public static function form_cats()
        {
            $step = ( isset( $_GET[ 'step' ] ) ) ? $_GET[ 'step' ] : '1';

            switch( $step ) {
                case '1':
                default:
                    self::form_cats_step_1();
                    break;
            }
        }

        public static function form_delete()
        {
            $step = ( isset( $_GET[ 'step' ] ) ) ? $_GET[ 'step' ] : '1';

            switch( $step ) {
                case '1':
                default:
                    self::form_delete_step_1();
                    break;
            }
        }

        private static function form_header( $title )
        {
            ?>
            <div class="wrap">
                <h2 class="ab-admin-title">Affiliate Bridge :: <?php echo $title; ?></h2>
                <?php settings_errors(); ?>
            <?php
        }

        private static function form_new_step_1()
        {
            $sources = new Sources();
            $source = ( isset( $_POST[ 'source' ] ) ) ? $_POST[ 'source' ] : $_GET[ 'source' ];
            $source = $sources->get_source( $source );

            self::form_header( 'New Source :: ' . $source->get_name() . ' Source Settings' );

            $source->form_new();
        }

        private static function form_edit_step_1()
        {
            $sources = new Sources();
            $id = $_GET[ 'source-id' ];
            $raw_source = $sources->get_source_by_id( $id );
            $source = $sources->get_source( $raw_source->source );
            $source->set( 'id', $raw_source->id );
            $source->set( 'source', $raw_source->source );
            $source->set( 'settings', $raw_source->settings );
            $source->set( 'priority', $raw_source->priority );
            
            self::form_header( 'Edit Source :: ' . $source->get_name() . ' Source Settings' );

            $source->form_edit();
        }

        private static function form_cats_step_1()
        {
            $sources = new Sources();
            $id = $_GET[ 'source-id' ];
            $raw_source = $sources->get_source_by_id( $id );
            $source = $sources->get_source( $raw_source->source );
            $source->set( 'id', $raw_source->id );
            $source->set( 'source', $raw_source->source );
            $source->set( 'settings', $raw_source->settings );
            $source->set( 'priority', $raw_source->priority );
            
            self::form_header( 'Download Categories :: ' . $source->get_name() . ' Categories' );

            $source->form_cats();
        }

        private static function form_delete_step_1()
        {
            $sources = new Sources();
            $id = $_GET[ 'source-id' ];
            $raw_source = $sources->get_source_by_id( $id );
            $source = $sources->get_source( $raw_source->source );
            $source->set( 'id', $raw_source->id );
            $source->set( 'source', $raw_source->source );
            $source->set( 'settings', $raw_source->settings );
            $source->set( 'priority', $raw_source->priority );
            
            self::form_header( 'Delete Source :: ' . $source->get_name() . ' Source Settings' );

            $source->form_delete();
        }
    }
}