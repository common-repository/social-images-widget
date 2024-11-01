<?php

class Social_Images_Widget_Feed {
    
    protected $access_token;
    protected $username;
    protected $user_id;
    
    public function __construct($plugin_name) {
        $options = get_option($plugin_name);
        $this->access_token = $options['access_token'];
        $this->username = $options['username'];
        $this->user_id = $options['user_id'];
        $this->instagram_api_url = 'https://graph.instagram.com/' . $this->user_id . '/media/?fields=caption,id,media_type,media_url,permalink,thumbnail_url,timestamp,username&access_token=' . $this->access_token;
        $this->refresh_access_token_url = 'https://graph.instagram.com/refresh_access_token?grant_type=ig_refresh_token&access_token=';
    }
    
    public function get_access_token(){
        return $this->access_token;
    }
    
    public function get_username(){
        return $this->username;
    }
    
    public function delete_transient( ){
	    delete_transient( 'siw-01-' . sanitize_title_with_dashes( $this->username ) );
    }
    
    public function get_feed(){
        
        if ( empty( $this->access_token ) ) {
            return new WP_Error( 'no_auth', esc_html__( 'Instagram not authorized.', 'social-images-widget' ) );
        }
        
        if ( false === ( $feed = get_transient( 'siw-01-' . sanitize_title_with_dashes( $this->username ) ) ) ) { 

            $url = $this->instagram_api_url;

            $remote = wp_remote_get( $url ); 
            
            if ( is_wp_error( $remote ) ) {
                return new WP_Error( 'site_down', esc_html__( 'Unable to communicate with Instagram.', 'social-images-widget' ) );
            }

            if ( 200 !== wp_remote_retrieve_response_code( $remote ) ) {
                return new WP_Error( 'invalid_response', esc_html__( 'Instagram did not return a 200.', 'social-images-widget' ) );
            }
            
            $data = json_decode( $remote['body'], true ); 
            $data = $data['data']; 
            
            if ( ! $data ) {
                return new WP_Error( 'bad_json', esc_html__( 'Instagram has returned invalid data.', 'social-images-widget' ) );
            }
            
            if ( count($data) > 0 ) {
                
                $feed = array();
                
                foreach($data as $item){
	                $temp['thumbnail'] = $item['media_type'] == 'VIDEO' ? $item['thumbnail_url'] : $item['media_url'];
                    $temp['caption'] = isset($item['caption']) ? $item['caption'] : '';
                    $temp['link'] = $item['permalink'];
                    $temp['created_time'] = $item['timestamp'];
                    $temp['id'] = $item['id'];
                    $feed[] = $temp;
                }
                
                if ( ! empty( $feed ) ) {
                    $feed = base64_encode( serialize( $feed ) );
                    set_transient( 'siw-01-' . sanitize_title_with_dashes( $this->username ), $feed, HOUR_IN_SECONDS * 3 );
                } 
                return unserialize( base64_decode( $feed ) );
                
            } else {
                
                $feed = base64_encode( serialize( array() ) );
                set_transient( 'siw-01-' . sanitize_title_with_dashes( $this->username ), $feed,  MINUTE_IN_SECONDS * 10 ) ;
                    
                return new WP_Error( 'no_images', esc_html__( 'Instagram did not return any images.', 'social-images-widget' ) );
            }
        } 
        else {
            return unserialize( base64_decode( $feed ) );
        }
    }
    
    public function refresh_access_token(){
        if($this->access_token !=''){
            $this->refresh_access_token_url = $this->refresh_access_token_url . $this->access_token;
            $remote = wp_remote_get($this->refresh_access_token_url);
            if ( is_wp_error( $remote ) ) {
                return new WP_Error( 'refresh_access_token_failed', esc_html__( 'Unable to refresh long lived access token.', 'social-images-widget' ) );
            } else {
                //add_option( 'siw-refreshed-token', print_r($remote, true), '', '' ); 
            }
        }
    }
    
}

?>