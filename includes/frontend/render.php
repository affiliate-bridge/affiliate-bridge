<?php
namespace Affiliate_Bridge\Frontend
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    use \Affiliate_Bridge\Debug;

    class Render
    {
        protected $type;

        public static function table( $items = null, $img_css = null, $defimg = null )
        {
            $table = '<table class="ab-results-table">' . "\n";
            $table .= "\t" . '<tbody>' . "\n";

            if ( ! is_null( $items ) ) {
                foreach( $items as $item ) {
                    $table .= self::row( $item, $img_css, $defimg );
                }
            } elseif ( is_null( $items ) ) {
                $table .= self::row();
            }

            $table .= "\t" . '</tbody>' . "\n";
            $table .= '</table>' . "\n";

            return $table;
        }

        public static function row( $item = null, $img_css = null, $defimg = null )
        {
            $row = "\t\t" . '<tr>' . "\n";
            
            if ( ! is_null( $item ) ) {
                $class  = ( ! is_null( $img_css ) ) ? $img_css : '';
                $src    = ( isset( $item[ 'image' ] ) ) ? $item[ 'image' ] : $defimage;
                $alt    = $item[ 'title' ] . ' (' . $item[ 'price-title' ] . ')';
                $url    = $item[ 'url' ];
                $title  = $item[ 'title' ];
                $price  = $item[ 'price-title' ];

                $row .= "\t\t\t" . '<td class="ab-results-table-firstcol">' . "\n";
                $row .= "\t\t\t\t" . '<a href="' . $url . '" target="_blank">' . "\n";
                $row .= "\t\t\t\t\t" . '<img src="' . $src . '" class="' . $class . '" alt="' . $alt . '" title="' . $alt . '">' . "\n";
                $row .= "\t\t\t\t" . '</a>' . "\n";
                $row .= "\t\t\t" . '</td>' . "\n";
                $row .= "\t\t\t" . '<td class="ab-results-table-data">' . "\n";
                $row .= "\t\t\t\t" . '<a href="' . $url . '" target="_blank">' . "\n";
                $row .= "\t\t\t\t\t" . $title . '<br />(' . $price . ')' . "\n";
                $row .= "\t\t\t\t" . '</a>' . "\n";
                $row .= "\t\t\t" . '</td>' . "\n";
            } elseif ( is_null( $item ) ) {
                $row .= "\t\t\t" . '<td class="ab-results-table-data">' . "\n";
                $row .= "\t\t\t\t" . 'No results found for this search.' . "\n";
                $row .= "\t\t\t" . '</td>' . "\n";
            }

            $row .= "\t\t" . '</tr>' . "\n";

            return $row;
        }
    }
}
