<?php

// SP Plugin options
if ( function_exists( 'delete_option' ) ) {
    // Delete all admin options used for plugin

    // TODO: keep up to date with all options
    $sp_options = array(
        'sp-location-id',
        'sp-api-key'
    );
    array_walk( $sp_options, 'delete_option' );
}