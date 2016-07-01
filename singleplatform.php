<?php
/*

  Plugin Name: SinglePlatform
  Plugin URI: TODO
  Description: TODO
  Version: 1.0.0
  Author: Chris Russell-Walker/SinglePlatform
  Author URI: http://chrisrussellwalker.com
  License: Public Domain
  @since TODO - WORDPRESS VERSION??
*/

// TODO - fill out above ^^^

// TODO - Make this code much cleaner

// TODO - should we employ some autoloading feature of WP?


function myplugin_activate() {

    // TODO - Confirm what we want the page to be called
    $new_page_title = 'Our Menu';
    $new_page_content = '[singleplatform_menu]';

    $page_check = get_page_by_title( $new_page_title );
    $new_page = array(
        'post_type' => 'page',
        'post_title' => $new_page_title,
        'post_content' => $new_page_content,
        'post_status' => 'publish',
        'post_author' => 1,
    );
    if(! isset( $page_check->ID ) ){
        $new_page_id = wp_insert_post($new_page);
        if( isset( $new_page_template ) ) {
            update_post_meta($new_page_id, '_wp_page_template');
        }
    }
}
register_activation_hook( __FILE__, 'myplugin_activate' );


function singleplatform_shortcode() {

    $location_id = get_option( 'sp-location-id' );
    $api_key = get_option( 'sp-api-key', '' );
    if (! $location_id ) {
        return;
    }

    $html = '<div id="menusContainer"></div>';
    $html .= '<script type="text/javascript" src="http://menus.singleplatform.co/businesses/storefront/?apiKey=' . $api_key . '"></script>';
    // TODO - Cleanup
    // TODO - Make these options configurable in Admin Portal
    $html .= "<script>
            var options = {};
            options['PrimaryBackgroundColor'] = '#d9d9d9';
            options['MenuDescBackgroundColor'] = '#d9d9d9';
            options['SectionTitleBackgroundColor'] = '#f1f1f1';
            options['SectionDescBackgroundColor'] = '#f1f1f1';
            options['ItemBackgroundColor'] = '#ffffff';
            options['PrimaryFontFamily'] = 'Roboto';
            options['BaseFontSize'] = '15px';
            options['FontCasing'] = 'Default';
            options['PrimaryFontColor'] = '#000000';
            options['MenuDescFontColor'] = '#000000';
            options['SectionTitleFontColor'] = '#555555';
            options['SectionDescFontColor'] = '#555555';
            options['ItemTitleFontColor'] = '#555555';
            options['FeedbackFontColor'] = '#555555';
            options['ItemDescFontColor'] = '#555555';
            options['ItemPriceFontColor'] = '#555555';
            options['HideDisplayOptionPhotos'] = 'true';
            options['HideDisplayOptionDisclaimer'] = 'true';
            options['MenuTemplate'] = '2';
            //options['MenuDropDownBackgroundColor'] = '#f1f1f1';
            new BusinessView('" . $location_id . "', 'menusContainer', options);
        </script>";

    return $html;
}

add_shortcode( 'singleplatform_menu', 'singleplatform_shortcode' );


add_action( 'admin_menu', function() {

    add_menu_page(
        'SinglePlatform Plugin',
        'SinglePlatform',
        'manage_options',
        'singleplatform-admin',
        'spSettingsPage',
        // TODO - update with real image
        plugins_url( 'singleplatform/images/sp-logo-mark.png' )
    );

    register_setting( 'singleplatform-admin', 'sp-location-id' );

    add_settings_section(
        'sp-section-one',
        '',
        'section_one_callback',
        'sp-plugin'
    );

    add_settings_field(
        'sp-location-id',
        'Location ID',
        'displayLocationId',
        'sp-plugin',
        'sp-section-one'
    );

    add_settings_field(
        'sp-api-key',
        'API Key',
        'displayApiKey',
        'sp-plugin',
        'sp-section-one'
    );
});

function displayLocationId() {

    $location_id = get_option( 'sp-location-id' );

    $html = '<input type="text" id="sp-location-id" name="sp-location-id" size="50"';
    if ( $location_id ) {
        $html .= ' value="' . esc_attr( $location_id ) . '"';
    }
    $html .= '/>';

    echo $html;
}

function displayApiKey() {

    $api_key = get_option( 'sp-api-key' );

    $html = '<input type="text" id="sp-api-key" name="sp-api-key" size="50"';
    if ( $api_key ) {
        $html .= ' value="' . esc_attr( $api_key ) . '"';
    }
    $html .= '/>';

    echo $html;
}

function spSettingsPage() {

    $hook = 'singleplatform-admin';

    echo '<div class="wrap">';
    echo '<header><h1>SinglePlatform Plugin</h1></header>';

    settings_errors( 'general' );

    echo '<form method="post" action="' . esc_url( admin_url( 'options.php' ), array( 'https', 'http' ) ) . '">';

    settings_fields( 'singleplatform-admin' );
    do_settings_sections( 'sp-plugin' );

    submit_button();
    echo '</form>';
    echo '</div>';
}
