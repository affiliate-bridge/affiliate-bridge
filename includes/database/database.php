<?php
namespace Affiliate_Bridge\Database
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    use \Affiliate_Bridge\Debug;

    class Database
    {
        const SELECT            = 'SELECT * FROM ';
        const INSERT            = 'INSERT INTO ';
        const UPDATE            = 'UPDATE ';
        const DELETE            = 'DELETE FROM ';

        const SOURCE_TABLE      = 'ab_sources';
        const EBAY_CATS_TABLE   = 'ab_ebay_categories';
        const EBAY_AUTH_TABLE   = 'ab_ebay_tokens';
        const ALI_CATS_TABLE    = 'ab_ali_categories';

        private $tables = [];

        public function __construct()
        {
            $this->tables = [
                'sources'       => self::SOURCE_TABLE,
                'ebay_cats'     => self::EBAY_CATS_TABLE,
                'ebay_auth'     => self::EBAY_AUTH_TABLE,
                'ali_cats'      => self::ALI_CATS_TABLE,
            ];
        }

        public static function init()
        {
            $db = new Database();

            if( ! $db->check_for_tables() ) {
                $db->create_tables();
            }

            return $db;
        }

        public function get( $tbl_key, $wheres = null, $orderby = null )
        {
            global $wpdb;

            $table = $this->get_table( $tbl_key );

            /* The basic select statement */
            $sql = self::SELECT . $table . " WHERE 1 ";
            
            /* Add some Where conditions */
            if ( ! empty( $wheres ) ) {
                foreach ( $wheres as $where ) {
                    $sql .= " AND `" . $where['column'] . "` " . $where['operator'] . " '" . $where['condition'] . "' ";
                }
            }

            /* Add the Order By Clause */
            if ( ! is_null( $orderby ) ) {
                $sql .= " ORDER BY $orderby";
            }
            
            /* Get the results from the database */
            return $wpdb->get_results( $sql );
        }

        public function insert( $tbl_key, $data )
        {
            global $wpdb;

            $table = $this->get_table( $tbl_key );

            $data_format = array();
            foreach ( $data as $k => $v ){
                if ( is_float( $v ) ) {
                    $data_format[] = '%f';
                } elseif ( is_int( $v ) ) {
                    $data_format[] = '%d';
                } else {
                    $data_format[] = '%s';
                }
            }
            
            return $wpdb->insert( $table, $data, $data_format );
        }

        public function update( $tbl_key, $where, $data )
        {
            global $wpdb;

            $table = $this->get_table( $tbl_key );

            $data_format = array();
            foreach ( $data as $k => $v ){
                if ( is_float( $v ) ) {
                    $data_format[] = '%f';
                } elseif ( is_int( $v ) ) {
                    $data_format[] = '%d';
                } else {
                    $data_format[] = '%s';
                }
            }

            $where_format = array();
            foreach ( $where as $k => $v ){
                if ( is_float( $v ) ) {
                    $where_format[] = '%f';
                } elseif ( is_int( $v ) ) {
                    $where_format[] = '%d';
                } else {
                    $where_format[] = '%s';
                }
            }

            return $wpdb->update( $table, $data, $where, $data_format, $where_format );
        }

        public function delete( $tbl_key, $where )
        {
            global $wpdb;

            $table = $this->get_table( $tbl_key );

            $where_format = array();
            foreach ( $where as $k => $v ){
                if ( is_float( $v ) ) {
                    $where_format[] = '%f';
                } elseif ( is_int( $v ) ) {
                    $where_format[] = '%d';
                } else {
                    $where_format[] = '%s';
                }
            }

            return $wpdb->delete( $table, $where, $where_format );
        }

        public function check( $tbl_key, $wheres )
        {
            global $wpdb;

            $table = $this->get_table( $tbl_key );

            $results = $this->get( $wheres );
            if( count( $results ) > 0 ) {
                return true;
            } else {
                return false;
            }
        }

        public function get_var( $sql )
        {
            global $wpdb;

            return $wpdb->get_var( $sql );
        }

        public function query( $sql, $return_type = 'OBJECT' )
        {
            global $wpdb;

            return $wpdb->get_results( $sql, $return_type );
        }

        public function get_table( $table )
        {
            global $wpdb;

            return $wpdb->prefix . $this->tables[ $table ];
        }

        public function get_tables()
        {
            global $wpdb;

            $tables = [];
            foreach( $this->tables as $key => $table ) {
                $tables[ $key ] = $wpdb->prefix . $table;
            }

            // echo "<h4>This->Tables</h4><pre>"; var_dump( $this->tables ); echo "</pre>";
            // echo "<h4>Tables</h4><pre>"; var_dump( $tables ); echo "</pre>";

            return $tables;
        }

        private function check_for_tables()
        {
            global $wpdb;

            $tables = $this->get_tables();
            
            foreach( $tables as $key => $table ) {
                if( $wpdb->get_var( "SHOW TABLES LIKE '" . $table . "'" ) != $table ) {
                    return false;
                }
            }

            return true;
        }

        public static function create_tables()
        {
            self::create_table_sources();
            self::create_table_ebay_categories();
            self::create_table_ebay_auth_tokens();
            // self::create_table_ali_categories();
        }

        private static function create_table_sources()
        {
            global $wpdb;

            $table = $wpdb->prefix . self::SOURCE_TABLE;

            if( $wpdb->get_var( "SHOW TABLES LIKE '" . $table . "'" ) != $table ) {
                $sql = "CREATE TABLE " . $table . " (
                    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                    `source` varchar(50) NOT NULL,
                    `settings` text NOT NULL,
                    `priority` mediumint(3) NOT NULL,
                    PRIMARY KEY(`id`)
                );";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
            }
        }

        private static function create_table_ebay_categories()
        {
            global $wpdb;

            $table = $wpdb->prefix . self::EBAY_CATS_TABLE;

            if( $wpdb->get_var( "SHOW TABLES LIKE '" . $table . "'" ) != $table ) {
                $sql = "CREATE TABLE " . $table . " (
                    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                    `category_id` varchar(10) NOT NULL,
                    `category_level` int(5) NOT NULL,
                    `category_name` varchar(50) NOT NULL,
                    `parent_id` varchar(10) NOT NULL,
                    `version` varchar(25) NOT NULL,
                    PRIMARY KEY(`id`)
                );";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
            }
        }

        private static function create_table_ebay_auth_tokens()
        {
            global $wpdb;

            $table = $wpdb->prefix . self::EBAY_AUTH_TABLE;

            if( $wpdb->get_var( "SHOW TABLES LIKE '" . $table . "'" ) != $table ) {
                $sql = "CREATE TABLE " . $table . " (
                    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                    `session_id` varchar(40) NOT NULL,
                    `auth_token` text NOT NULL,
                    `date_created` datetime NOT NULL,
                    `date_expires` datetime NOT NULL,
                    PRIMARY KEY(`id`)
                );";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
            }
        }

        private static function create_table_ali_categories()
        {
            global $wpdb;

            $table = $wpdb->prefix . self::ALI_CATS_TABLE;

            if( $wpdb->get_var( "SHOW TABLES LIKE '" . $table . "'" ) != $table ) {
                $sql = "CREATE TABLE " . $table . " (
                    `id` mediumint(9) NOT NULL AUTO_INCREMENT,
                    `category_id` varchar(10) NOT NULL,
                    `category_level` int(5) NOT NULL,
                    `category_name` varchar(50) NOT NULL,
                    `parent_id` varchar(10) NOT NULL,
                    `version` varchar(25) NOT NULL,
                    PRIMARY KEY(`id`)
                );";

                require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
                dbDelta( $sql );
            }
        }

        public static function upgrade_tables()
        {
            /* Nothing to do here for now */
        }

        public static function delete_tables()
        {
            self::delete_table_sources();
            self::delete_table_ebay_categories();
            self::delete_table_ebay_auth_tokens();
        }

        private static function delete_table_sources()
        {
            global $wpdb;

            $table = $wpdb->prefix . self::SOURCE_TABLE;

            $wpdb->query( "DROP TABLE IF EXISTS " . $table );
        }

        private static function delete_table_ebay_categories()
        {
            global $wpdb;

            $table = $wpdb->prefix . self::EBAY_CATS_TABLE;

            $wpdb->query( "DROP TABLE IF EXISTS " . $table );
        }

        private static function delete_table_ebay_auth_tokens()
        {
            global $wpdb;

            $table = $wpdb->prefix . self::EBAY_AUTH_TABLE;

            $wpdb->query( "DROP TABLE IF EXISTS " . $table );
        }
    }
}