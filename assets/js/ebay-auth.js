jQuery(document).ready(function($){
    var ebay_auth_window = null;
    $( '#ebay-auth-btn' ).click( function( e ) 
    {
        var url      = $( this ).attr( 'href');
        var target   = $( this ).attr( 'target');
        e.preventDefault();
        if ( ebay_auth_window == null || ebay_auth_window.closed ) {
            ebay_auth_window = window.open( url, target );
        } else {
            ebay_auth_window.focus();
        }

        var ebay_timer = setInterval( function( ee ) 
        {
            if ( ebay_auth_window.closed ) {
                clearInterval( ebay_timer );
                $( '#ebay-auth-btn' ).removeClass( 'button-primary' );
                $( '#ebay-auth-btn' ).html( 'Re-Authorize' );
                $( '#submit' ).removeAttr( 'disabled' );
            }
        }, 1000);
    });
});