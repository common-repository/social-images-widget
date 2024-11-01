<?php 
Class Social_Images_Widget extends WP_Widget {
    
    protected $feed;
    
	function __construct() {
		parent::__construct(
			'siw-widget',
			__( 'Instagram', 'social-images-widget' ),
			array(
				'classname' => 'null-instagram-feed', //LyraThemes: change this later when themes are compatible with new class name
				'description' => esc_html__( 'Displays your latest Instagram photos', 'social-images-widget' ),
				'customize_selective_refresh' => true,
			)
		);
        $this->feed = new Social_Images_Widget_Feed( SOCIAL_IMAGES_WIDGET_NAME );
        add_action( 'wp_enqueue_scripts', array( $this, 'scripts' ) );
	}

    /**
	 * Widget specific scripts & styles
	 */
    public function scripts() {
		wp_enqueue_style( 'social-images-widget', plugin_dir_url( dirname( __FILE__ ) . '/social-images-widget.php' ) . 'css/social-images-widget.css');
	}
    
    /**
	 * Frontend display of widget
	 */
	function widget( $args, $instance ) {
        
        $title  = empty( $instance['title'] )  ? ''       : apply_filters( 'widget_title', $instance['title'] );
		$limit  = empty( $instance['number'] ) ? 9        : $instance['number'];
		$grid   = empty( $instance['grid'] )   ? ''       : $instance['grid'];
        $size   = empty( $instance['size'] )   ? 'medium' : $instance['size'];
		$target = empty( $instance['target'] ) ? '_blank' : $instance['target'];

		$media_array = $this->feed->get_feed();
        
		if ( is_wp_error( $media_array ) ) {
            echo wp_kses_post( $media_array->get_error_message() );
		} 
        else if( count($media_array) > 0 ) {	
        
            echo $args['before_widget'];

            if ( ! empty( $title ) ) { echo $args['before_title'] . wp_kses_post( $title ) . $args['after_title']; };
            
            $media_array = array_slice($media_array, 0, $limit);
            
			$ulclass = 'instagram-pics instagram-size-' . $size;
			$liclass = '';
			$aclass = '';
			$imgclass = 'instagram-img-size-' . $size;
      
            if( $grid != '' ){
                $ulclass .= ' instagram-pics-grid instagram-pics-grid--' . $grid;
            }
			?>
            <ul class="<?php echo esc_attr( $ulclass ); ?>">
            <?php
			foreach( $media_array as $item ) {
				$rel = ( $target === '_blank') ? 'noopener' : '';
				echo '<li class="' . esc_attr( $liclass ) . '"><a href="' . esc_url( $item['link'] ) . '" target="' . esc_attr( $target ) . '" rel="' . esc_attr( $rel ) . '"  class="' . esc_attr( $aclass ) . '"><img src="' . esc_url( $item['thumbnail'] ) . '"  alt="' . esc_attr( $item['caption'] ) . '" title="' . esc_attr( $item['caption'] ) . '"  class="' . esc_attr( $imgclass ) . '"/></a></li>';
			}
            ?></ul><?php
            
            echo $args['after_widget'];
		} 
	}

    /**
	 * Backend widget form
	 */
	function form( $instance ) {
    $instance = wp_parse_args( (array) $instance, array(
			'title' => __( 'Instagram', 'social-images-widget' ),
			'size' => 'medium',
			'number' => 9,
			'grid'	=> '',
			'target' => '_blank',
		) );
        
		$title = $instance['title'];
		$number = absint( $instance['number'] );
        $grid = $instance['grid'];
        $size = $instance['size'];
		$target = $instance['target'];
		
        
        if($this->feed->get_username() == '') { ?>
        
        <p><a class="button button-primary" href="<?php echo admin_url( 'options-general.php?page=' . SOCIAL_IMAGES_WIDGET_NAME ) ?>"><?php esc_html_e( 'Authorize Instagram', 'social-images-widget' ); ?></a></p>
        
        <?php } else { 
		?>
        
        <p><label for="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>"><?php esc_html_e( 'Username', 'social-images-widget' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'username' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'username' ) ); ?>" type="text" value="<?php echo esc_attr( $this->feed->get_username() ); ?>" readonly /></label></p>
        
        
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>"><?php esc_html_e( 'Title', 'social-images-widget' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'title' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'title' ) ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" /></label></p>
        
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>"><?php esc_html_e( 'Number of photos', 'social-images-widget' ); ?>: <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( 'number' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'number' ) ); ?>" type="number" value="<?php echo esc_attr( $number ); ?>" /></label></p>
		
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'grid' ) ); ?>"><?php esc_html_e( 'Grid Display', 'social-images-widget' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'grid' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'grid' ) ); ?>" class="widefat">
				
				<option value="" <?php selected( '', $grid ); ?>><?php esc_html_e( 'None', 'social-images-widget' ); ?></option>
				<?php for( $i=2; $i<7; $i++){ ?>
				<option value="<?php echo esc_attr($i); ?>" <?php selected( $i, $grid ); ?>><?php echo esc_html($i); ?> <?php esc_html_e( 'in a row', 'social-images-widget' ); ?></option>
				<?php
					}
				?>
			</select>
		</p>

        <p><label for="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>"><?php esc_html_e( 'Photo size', 'social-images-widget' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'size' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'size' ) ); ?>" class="widefat">
				<option value="medium" <?php selected( 'medium', $size ); ?>><?php esc_html_e( 'Medium', 'social-images-widget' ); ?></option>
				<option value="small" <?php selected( 'small', $size ); ?>><?php esc_html_e( 'Small', 'social-images-widget' ); ?></option>
				<option value="large" <?php selected( 'large', $size ); ?>><?php esc_html_e( 'Large (Full)', 'social-images-widget' ); ?></option>
			</select>
		</p>
        
		<p><label for="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>"><?php esc_html_e( 'Open links in', 'social-images-widget' ); ?>:</label>
			<select id="<?php echo esc_attr( $this->get_field_id( 'target' ) ); ?>" name="<?php echo esc_attr( $this->get_field_name( 'target' ) ); ?>" class="widefat">
				<option value="_blank" <?php selected( '_blank', $target ); ?>><?php esc_html_e( 'New window (_blank)', 'social-images-widget' ); ?></option>
                <option value="_self" <?php selected( '_self', $target ); ?>><?php esc_html_e( 'Current window (_self)', 'social-images-widget' ); ?></option>
			</select>
		</p>
        
		<?php } 

	}
    
    /**
	 * Save widget values
	 */
	function update( $new_instance, $old_instance ) {
        $instance = $old_instance;
		$instance['title'] = strip_tags( $new_instance['title'] );
		$instance['number'] = ! absint( $new_instance['number'] ) ? 9 : $new_instance['number'];
		$instance['grid'] = ! absint( $new_instance['grid'] ) ? '' : $new_instance['grid'];
        $instance['size'] = ( 'medium' === $new_instance['size'] || 'large' === $new_instance['size'] || 'small' === $new_instance['size'] ) ? $new_instance['size'] : 'large' ;
		$instance['target'] = ( ( '_self' === $new_instance['target'] || '_blank' === $new_instance['target'] ) ? $new_instance['target'] : '_blank' );
		return $instance;		
	}

}
