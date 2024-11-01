<?php
$options = get_option($this->plugin_name);
$access_token = '';
if($options){
    $access_token = $options['access_token'];
    $user_id = $options['user_id'];
    $username = $options['username'];
}

$show_authorize_instagram = true;
$show_instagram_user = false;
$show_save_instagram = false;
$show_remove_instagram_user = false;

$siw_access_token = isset($_GET['siw_access_token']) ? $_GET['siw_access_token'] : '';
if($siw_access_token != '') { $show_authorize_instagram = false; $show_save_instagram = true; }
if($access_token != '') { $show_authorize_instagram = false; $show_instagram_user = true; $show_save_instagram = false;}
if($siw_access_token == '' && $access_token != '') $show_remove_instagram_user = true;
?>

<div class="wrap">

    <h2><?php echo esc_html(get_admin_page_title()); ?></h2>
    
    <?php if($show_authorize_instagram) { ?>
    
        <h3><?php esc_html_e( 'Authorize Instagram', 'social-images-widget' ); ?></h3>
            
        <p><?php esc_html_e( 'In order to use this widget to show your Instagram images on your website, you need to authorize access to your Instagram account.', 'social-images-widget' ); ?><br /><?php esc_html_e( 'Click the button below and authorize the "LyraThemes Social Images Widget" application.', 'social-images-widget' ); ?></p>
        
        <a class="button button-primary" href="<?php echo esc_url($this->oauth_url); ?>"><?php esc_html_e( 'Connect Your Instagram Account', 'social-images-widget' ); ?></a>
    
    <?php } ?>
    
    
    <?php if($show_save_instagram) { ?>
    
        <h3><?php esc_html_e( 'Save Instagram Account', 'social-images-widget' ); ?></h3>
        
        <p><?php esc_html_e( 'Thanks for authorizing your Instagram account.', 'social-images-widget' ); ?><br /><?php esc_html_e( 'To finalize the connection, please go ahead and save these settings by clicking the "Save Instagram User" button below.', 'social-images-widget' ); ?></p>
        
        <form method="post" action="options.php">
        
            <?php settings_fields($this->plugin_name); ?>
            
            <p><strong><?php esc_html_e( 'Instagram Username: ', 'social-images-widget' ); ?><?php echo esc_html($_GET['siw_username']); ?></strong></p>
            
            <input type="hidden" id="<?php echo $this->plugin_name; ?>-siw_username" name="<?php echo $this->plugin_name; ?>[username]" value="<?php echo esc_html($_GET['siw_username']); ?>"/>
            <input type="hidden" id="<?php echo $this->plugin_name; ?>-siw_access_token" name="<?php echo $this->plugin_name; ?>[access_token]" value="<?php echo esc_html($_GET['siw_access_token']); ?>"/>
            <input type="hidden" id="<?php echo $this->plugin_name; ?>-siw_user_id" name="<?php echo $this->plugin_name; ?>[user_id]" value="<?php echo esc_html($_GET['siw_user_id']); ?>"/>
           
            <?php submit_button(__('Save Instagram User', 'social-images-widget'), 'primary','submit', TRUE); ?>
        
        </form>
    
    <?php } ?>
    
    
    <?php if($show_instagram_user) { ?>
    
        <h3><?php esc_html_e( 'Instagram Account Connected', 'social-images-widget' ); ?></h3>
        
        <p><?php esc_html_e( 'Your Instagram account is connected.', 'social-images-widget' ); ?></p>

        <p><strong><?php esc_html_e( 'Instagram Username: ', 'social-images-widget' ); ?><?php echo esc_html($username); ?></strong> &#x2705</p>

        <p><?php echo sprintf( wp_kses( __( 'You can now go to <a href="%s">Appearance > Widgets</a> to add the Instagram widget on your website.', 'social-images-widget' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( admin_url('widgets.php') ) ); ?></p>
        
        <br /><br />
        
        <?php if($show_remove_instagram_user) { ?>
        
        <form method="post" action="<?php echo admin_url( 'options-general.php' ); ?>?page=social-images-widget">
        
            <hr />
            <p><br/><?php esc_html_e( 'If you would like to remove this account or connect a new one, please click "Remove Instagram User" below.', 'social-images-widget' ); ?></p>
            <?php settings_fields($this->plugin_name); ?>
        
            <input type="hidden" id="<?php echo $this->plugin_name; ?>-remove" name="<?php echo $this->plugin_name; ?>[remove]" value="1"/>
            <?php submit_button( __('Remove Instagram User', 'social-images-widget'), 'primary','submit', TRUE ); ?>
            
            <p><?php echo sprintf( wp_kses( __( 'To completely remove access for the <em>"LyraThemes Social Images Widget"</em> application, you will need to remove it from your <a href="%s" target="_blank">Authorized Applications</a> in your Instagram account.', 'social-images-widget' ), array(  'a' => array( 'href' => array() ) ) ), esc_url( 'https://www.instagram.com/accounts/manage_access/'  ) ); ?></p>
            
        </form>
        
        <?php } ?>
    
    <?php } ?>

</div>

<div class="wrap">
    <hr />
    <h3><?php esc_html_e( 'Support and Documentation', 'social-images-widget'); ?></h3>
        
        <p><?php echo sprintf( __( '&#x1F60A In order to reach out to the plugin authors for any questions or assistance, click <a href="%s" target="_blank">here</a>.', 'social-images-widget' ), esc_url( SOCIAL_IMAGES_WIDGET_PLUGIN_SUPPORT_URL ) ); ?></p>
        
        <p><?php echo sprintf( __( '&#x1F6E0 For usage instructions and details, click <a href="%s" target="_blank">here</a>.', 'social-images-widget' ), esc_url( SOCIAL_IMAGES_WIDGET_DOCUMENTATION_PAGE_URL ) ); ?></p>
        
        <p><?php echo sprintf( __( '&#x2B50 Check out free and premium themes from LyraThemes with built-in support for the Social Images Widget plugin <a href="%s" target="_blank">here</a>.', 'social-images-widget' ), esc_url( SOCIAL_IMAGES_WIDGET_LYRATHEMES_URL ) ); ?></p>
    <h3>
</div>
