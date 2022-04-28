<?php
namespace Affiliate_Bridge\Admin\Pages
{
    use \Affiliate_Bridge\Debug;
    use \Affiliate_Bridge\Admin\Objects\Source;
    use \Affiliate_Bridge\Admin\Objects\Sources_Table;
    use \Affiliate_Bridge\Sources\Sources as BaseSources;

    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class SourceList
    {
        public function __construct()
        {
            add_action( 'admin_menu', [ $this, 'init_table' ] );
        }

        public static function init()
        {
            return new SourceList();
        }

        public function init_table()
        {
            $this->sources_list = new Sources_Table();
        }

        public function display()
        {
            $action = ( isset( $_GET[ 'action' ] ) ) ? $_GET[ 'action' ] : '';

            switch( $action ) {
                case 'new':
                    $this->new();
                    break;
                case 'edit':
                    $this->edit();
                    break;
                case 'cats':
                    $this->cats();
                    break;
                case 'delete':
                    $this->delete();
                    break;
                default:
                    $this->list();
                    break;
            }
        }

        private function list()
        {
            if ( isset( $_POST[ 'action' ] ) ) {
                $this->handle_action();
            }

            ?>
            <div class="wrap">
                <h2 class="ab-admin-title">
                    Affiliate Bridge :: Source Settings
                    <!--<a href="<?php //echo admin_url('admin.php?page=affiliate-bridge-sources&action=new' ); ?>" class="add-new-h2">Add New Source</a> -->
                </h2>
                <?php settings_errors(); ?>
                <form method="post">
                    <?php 
                    wp_nonce_field( 'affiliate-bridge-save-source-order', 'affiliate-bridge-nonce' );
                    $this->sources_list->prepare_items();
                    $this->sources_list->display();
                    submit_button();
                    ?>
                </form>
            <?php
        }

        private function new()
        {
            Source::form_new();
        }

        private function edit()
        {
            Source::form_edit();
        }

        private function cats()
        {
            Source::form_cats();
        }

        private function delete()
        {
            Source::form_delete();
        }

        private function handle_action()
        {
            $action     = $_POST[ 'action' ];
            $step       = $_POST[ 'step' ];
            $source     = $_POST[ 'source' ];
            
            $sources    = new BaseSources();
            $src        = $sources->get_source( $source );

            if ( $action == 'new' && $step == '2' ) {
                $src->save_as_new_source();
            } elseif ( $action == 'edit' && $step == '2' ) {
                $src->save_existing_source();
            } elseif ( $action == 'cats' && $step == '2' ) {
                $src->download_categories();
            } elseif ( $action == 'delete' ) {
                $src->maybe_delete();
            }
        }
    }
}