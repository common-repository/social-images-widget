<?php

/**
 * Plugin admin options / settings
 *
 */
 
class Social_Images_Widget_Settings {
    
    public function __construct( $plugin_name, $version ) {
        
		$this->plugin_name = $plugin_name;
		$this->version = $version;
        
        $oauth_url = 'https://api.instagram.com/oauth/authorize?client_id=' . SOCIAL_IMAGES_WIDGET_INSTAGRAM_CLIENT_ID . '&scope=user_profile,user_media&response_type=code&redirect_uri=' . SOCIAL_IMAGES_WIDGET_INSTAGRAM_REDIRECT_URL;

        $oauth_url .= '&state=' . $this->base64url_encode ( esc_url( admin_url( 'options-general.php?page=' . $this->plugin_name ) ) );

        $this->oauth_url = $oauth_url;
    }
    
    public function enqueue_styles() {
		wp_register_style( $this->plugin_name, plugin_dir_url( __FILE__ ) . 'css/admin/social-images-widget-admin.css', array(), $this->version, 'all' );
        wp_enqueue_style( $this->plugin_name );
	}
    
    public function add_action_links( $links ) {
       $settings_link = array(
        '<a href="' . admin_url( 'options-general.php?page=' . $this->plugin_name ) . '">' . __('Settings', $this->plugin_name) . '</a>',
       );
       return array_merge(  $settings_link, $links );
    }
    
    public function add_plugin_admin_menu() {
        add_options_page( 'LyraThemes Social Images Widget - Settings', 'Instagram Widget', 'manage_options', $this->plugin_name, array($this, 'display_plugin_setup_page'));
    }
    
    public function display_plugin_setup_page() {
        include_once( 'partials/admin/social-images-widget-admin-display.php' );
    }
    
    public function options_update() {
        $var = $this->plugin_name;
        if( isset ( $_POST[$var]['remove'] ) ) { 
            $images_feed = new Social_Images_Widget_Feed( $this->plugin_name );
            $images_feed->delete_transient();
            delete_option( $this->plugin_name );
        }
        else{
	        register_setting($this->plugin_name, $var, array($this, 'validate'));
	    }
	}
    
    public function validate($input) {
        $valid = array();
        $valid['access_token'] = isset($input['access_token']) ? sanitize_text_field($input['access_token']):'';
        $valid['user_id']      = isset($input['user_id'])      ? sanitize_text_field($input['user_id']):'';
        $valid['username']     = isset($input['username'])     ? sanitize_text_field($input['username']):'';
        if( isset($input['remove']) && $input['remove'] == 1 ) { $valid['remove'] = 1; } else { $valid["remove"] = ''; }
        return $valid;
    }
    
    public function run(){
        
        // Add menu item
        add_action( 'admin_menu', array($this, 'add_plugin_admin_menu') );  
        
        // Add Settings link to the plugin
        $plugin_basename = trailingslashit(plugin_basename(__DIR__)) .$this->plugin_name . '.php';
        add_filter( 'plugin_action_links_' . $plugin_basename, array($this, 'add_action_links') );
        
        // Save/Update our plugin options
        add_action( 'admin_init', array($this, 'options_update') );
        
        // Enqueue stylesheet
        add_action( 'admin_enqueue_scripts', array($this, 'enqueue_styles') );
    }
    
    public function base64url_encode($data) {
        return rtrim(strtr(base64_encode($data), '+/', '-_'), '=');
    }

    public function base64url_decode($data) {
        return base64_decode(str_pad(strtr($data, '-_', '+/'), strlen($data) % 4, '=', STR_PAD_RIGHT));
    }
}

$plugin_admin = new Social_Images_Widget_Settings( SOCIAL_IMAGES_WIDGET_NAME, SOCIAL_IMAGES_WIDGET_VERSION );
$plugin_admin->run();
?>