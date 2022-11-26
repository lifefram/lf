<?php
class SiteSearch360Widget extends WP_Widget {
 
    function __construct() {
        parent::__construct('sitesearch360_widget', __('Site Search 360 Search Form', 'site-search-360'),
            array( 'description' => __( 'Site Search 360 search input (search box + search button).', 'wpb_widget_domain' )) 
        );
    }
        
    public function widget( $args, $instance ) {
        $title = apply_filters( 'widget_title', $instance['title'] );
        $label = $instance['searchButtonLabel'];
        $customStyling = isset($instance['customStyling']) ? $instance['customStyling'] : true;

        echo $args['before_widget'];
        if ( ! empty( $title ) ) {
            echo $args['before_title'] . $title . $args['after_title'];
        }
        
		$ss360_integration_type = get_option("ss360_sr_type");
        if ($ss360_integration_type == null) {
            $ss360_integration_type = "full";
        }
        $result = '';
        if($ss360_integration_type != 'full') {
            $result = '<form role="search" method="get" class="ss360-search-form search-form" action="'.esc_url(home_url('/')).'"';
        } else {
            $result = '<section role="search" class="ss360-search-form"';
        }
        if($customStyling) {
            $result = $result . ' data-ss360="true"';
		}
		$placeholder = '';
		if (isset($instance['placeholder']) && !empty($instance['placeholder'])) {
			$placeholder = ' data-ss360-keep-placeholder="true" placeholder="' . $instance['placeholder'] . '"'; 
		}
        $result = $result . ' style="display:-webkit-box;display:-ms-flexbox;display:flex;-webkit-box-orient:horizontal;-webkit-box-direction:normal;-ms-flex-direction:row;flex-direction:row;-webkit-box-align:center;-ms-flex-align:center;align-items:center">';
        $result = $result . '<input class="ss360-searchbox" type="search"'.$placeholder.($customStyling?'':' style="margin-right:8px;"').($ss360_integration_type!='full'?' name="s"':'').'>';
        $result = $result . '<button class="ss360-searchbutton"'.($customStyling?'':' style="margin-right:8px;"').'>'.$label.'</button>';
        if($ss360_integration_type != 'full'){
            $result = $result . '</form>';
        } else {
            $result = $result . '</section>';
        }

        echo $result;

        echo $args['after_widget'];
    }
          
    // Widget Backend 
    public function form( $instance ) {
        if (isset( $instance[ 'title' ])) {
            $title = $instance[ 'title' ];
        } else {
            $title = __( 'Search', 'wpb_widget_domain' );
		}
		if (isset($instance['placeholder'])) {
			$placeholder = $instance['placeholder'];
		} else {
			$placeholder = '';
		}
        if(isset($instance['searchButtonLabel'])) {
            $searchButtonLabel = $instance['searchButtonLabel'];
        } else {
            $searchButtonLabel = 'Search';
        }
        if (isset($instance['customStyling'])) {
            $customStyling = $instance['customStyling'];
        } else {
            $customStyling = true;
        }
         // Widget admin form
        ?>
        <p>
            <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php esc_html_e( 'Title:', 'site-search-360' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />       
			<label for="<?php echo $this->get_field_id( 'placeholder' ); ?>"><?php esc_html_e( 'Placeholder:', 'site-search-360' ); ?></label> 
            <input class="widefat" id="<?php echo $this->get_field_id( 'placeholder' ); ?>" name="<?php echo $this->get_field_name( 'placeholder' ); ?>" type="text" value="<?php echo esc_attr( $placeholder ); ?>" /> 
            <?php if(!$customStyling) { ?>
                <label for="<?php echo $this->get_field_id( 'searchButtonLabel' ); ?>"><?php esc_html_e( 'Search Button Text:', 'site-search-360' ); ?></label>
            <?php } ?>
            <input class="widefat" id="<?php echo $this->get_field_id( 'searchButtonLabel' ); ?>" name="<?php echo $this->get_field_name( 'searchButtonLabel' ); ?>" type="<?php echo $customStyling ? 'hidden' : 'text'?>" value="<?php echo esc_attr( $searchButtonLabel ); ?>" />
            <label for="<?php echo $this->get_field_id('customStyling'); ?>"><?php esc_html_e('Apply styling:', 'site-search-360'); ?></label>
            <input class="widefat" id="<?php echo $this->get_field_id('customStyling'); ?>" name="<?php echo $this->get_field_name( 'customStyling' ); ?>" type="checkbox" <?php echo $customStyling ? 'checked' : '' ?>>
        </p>
        <?php 
    }
      
    // Updating widget replacing old instances with new
    public function update( $new_instance, $old_instance ) {
        $instance = array();
        $instance['title'] = ( ! empty( $new_instance['title'] ) ) ? strip_tags( $new_instance['title'] ) : '';
        $instance['searchButtonLabel'] = ( ! empty( $new_instance['searchButtonLabel'] ) ) ? strip_tags( $new_instance['searchButtonLabel'] ) : '';
		$instance['customStyling'] = (isset($new_instance['customStyling']) && !empty($new_instance['customStyling']));
		$instance['placeholder'] = (isset($new_instance['placeholder']) && !empty($new_instance['placeholder'])) ? strip_tags($new_instance['placeholder']) : '';
        return $instance;
    }
 }
 ?>