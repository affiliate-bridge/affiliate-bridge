<?php
namespace Affiliate_Bridge\Sources
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    use \Affiliate_Bridge\Debug;
    use \Affiliate_Bridge\Database\Database;
    use \Affiliate_Bridge\Sources\Ebay\Source as Ebay;

    class Sources
    {
        private $registered_sources     = null;
        private $active_sources         = null;
        private $inactive_sources       = null;
        
        private $db                     = null;
        private $table                  = null;

        public function __construct()
        {
            $this->db = new Database();
            $this->table = $this->db->get_table( 'sources' );

            $this->register_sources();
            $this->load_sources();
            $this->sort_sources();
        }

        public function get_source( $id )
        {
            return $this->registered_sources[ $id ];
        }

        public function get_all_sources()
        {
            return $this->registered_sources;
        }

        public function get_active_sources()
        {
            return $this->active_sources;
        }

        public function get_inactive_sources()
        {
            return $this->inactive_sources;
        }

        public function get_source_by_id( $id )
        {
            $wheres = [
                0 => [
                    'column'    => 'id',
                    'operator'  => '=',
                    'condition' => $id
                ]
            ];

            $results = $this->db->get( 'sources', $wheres );

            if ( is_array( $results ) && count( $results ) === 1 ) {
                return $results[ 0 ];
            } else {
                return false;
            }
        }

        private function register_sources()
        {
            $ebay           = new Ebay();
            
            $sources = [
                'ebay'          => $ebay,
            ];

            $this->registered_sources = $sources;
        }

        private function load_sources()
        {
            $sources = $this->registered_sources;

            $sql = 'SELECT * FROM ' . $this->table . ' WHERE 1';
            $sql .= ' ORDER BY `priority` DESC';

            $results = $this->db->query( $sql, 'ARRAY_A' );

            $return = [];
            foreach( $results as $result ) {
                $sources[ $result[ 'source' ] ]->set( 'id', $result[ 'id' ] );
                $sources[ $result[ 'source' ] ]->set( 'source', $result[ 'source' ] );
                $sources[ $result[ 'source' ] ]->set( 'settings', $result[ 'settings' ] );
                $sources[ $result[ 'source' ] ]->set( 'priority', $result[ 'priority' ] );
            }

            $this->registered_sources = $sources;
        }

        private function sort_sources()
        {
            foreach( $this->registered_sources as $id => $source ) {
                if ( $source->is_active() ) {
                    $this->active_sources[ $id ] = $source;
                } else {
                    $this->inactive_sources[ $id ] = $source;
                }
            }
        }
    }
}