<?php
namespace Affiliate_Bridge
{
    if ( !defined( 'ABSPATH' ) ) {
        exit;
    }

    class Debug
    {
        public static function dump ( $title, $var )
        {
            static $i = 0;
            $from_array = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 2 );
            $from = $from_array[0];
            $from['function'] = $from_array[1]['function'];
            if ( defined( 'AB_DEBUG_ENABLED' ) && AB_DEBUG_ENABLED === true ) {
                echo "<div class='ab-debug'>";
                echo "<strong class='ab-debug-heading'>" . $i++ . ' :: ' . strtoupper( $title ) . "</strong><br />";
                echo "<em class='ab-debug-info'>Called in file: <u>" . str_replace( AB_PLUGIN_DIR, '', $from['file'] ) . "</u> ";
                echo "in function <u>" . $from['function'] . "</u> on line <u>" . $from['line'] . "</u>.</em><br />";
                echo "<pre class='ab-debug-output'>";
                var_dump ( $var );
                echo "</pre><br></div>";
            }
        }

        public static function trace( $title )
        {
            static $i = 0;
            $from_array = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 2 );
            $from = $from_array[0];
            $from['function'] = $from_array[1]['function'];
            if ( defined( 'AB_DEBUG_ENABLED' ) && AB_DEBUG_ENABLED === true ) {
                echo "<div class='ab-debug'>";
                echo "<strong class='ab-debug-heading'>" . $i++ . ' :: BACKTRACE ::' . strtoupper( $title ) . "</strong><br />";
                echo "<em class='ab-debug-info'>Called in file: <u>" . str_replace( AB_PLUGIN_DIR, '', $from['file'] ) . "</u> ";
                echo "in function <u>" . $from['function'] . "</u> on line <u>" . $from['line'] . "</u>.</em><br />";
                echo "<pre class='ab-debug-output'>";
                debug_print_backtrace();
                echo "</pre><br></div>";
            }
        }

        public static function msg( $msg )
        {
            static $i = 0;
            $from_array = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 2 );
            $from = $from_array[0];
            $from['function'] = $from_array[1]['function'];
            if ( defined( 'AB_DEBUG_ENABLED' ) && AB_DEBUG_ENABLED === true ) {
                echo "<div class='ab-debug'>";
                echo "<strong class='ab-debug-heading'>Debug Message #" . $i++ . "</strong><br />";
                echo "<em class='ab-debug-info'>Called in file: <u>" . str_replace( AB_PLUGIN_DIR, '', $from['file'] ) . "</u> ";
                echo "in function <u>" . $from['function'] . "</u> on line <u>" . $from['line'] . "</u>.</em><br />";
                echo "<pre class='ab-debug-output'>";
                echo $msg;
                echo "</pre><br></div>";
            }
        }

        public static function kill( $msg )
        {
            static $i = 0;
            $from_array = debug_backtrace( DEBUG_BACKTRACE_PROVIDE_OBJECT, 2 );
            $from = $from_array[0];
            $from['function'] = $from_array[1]['function'];
            if ( defined( 'AB_DEBUG_ENABLED' ) && AB_DEBUG_ENABLED === true ) {
                echo "<div class='ab-debug'>";
                echo "<strong class='ab-debug-heading'>KILL COMMAND GIVEN</strong><br />";
                echo "<em class='ab-debug-info'>In file: <u>" . str_replace( AB_PLUGIN_DIR, '', $from['file'] ) . "</u> ";
                echo "in function <u>" . $from['function'] . "</u> on line <u>" . $from['line'] . "</u> with message:</em><br />";
                echo "<p>";
                echo $msg;
                echo "</p><br></div>";
            }
            wp_die();
        }
    }

}