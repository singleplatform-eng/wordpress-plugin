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


add_action( 'admin_enqueue_scripts', 'singlePlatformEnqueueAdminAssets' );

function singlePlatformEnqueueAdminAssets() {
    wp_enqueue_style( 'wp-color-picker' );
    wp_enqueue_script( 'my-script-handle', plugins_url('js/sp-color-picker.js', __FILE__ ), array( 'wp-color-picker' ), false, true );

    wp_register_style( 'sp-admin', plugins_url('css/sp-admin.css', __FILE__) );
    wp_enqueue_style( 'sp-admin' );
}

function singleplatformGetDisplayOption($id) {
    return (get_option($id, '') == 'on') ? "'false'" : "'true'";
}

function singlePlatformShortcode() {

    $location_id = get_option( 'sp-location-id' );
    $api_key = get_option( 'sp-api-key', '' );
    if (!$location_id) {
        return;
    }

    $hide_photos = singleplatformGetDisplayOption('sp-display-photos');
    $hide_announcements = singleplatformGetDisplayOption('sp-display-announcements');
    $hide_currency_symbol = singleplatformGetDisplayOption('sp-display-currency-symbol');
    $hide_price = singleplatformGetDisplayOption('sp-display-price');
    $hide_disclaimer = singleplatformGetDisplayOption('sp-display-disclaimer');
    $hide_feedback_widget = singleplatformGetDisplayOption('sp-feedback-widget');
    $hide_attribution_image = singleplatformGetDisplayOption('sp-attribution-image');

    $html = '<div id="menusContainer"></div>';
    $html .= '<script type="text/javascript" src="https://menus.singleplatform.co/businesses/storefront/?apiKey=' . $api_key . '"></script>';

    $html .= "<script>
                var options = {};
                options['PrimaryBackgroundColor'] = '" . get_option('sp-primary-background-color', '#d9d9d9') . "';
                options['MenuDescBackgroundColor'] = '" . get_option('sp-primary-background-color', '#d9d9d9') . "';
                options['SectionTitleBackgroundColor'] = '" . get_option('sp-secondary-background-color', '#f1f1f1') . "';
                options['SectionDescBackgroundColor'] = '" . get_option('sp-secondary-background-color', '#f1f1f1') . "';
                options['ItemBackgroundColor'] = '" . get_option('sp-tertiary-background-color', '#ffffff') . "';
                options['PrimaryFontFamily'] = '" . get_option('sp-font-family', 'Roboto') . "';
                options['BaseFontSize'] = '" . get_option('sp-base-font-size', '15px') . "';
                options['FontCasing'] = '" . get_option('sp-item-casing', 'Default') . "';
                options['PrimaryFontColor'] = '" . get_option('sp-primary-font-color', '#000000') . "';
                options['MenuDescFontColor'] = '" . get_option('sp-primary-font-color', '#000000') . "';
                options['SectionTitleFontColor'] = '" . get_option('sp-secondary-font-color', '#555555') . "';
                options['SectionDescFontColor'] = '" . get_option('sp-secondary-font-color', '#555555') . "';
                options['ItemTitleFontColor'] = '" . get_option('sp-tertiary-font-color', '#555555') . "';
                options['FeedbackFontColor'] = '" . get_option('sp-tertiary-font-color', '#555555') . "';
                options['ItemDescFontColor'] = '" . get_option('sp-tertiary-font-color', '#555555') . "';
                options['ItemPriceFontColor'] = '" . get_option('sp-tertiary-font-color', '#555555') . "';";
    $html .= "
                options['HideDisplayOptionAnnouncements'] = " . $hide_announcements . ";
                options['HideDisplayOptionPhotos'] = " . $hide_photos . ";
                options['HideDisplayOptionDollarSign'] = " . $hide_currency_symbol . ";
                options['HideDisplayOptionPrice'] = " . $hide_price . ";
                options['HideDisplayOptionDisclaimer'] = " . $hide_disclaimer . ";
                options['HideDisplayOptionFeedback'] = " . $hide_feedback_widget . ";
                options['HideDisplayOptionAttribution'] = " . $hide_attribution_image . ";";
    $html .= "
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

    register_setting( 'singleplatform-admin', 'sp-primary-background-color' );
    register_setting( 'singleplatform-admin', 'sp-secondary-background-color' );
    register_setting( 'singleplatform-admin', 'sp-tertiary-background-color' );

    register_setting( 'singleplatform-admin', 'sp-primary-font-color' );
    register_setting( 'singleplatform-admin', 'sp-secondary-font-color' );
    register_setting( 'singleplatform-admin', 'sp-tertiary-font-color' );

    register_setting( 'singleplatform-admin', 'sp-font-family' );
    register_setting( 'singleplatform-admin', 'sp-base-font-size' );
    register_setting( 'singleplatform-admin', 'sp-item-casing' );

    register_setting( 'singleplatform-admin', 'sp-display-announcements' );
    register_setting( 'singleplatform-admin', 'sp-display-photos' );
    register_setting( 'singleplatform-admin', 'sp-display-currency-symbol' );
    register_setting( 'singleplatform-admin', 'sp-display-price' );
    register_setting( 'singleplatform-admin', 'sp-display-disclaimer' );
    register_setting( 'singleplatform-admin', 'sp-feedback-widget' );
    register_setting( 'singleplatform-admin', 'sp-attribution-image' );

    add_settings_section(
        'sp-section-one',
        'Setup',
        '',
        'sp-plugin'
    );

    add_settings_section(
        'sp-background-colors-section',
        'Background colors',
        '',
        'sp-plugin'
    );

    add_settings_section(
        'sp-font-section',
        'Font',
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
        'sp-advanced-section',
        'Advanced',
        '',
        'sp-plugin'
    );

    /* Setup Fields */
    add_settings_field(
        'sp-location-id',
        'Location ID',
        'singlePlatformDisplayLocationId',
        'sp-plugin',
        'sp-section-one'
    );

    /* Background Color Fields */
    add_settings_field(
        'sp-primary-background-color',
        'Primary color',
        'singleplatformOptionPrimaryBgColor',
        'sp-plugin',
        'sp-background-colors-section'
    );

    add_settings_field(
        'sp-secondary-background-color',
        'Secondary color',
        'singleplatformOptionSecondaryBgColor',
        'sp-plugin',
        'sp-background-colors-section'
    );

    add_settings_field(
        'sp-tertiary-background-color',
        'Tertiary color',
        'singleplatformOptionTertiaryBgColor',
        'sp-plugin',
        'sp-background-colors-section'
    );

    /* Font Fields */
    add_settings_field(
        'sp-font-family',
        'Font Family',
        'singlePlatformOptionFontFamily',
        'sp-plugin',
        'sp-font-section'
    );

    add_settings_field(
        'sp-base-font-size',
        'Base font size',
        'singleplatformOptionFontSize',
        'sp-plugin',
        'sp-font-section'
    );

    add_settings_field(
        'sp-item-casing',
        'Item casing',
        'singleplatformOptionItemCasing',
        'sp-plugin',
        'sp-font-section'
    );

    /* Font Color Fields */
    add_settings_field(
        'sp-primary-font-color',
        'Primary Font Color',
        'singlePlatformOptionPrimaryFontColor',
        'sp-plugin',
        'sp-font-colors-section'
    );

    add_settings_field(
        'sp-secondary-font-color',
        'Secondary Font Color',
        'singlePlatformOptionSecondaryFontColor',
        'sp-plugin',
        'sp-font-colors-section'
    );

    add_settings_field(
        'sp-tertiary-font-color',
        'Tertiary Font Color',
        'singlePlatformOptionTertiaryFontColor',
        'sp-plugin',
        'sp-font-colors-section'
    );

    /* Display Fields */
    add_settings_field(
        'sp-display-announcements',
        'Announcements',
        'singleplatformOptionDisplayAnnouncements',
        'sp-plugin',
        'sp-display-section'
    );

    add_settings_field(
        'sp-hide-display-option-photos',
        'Photos tab',
        'singlePlatformOptionHidePhotos',
        'sp-plugin',
        'sp-display-section'
    );

    add_settings_field(
        'sp-display-currency-symbol',
        'Currency symbol (e.g. $ / €)',
        'singleplatformOptionDisplayCurrencySymbol',
        'sp-plugin',
        'sp-display-section'
    );

    add_settings_field(
        'sp-display-price',
        'Price',
        'singleplatformOptionDisplayPrice',
        'sp-plugin',
        'sp-display-section'
    );

    add_settings_field(
        'sp-display-disclaimer',
        'Disclaimer',
        'singleplatformOptionDisplayDisclaimer',
        'sp-plugin',
        'sp-display-section'
    );

    add_settings_field(
        'sp-feedback-widget',
        'Feedback widget',
        'singleplatformOptionFeedbackWidget',
        'sp-plugin',
        'sp-display-section'
    );

    add_settings_field(
        'sp-attribution-image',
        'Attribution image',
        'singleplatformOptionAttributionImage',
        'sp-plugin',
        'sp-display-section'
    );

    /* Advanced Fields */
    add_settings_field(
        'sp-api-key',
        'API Key',
        'singlePlatformDisplayApiKey',
        'sp-plugin',
        'sp-advanced-section'
    );
});

function singlePlatformSettingsPage() {

    echo '<div id="singleplatform-container">';
    echo '<header><h1>SinglePlatform Settings</h1></header>';

    echo '<div class="sp-settings-container">';
    settings_errors( 'general' );

    $options_url = esc_url( admin_url( 'options.php' ), array( 'https', 'http' ) );
    echo '<form method="post" action="' . $options_url . '">';

    settings_fields( 'singleplatform-admin' );
    do_settings_sections( 'sp-plugin' );

    submit_button();
    echo '</form>';
    echo '</div>';
    echo '</div>';
}

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

/*
 * Background colors
 */
function singleplatformOptionPrimaryBgColor() {
    $display_primary_bg_color = get_option('sp-primary-background-color', '#d9d9d9');

    $html = '<input type="text" class="sp-color-field" id="sp-primary-background-color" name="sp-primary-background-color"';
    if ($display_primary_bg_color) {
        $html .= ' value="' . esc_attr($display_primary_bg_color) . '"';
    }
    $html .= ' />';

    echo $html;
}

function singleplatformOptionSecondaryBgColor() {
    $display_secondary_bg_color = get_option('sp-secondary-background-color', '#f1f1f1');

    $html = '<input type="text" class="sp-color-field" id="sp-secondary-background-color" name="sp-secondary-background-color"';
    if ($display_secondary_bg_color) {
        $html .= ' value="' . esc_attr($display_secondary_bg_color) . '"';
    }

    $html .= ' />';

    echo $html;
}

function singleplatformOptionTertiaryBgColor() {
    $display_tertiary_bg_color = get_option('sp-tertiary-background-color', '#ffffff');

    $html = '<input type="text" class="sp-color-field" id="sp-tertiary-background-color" name="sp-tertiary-background-color"';
    if ($display_tertiary_bg_color) {
        $html .= ' value="' . esc_attr($display_tertiary_bg_color) . '"';
    }

    $html .= ' />';

    echo $html;
}

/*
 * Fonts
 */
function singlePlatformOptionFontFamily() {
    $display_font_family = get_option('sp-font-family', 'Roboto');
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

    $html = '<select id="sp-font-family" name="sp-font-family">';
    foreach ($options as $option) {
        $html .= '<option value="';
        $html .= $option . '"';
        $html .= ($display_font_family == $option) ? ' selected' : '';
        $html .= '>' . $option . '</option>';
    }
    $html .= '</select>';

    echo $html;
}

function singleplatformOptionFontSize() {
    $display_font_size = get_option('sp-base-font-size', '15px');

    $options = [
        '10px',
        '11px',
        '12px',
        '14px',
        '15px',
        '16px'
    ];

    $html = '<select id="sp-base-font-size" name="sp-base-font-size">';
    foreach ($options as $option) {
        $html .= '<option value="' . $option . '"';
        $html .= ($display_font_size == $option) ? ' selected' : '';
        $html .= '>' . $option . '</option>';
    }
    $html .= '</select>';

    echo $html;
}

function singleplatformOptionItemCasing() {
    $display_item_casing = get_option('sp-item-casing', 'Default');

    $options = [
        'Default',
        'Lowercase',
        'Uppercase'
    ];

    $html = '<select id="sp-item-casing" name="sp-item-casing">';
    foreach ($options as $option) {
        $html .= '<option value="' . $option . '"';
        $html .= ($display_item_casing == $option) ? ' selected' : '';
        $html .= '>' . $option . '</option>';
    }
    $html .= '</select>';

    echo $html;
}

/*
 * Font Colors
 */
function singlePlatformOptionPrimaryFontColor() {
    $display_primary_font_color = get_option('sp-primary-font-color', '#000000');

    $html = '<input type="text" class="sp-color-field" id="sp-primary-font-color" name="sp-primary-font-color"';
    if ( $display_primary_font_color ) {
        $html .= ' value="' . esc_attr( $display_primary_font_color ) . '"';
    }
    $html .= '/>';

    echo $html;
}

function singlePlatformOptionSecondaryFontColor() {
    $display_secondary_font_color = get_option('sp-secondary-font-color', '#555555');

    $html = '<input type="text" class="sp-color-field" id="sp-secondary-font-color" name="sp-secondary-font-color"';
    if ( $display_secondary_font_color ) {
        $html .= ' value="' . esc_attr( $display_secondary_font_color ) . '"';
    }
    $html .= '/>';

    echo $html;
}

function singlePlatformOptionTertiaryFontColor() {
    $display_tertiary_font_color = get_option('sp-tertiary-font-color', '#555555');

    $html = '<input type="text" class="sp-color-field" id="sp-tertiary-font-color" name="sp-tertiary-font-color"';
    if ( $display_tertiary_font_color ) {
        $html .= ' value="' . esc_attr( $display_tertiary_font_color ) . '"';
    }
    $html .= '/>';

    echo $html;
}

/*
 * Display Options
 */
function singleplatformOptionDisplayAnnouncements() {
    $display_announcements = get_option('sp-display-announcements', true);

    $html = '<input type="checkbox" id="sp-display-announcements" name="sp-display-announcements"';
    if ($display_announcements) {
        $html .= ' checked';
    }
    $html .= ' />';

    echo $html;
}

function singlePlatformOptionHidePhotos() {
    $display_photos = get_option('sp-display-photos', false);

    $html = '<input type="checkbox" id="sp-display-photos" name="sp-display-photos"';
    if ( $display_photos ) {
        $html .= ' checked';
    }
    $html .= ' />';

    echo $html;
}

function singleplatformOptionDisplayCurrencySymbol() {
    $display_currency_symbol = get_option('sp-display-currency-symbol', true);

    $html = '<input type="checkbox" id="sp-display-currency-symbol" name="sp-display-currency-symbol"';
    if ( $display_currency_symbol ) {
        $html .= ' checked';
    }
    $html .= ' />';

    echo $html;
}

function singleplatformOptionDisplayPrice() {
    $display_price = get_option('sp-display-price', true);

    $html = '<input type="checkbox" id="sp-display-price" name="sp-display-price"';
    if ( $display_price ) {
        $html .= ' checked';
    }
    $html .= ' />';

    echo $html;
}

function singleplatformOptionDisplayDisclaimer() {
    $display_disclaimer = get_option('sp-display-disclaimer', false);

    $html = '<input type="checkbox" id="sp-display-disclaimer" name="sp-display-disclaimer"';
    if ( $display_disclaimer ) {
        $html .= ' checked';
    }
    $html .= ' />';

    echo $html;
}

function singleplatformOptionFeedbackWidget() {
    $display_feedback_widget = get_option('sp-feedback-widget', true);

    $html = '<input type="checkbox" id="sp-feedback-widget" name="sp-feedback-widget"';
    if ( $display_feedback_widget ) {
        $html .= ' checked';
    }
    $html .= ' />';

    echo $html;
}

function singleplatformOptionAttributionImage() {
    $display_attribution_image = get_option('sp-attribution-image', true);

    $html = '<input type="checkbox" id="sp-attribution-image" name="sp-attribution-image"';
    if ( $display_attribution_image ) {
        $html .= ' checked';
    }
    $html .= ' />';

    echo $html;
}
