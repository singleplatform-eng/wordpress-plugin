<?php

// SP Plugin options
if ( function_exists( 'delete_option' ) ) {
    // Delete all admin options used for plugin
    $sp_options = array(
        'sp-location-id',
        'sp-primary-background-color',
        'sp-secondary-background-color',
        'sp-tertiary-background-color',
        'sp-primary-font-color',
        'sp-secondary-font-color',
        'sp-tertiary-font-color',
        'sp-font-family',
        'sp-base-font-size',
        'sp-item-casing',
        'sp-display-announcements',
        'sp-display-photos',
        'sp-display-currency-symbol',
        'sp-display-price',
        'sp-display-disclaimer',
        'sp-attribution-image'
    );
    array_walk( $sp_options, 'delete_option' );
}