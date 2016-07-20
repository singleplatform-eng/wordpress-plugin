<?php

// SP Plugin options
if ( function_exists( 'delete_option' ) ) {
    // Delete all admin options used for plugin
    $sp_options = array(
        'sp-location-id',
        'sp-api-key',
        'sp-primary-font-color',
        'sp-display-photos',
        'sp-primary-font-family'
    );
    array_walk( $sp_options, 'delete_option' );
}