<?php
/**
 * Plugin Name:       Social Images Widget
 * Plugin URI:        https://www.lyrathemes.com/instagram-feed-widget
 * Description:       Display your Instagram feed.
 * Version:           2.1
 * Author:            LyraThemes
 * Author URI:        https://www.lyrathemes.com/
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       social-images-widget
 * Domain Path:       /languages/
 */
 
// If this file is called directly, abort
if ( ! defined( 'WPINC' ) ) {
	die;
}

// Define constants
define( 'SOCIAL_IMAGES_WIDGET_VERSION', '2.0' );
define( 'SOCIAL_IMAGES_WIDGET_NAME', 'social-images-widget' );
define( 'SOCIAL_IMAGES_WIDGET_INSTAGRAM_CLIENT_ID', '2881994428593108');
define( 'SOCIAL_IMAGES_WIDGET_INSTAGRAM_REDIRECT_URL', 'https://www.lyrathemes.com/services/instagram/');
define( 'SOCIAL_IMAGES_WIDGET_BASE', plugin_basename( __FILE__ ) );

define( 'SOCIAL_IMAGES_WIDGET_PLUGIN_SUPPORT_URL', 'https://wordpress.org/support/plugin/social-images-widget/');
define( 'SOCIAL_IMAGES_WIDGET_DOCUMENTATION_PAGE_URL', 'https://help.lyrathemes.com/collection/364-instagram-widget');
define( 'SOCIAL_IMAGES_WIDGET_LYRATHEMES_URL', 'https://www.lyrathemes.com?utm_source=social-images-widget&utm_medium=social-images-widget&utm_campaign=social-images-widget');
  
// Includes
require_once( plugin_dir_path( __FILE__ ) . 'class-social-images-widget-feed.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-social-images-widget-settings.php' );
require_once( plugin_dir_path( __FILE__ ) . 'class-social-images-widget.php' );


// Init
function siw_init() {
    // load language files
	load_plugin_textdomain( 'social-images-widget', false, dirname( SOCIAL_IMAGES_WIDGET_BASE ) . '/languages/' );
}
add_action( 'init', 'siw_init' );


// Widget
add_action( 'widgets_init', 'siw_register' );
function siw_register() {
    register_widget( 'Social_Images_Widget' );
}

// Plugin activation

register_activation_hook( __FILE__, 'social_images_widget_plugin_activation' );

function social_images_widget_plugin_activation() {
    if ( ! wp_next_scheduled( 'social_images_widget_plugin_event' ) ) {
        wp_schedule_event( time(), 'daily', 'social_images_widget_plugin_event' );
    }
}
add_action( 'social_images_widget_plugin_event', 'social_images_widget_cron' );
function social_images_widget_cron(){
    //refresh long-lived token
    $images_feed = new Social_Images_Widget_Feed( SOCIAL_IMAGES_WIDGET_NAME );
    $images_feed->refresh_access_token();
}

// Plugin deactivation

register_deactivation_hook( __FILE__, 'social_images_widget_plugin_deactivation' );

function social_images_widget_plugin_deactivation() {
    //remove cron
    wp_clear_scheduled_hook( 'social_images_widget_plugin_event' );
    //remove transients
    $images_feed = new Social_Images_Widget_Feed( SOCIAL_IMAGES_WIDGET_NAME );
    $images_feed->delete_transient();
}


?>