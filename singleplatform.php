<?php
/*

  Plugin Name: SinglePlatform
  Plugin URI: http://www.singleplatform.com
  Description: Official SinglePlatform plugin for WordPress. Easily display your SinglePlatform menus or listings on your WordPress site.
  Version: 1.0.0
  Author: SinglePlatform
  Author URI: http://www.singleplatform.com
  License: MIT

*/


function singlePlatformActivatePlugin() {

    $new_page_title = 'Menu';
    $new_page_content = '[singleplatform_menu]';

    $page_check = get_page_by_title( $new_page_title );

    if( isset( $page_check->ID ) ) {
        $new_page_title .= ' (powered by SinglePlatform)';
    }
    $new_page = array(
        'post_type' => 'page',
        'post_title' => $new_page_title,
        'post_content' => $new_page_content,
        'post_status' => 'publish',
        'post_author' => 1,
    );

    wp_insert_post( $new_page );
}
register_activation_hook( __FILE__, 'singlePlatformActivatePlugin' );


add_action( 'admin_enqueue_scripts', 'spEnqueueColorPicker' );

function spEnqueueColorPicker() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('js/sp-color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );
}


function singlePlatformShortcode() {

    $location_id = get_option( 'sp-location-id' );
    $api_key = get_option( 'sp-api-key', '' );
    if (! $location_id ) {
        return;
    }

    $hidePhotos = (get_option( 'sp-display-photos', '' ) == 'on') ? "'true'" : "'false'";

    $html = '<div id="menusContainer"></div>';
    $html .= '<script type="text/javascript" src="https://menus.singleplatform.co/businesses/storefront/?apiKey=' . $api_key . '"></script>';

    $html .= "<script>
                var options = {};
                options['PrimaryBackgroundColor'] = '#d9d9d9';
                options['MenuDescBackgroundColor'] = '#d9d9d9';
                options['SectionTitleBackgroundColor'] = '#f1f1f1';
                options['SectionDescBackgroundColor'] = '#f1f1f1';
                options['ItemBackgroundColor'] = '#ffffff';
                options['PrimaryFontFamily'] = '" . get_option( 'sp-primary-font-family', 'Roboto' ) . "';
                options['BaseFontSize'] = '15px';
                options['FontCasing'] = 'Default';
                options['PrimaryFontColor'] = '" . get_option( 'sp-primary-font-color', '#000000' ) . "';
                options['MenuDescFontColor'] = '#000000';
                options['SectionTitleFontColor'] = '#555555';
                options['SectionDescFontColor'] = '#555555';
                options['ItemTitleFontColor'] = '#555555';
                options['FeedbackFontColor'] = '#555555';
                options['ItemDescFontColor'] = '#555555';
                options['ItemPriceFontColor'] = '#555555';";
    $html .= "
                options['HideDisplayOptionPhotos'] = " . $hidePhotos . ";";
    $html .= "
                options['HideDisplayOptionDisclaimer'] = 'true';
                options['MenuTemplate'] = '2';
                options['MenuIframe'] = 'true';
                new BusinessView('" . $location_id . "', 'menusContainer', options);
            </script>";

    return $html;
}

add_shortcode( 'singleplatform_menu', 'singlePlatformShortcode' );


add_action( 'admin_menu', function() {

    add_menu_page(
        'SinglePlatform Plugin',
        'SinglePlatform',
        'manage_options',
        'singleplatform-admin',
        'singlePlatformSettingsPage',
        plugins_url( 'singleplatform/images/sp-logo-mark.png' )
    );

    register_setting( 'singleplatform-admin', 'sp-location-id' );
    register_setting( 'singleplatform-admin', 'sp-primary-font-color' );
    register_setting( 'singleplatform-admin', 'sp-display-photos' );
    register_setting( 'singleplatform-admin', 'sp-primary-font-family' );

    add_settings_section(
        'sp-section-one',
        '',
        '',
        'sp-plugin'
    );

    add_settings_section(
        'sp-font-colors-section',
        'Font colors',
        '',
        'sp-plugin'
    );

    add_settings_section(
        'sp-display-section',
        'Display',
        '',
        'sp-plugin'
    );

    add_settings_section(
        'sp-font-section',
        'Font',
        '',
        'sp-plugin'
    );

    add_settings_field(
        'sp-location-id',
        'Location ID',
        'singlePlatformDisplayLocationId',
        'sp-plugin',
        'sp-section-one'
    );

    add_settings_field(
        'sp-api-key',
        'API Key',
        'singlePlatformDisplayApiKey',
        'sp-plugin',
        'sp-section-one'
    );

    add_settings_field(
        'sp-primary-font-color',
        'Primary Font Color',
        'singlePlatformOptionPrimaryFontColor',
        'sp-plugin',
        'sp-font-colors-section'
    );

    add_settings_field(
        'sp-hide-display-option-photos',
        'Hide Photos',
        'singlePlatformOptionHidePhotos',
        'sp-plugin',
        'sp-display-section'
    );

    add_settings_field(
        'sp-primary-font-family',
        'Primary Font Family',
        'singlePlatformOptionPrimaryFontFamily',
        'sp-plugin',
        'sp-font-section'
    );
});

function singlePlatformDisplayLocationId() {

    $location_id = get_option( 'sp-location-id' );

    $html = '<input type="text" id="sp-location-id" name="sp-location-id" size="30"';
    if ( $location_id ) {
        $html .= ' value="' . esc_attr( $location_id ) . '"';
    }
    $html .= '/>';

    echo $html;
}

function singlePlatformDisplayApiKey() {

    $api_key = get_option( 'sp-api-key' );

    $html = '<input type="text" id="sp-api-key" name="sp-api-key" size="50"';
    if ( $api_key ) {
        $html .= ' value="' . esc_attr( $api_key ) . '"';
    }
    $html .= '/>';

    echo $html;
}

function singlePlatformOptionPrimaryFontColor() {
    $display_primary_font_color = get_option( 'sp-primary-font-color' );

    $html = '<input type="text" class="sp-color-field" id="sp-primary-font-color" name="sp-primary-font-color"';
    if ( $display_primary_font_color ) {
        $html .= ' value="' . esc_attr( $display_primary_font_color ) . '"';
    }
    $html .= '/>';

    echo $html;
}


function singlePlatformOptionHidePhotos() {
    $display_photos = get_option( 'sp-display-photos' );

    $html = '<input type="checkbox" id="sp-display-photos" name="sp-display-photos"';
    if ( $display_photos ) {
        $html .= ' checked';
    }
    $html .= '/>';

    echo $html;
}

function singlePlatformOptionPrimaryFontFamily() {
    $display_primary_font_family = get_option( 'sp-primary-font-family', 'Roboto');
    $options = [
        'Arial',
        'Helvetica Neue',
        'Times New Roman',
        'Georgia',
        'Trebuchet',
        'Sans Serif',
        'Calibri',
        'Helvetica',
        'Verdana',
        'Roboto',
        'Open Sans'
    ];

    $html = '<select id="sp-primary-font-family" name="sp-primary-font-family">';
    foreach ($options as $option) {
        $html .= '<option value="';
        $html .= $option . '"';
        $html .= ($display_primary_font_family == $option) ? ' selected' : '';
        $html .= '>' . $option . '</option>';
    }
    $html .= '<select/>';

    echo $html;
}

function singlePlatformSettingsPage() {

    echo '<header><h1>SinglePlatform Plugin</h1></header>';

    settings_errors( 'general' );

    $options_url = esc_url( admin_url( 'options.php' ), array( 'https', 'http' ) );
    echo '<form method="post" action="' . $options_url . '">';

    settings_fields( 'singleplatform-admin' );
    do_settings_sections( 'sp-plugin' );

    submit_button();
    echo '</form>';
}
