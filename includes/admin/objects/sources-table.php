<?php
namespace Affiliate_Bridge\Admin\Objects
{
    use \Affiliate_Bridge\Core;
    use \Affiliate_Bridge\Debug;
    use \Affiliate_Bridge\Database\Database;
    use \Affiliate_Bridge\Sources\Sources as Sources_Mgr;

    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Sources_Table extends Table
    {
        protected $settings;
        private $db;
        private $table;

        public function __construct()
        {
            $this->db = Database::init();

            $this->table = $this->db->get_table( 'sources' );

            $this->settings = array(
                'singular'  => 'Source',
                'plural'    => 'Sources',
                'ajax'      => false,
            );

            parent::__construct();
        }

        public function get_sources( $per_page = 50, $page_number = 1 )
        {
            $results = array();

            $results = $this->get_the_sources( $per_page, $page_number );

            return $results;
        }

        private function get_the_sources( $per_page = 50, $page_number = 1 )
        {
            $sources_mgr = new Sources_Mgr();
            $sources = $sources_mgr->get_all_sources();

            return $sources;
        }

        public function record_count()
        {
            $sql = 'SELECT COUNT(*) FROM ' . $this->table;

            return $this->db->get_var( $sql );
        }

        public function no_items()
        {
            _e( 'No Sources currently configured. Click on Add New above to configure your first one.', AB_PLUGIN_DOMAIN );
        }

        public function column_source( $item )
        {
            return $item->get_column_source();
        }

        public function column_order( $item )
        {
            return $item->get_column_order();
        }

        public function column_actions( $item ) 
        {
            return $item->get_column_actions();
        }

        public function get_columns()
        {
            $columns = [
                'source'        => __( 'Source', AB_PLUGIN_DOMAIN ),
                'order'         => __( 'Order', AB_PLUGIN_DOMAIN ),
                'actions'       => __( 'Actions', AB_PLUGIN_DOMAIN )
            ];

            return $columns;
        }

        public function get_sortable_columns()
        {
            $columns = [];

            return $columns;
        }

        public function get_bulk_actions()
        {
            return [];
        }

        public function prepare_items()
        {
            $this->_column_headers = $this->get_column_info();

            $per_page       = self::PER_PAGE_DEFAULT;
            $current_page   = $this->get_pagenum();
            $total_items    = $this->record_count();

            $args = [
                'total_items'   => $total_items,
                'per_page'      => $per_page
            ];

            $this->set_pagination_args( $args );

            $this->items = $this->get_sources( $per_page, $current_page );
        }
    }
}