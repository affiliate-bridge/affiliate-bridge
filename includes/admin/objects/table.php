<?php
namespace Affiliate_Bridge\Admin\Objects
{
    use \Affiliate_Bridge\Imports\AB_WP_List_Table;
    
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Table extends AB_WP_List_Table
    {
        const PER_PAGE_DEFAULT = 50;

        public function __construct()
        {
            parent::__construct( array(
                'singular'  => $this->settings[ 'singular' ],
                'plural'    => $this->settings[ 'plural' ],
                'ajax'      => $this->settings[ 'ajax' ]
            ) );
        }

        public function get_columns()
        {
            $columns = array();
            return $columns;
        }

        protected function get_sortable_columns()
        {
            $sortable_columns = array();
            return $sortable_columns;
        }

        protected function column_default( $item, $column_name )
        {
            return $item[ $column_name ];
        }

        protected function get_bulk_actions()
        {
            /* No bulk actions */
        }

        public function prepare_items()
        {
            $per_page   = self::PER_PAGE_DEFAULT;

            $columns    = $this->get_columns();
            $hidden     = array();
            $sortable   = $this->get_sortable_columns();

            $this->_column_headers = array( $columns, $hidden, $sortable );

            $data = array();

            $current_page = $this->get_pagenum();

            $total_items = count( $data );

            $data = array_slice( $data, ( ( $current_page - 1 ) + $per_page ), $per_page );

            $this->items = $data;

            $this->set_pagination_args( array(
                'total_items'   => $total_items,
                'per_page'      => $per_page,
                'total_pages'   => ceil( $total_items / $per_page )
            ) );
        }
    }
}