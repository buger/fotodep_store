<?php
/**
 * Admin Settings API used by Shipping Methods and Payment Gateways
 *
 * @class 		woocommerce
 * @package		WooCommerce
 * @category	Class
 * @author		WooThemes
 */
class woocommerce_settings_api {
	
	var $plugin_id = 'woocommerce_';
	var $settings = array();
	var $form_fields = array();
	var $errors = array();
	var $sanitized_fields = array();
	
	/**
	 * Admin Options
	 *
	 * Setup the gateway settings screen.
	 * Override this in your gateway.
	 *
	 * @since 1.0.0
	 */
	function admin_options() {}
	
	/**
	 * Initialise Settings Form Fields
	 *
	 * Add an array of fields to be displayed
	 * on the gateway's settings screen.
	 *
	 * @since 1.0.0
	 */
	function init_form_fields () { return __( 'This function needs to be overridden by your payment gateway class.', 'woothemes' ); }
	
	/**
	 * Admin Panel Options Processing
	 * - Saves the options to the DB
	 *
	 * @since 1.0.0
	 */
    public function process_admin_options() {
    	$this->validate_settings_fields();
    	
    	if ( count( $this->errors ) > 0 ) {
    		$this->display_errors();
    	} else {
    		update_option( $this->plugin_id . $this->id . '_settings', $this->sanitized_fields );
    	}
    }
    
    /**
     * Display admin error messages.
     *
     * @since 1.0.0
     */
    function display_errors() {} // End display_errors()
	
	/**
     * Initialise Gateway Settings
     *
     * Store all settings in a single database entry
     * and make sure the $settings array is either the default
     * or the settings stored in the database.
     *
     * @since 1.0.0
     * @uses get_option(), add_option()
     */
    function init_settings () {
    	if ( ! is_array( $this->settings ) ) { return; }

    	$settings = array();
    	$existing_settings = get_option( $this->plugin_id . $this->id . '_settings' );

    	if ( ! $existing_settings ) {
    	
	    	// Get defaults
	    	$defaults = array();
	    	
	    	foreach ( $this->form_fields as $k => $v ) {
	    		
	    		// Backwards compatibility
	    		if ( $value = get_option( $this->plugin_id . $this->id . '_' . $k ) ) :
	    			$defaults[$k] = $value;
	    		else :
	    		
		    		if ( isset( $v['default'] ) ) {
		    			$defaults[$k] = $v['default'];
		    		} else {
		    			$defaults[$k] = '';
		    		}
	    		
	    		endif;
	    		
	    	}
    	
    		$existing_settings = $defaults;
    		
    	} else {
    		// Prevent "undefined index" errors.
    		foreach ( $existing_settings as $k => $v ) {
    			if ( ! isset( $existing_settings[$k] ) ) {
    				$existing_settings[$k] = $v;
    			}  
    		}
    	}
    	
    	$this->settings = $existing_settings;
    	
    	if ( isset( $this->settings['enabled'] ) && ( $this->settings['enabled'] == 'yes' ) ) { $this->enabled = 'yes'; }
    } // End init_settings()
    
    /**
     * Generate Settings HTML.
     *
     * Generate the HTML for the fields on the "settings" screen.
     *
     * @since 1.0.0
     * @uses method_exists()
     */
    function generate_settings_html () {
    	$html = '';
    	foreach ( $this->form_fields as $k => $v ) {
    		if ( ! isset( $v['type'] ) || ( $v['type'] == '' ) ) { $v['type'] == 'text'; } // Default to "text" field type.
    		
    		if ( method_exists( $this, 'generate_' . $v['type'] . '_html' ) ) {
    			$html .= $this->{'generate_' . $v['type'] . '_html'}( $k, $v );
    		}
    	}
    	
    	echo $html;
    } // End generate_settings_html()
    
    /**
     * Generate Text Input HTML.
     *
     * @since 1.0.0
     * @return $html string
     */
    function generate_text_html ( $key, $data ) {
    	$html = '';
    	
    	if ( isset( $data['title'] ) && $data['title'] != '' ) { $title = $data['title']; }
    	$data['class'] = (isset( $data['class'] )) ? $data['class'] : '';
    	$data['css'] = (isset( $data['css'] )) ? $data['css'] : '';
    	
		$html .= '<tr valign="top">' . "\n";
			$html .= '<th scope="row" class="titledesc">' . $title . '</th>' . "\n";
			$html .= '<td class="forminp">' . "\n";
				$html .= '<fieldset><legend class="screen-reader-text"><span>' . $title . '</span></legend>' . "\n";
				$html .= '<label for="' . $this->plugin_id . $this->id . '_' . $key . '">';
				$html .= '<input class="input-text wide-input '.$data['class'].'" type="text" name="' . $this->plugin_id . $this->id . '_' . $key . '" id="' . $this->plugin_id . $this->id . '_' . $key . '" style="'.$data['css'].'" value="' . esc_attr($this->settings[$key]) . '" />';
				if ( isset( $data['description'] ) && $data['description'] != '' ) { $html .= '<span class="description">' . $data['description'] . '</span>' . "\n"; }
			$html .= '</fieldset>';
			$html .= '</td>' . "\n";
		$html .= '</tr>' . "\n";
    	
    	return $html;
    } // End generate_text_html()

    /**
     * Generate Password Input HTML.
     *
     * @since 1.0.0
     * @return $html string
     */
    function generate_password_html ( $key, $data ) {
    	$html = '';
    	
    	if ( isset( $data['title'] ) && $data['title'] != '' ) { $title = $data['title']; }
    	$data['class'] = (isset( $data['class'] )) ? $data['class'] : '';
    	$data['css'] = (isset( $data['css'] )) ? $data['css'] : '';
    	
		$html .= '<tr valign="top">' . "\n";
			$html .= '<th scope="row" class="titledesc">' . $title . '</th>' . "\n";
			$html .= '<td class="forminp">' . "\n";
				$html .= '<fieldset><legend class="screen-reader-text"><span>' . $title . '</span></legend>' . "\n";
				$html .= '<label for="' . $this->plugin_id . $this->id . '_' . $key . '">';
				$html .= '<input class="input-text wide-input '.$data['class'].'" type="password" name="' . $this->plugin_id . $this->id . '_' . $key . '" id="' . $this->plugin_id . $this->id . '_' . $key . '" style="'.$data['css'].'" value="' . esc_attr($this->settings[$key]) . '" />';
				if ( isset( $data['description'] ) && $data['description'] != '' ) { $html .= '<span class="description">' . $data['description'] . '</span>' . "\n"; }
			$html .= '</fieldset>';
			$html .= '</td>' . "\n";
		$html .= '</tr>' . "\n";
    	
    	return $html;
    } // End generate_password_html()
    
    /**
     * Generate Textarea HTML.
     *
     * @since 1.0.0
     * @return $html string
     */
    function generate_textarea_html( $key, $data ) {
    	$html = '';
    	
    	if ( isset( $data['title'] ) && $data['title'] != '' ) { $title = $data['title']; }
    	$data['class'] = (isset( $data['class'] )) ? $data['class'] : '';
    	$data['css'] = (isset( $data['css'] )) ? $data['css'] : '';
    	
		$html .= '<tr valign="top">' . "\n";
			$html .= '<th scope="row" class="titledesc">' . $title . '</th>' . "\n";
			$html .= '<td class="forminp">' . "\n";
				$html .= '<fieldset><legend class="screen-reader-text"><span>' . $title . '</span></legend>' . "\n";
				$html .= '<label for="' . $this->plugin_id . $this->id . '_' . $key . '">';
				$html .= '<textarea rows="3" cols="20" class="input-text wide-input '.$data['class'].'" name="' . $this->plugin_id . $this->id . '_' . $key . '" id="' . $this->plugin_id . $this->id . '_' . $key . '" style="'.$data['css'].'">'.esc_attr($this->settings[$key]).'</textarea>';
				if ( isset( $data['description'] ) && $data['description'] != '' ) { $html .= '<span class="description">' . $data['description'] . '</span>' . "\n"; }
			$html .= '</fieldset>';
			$html .= '</td>' . "\n";
		$html .= '</tr>' . "\n";
    	
    	return $html;
    } // End generate_textarea_html()
         
    /**
     * Generate Checkbox HTML.
     *
     * @since 1.0.0
     * @return $html string
     */
    function generate_checkbox_html ( $key, $data ) {
    	$html = '';
    	
    	if ( isset( $data['title'] ) && $data['title'] != '' ) $title = $data['title'];
    	if ( isset( $data['label'] ) && $data['label'] != '' ) $label = $data['label']; else $label = $data['title'];
    	$data['class'] = (isset( $data['class'] )) ? $data['class'] : '';
    	$data['css'] = (isset( $data['css'] )) ? $data['css'] : '';
    	
		$html .= '<tr valign="top">' . "\n";
			$html .= '<th scope="row" class="titledesc">' . $title . '</th>' . "\n";
			$html .= '<td class="forminp">' . "\n";
				$html .= '<fieldset><legend class="screen-reader-text"><span>' . $title . '</span></legend>' . "\n";
				$html .= '<label for="' . $this->plugin_id . $this->id . '_' . $key . '">';
				$html .= '<input style="'.$data['css'].'" name="' . $this->plugin_id . $this->id . '_' . $key . '" id="' . $this->plugin_id . $this->id . '_' . $key . '" type="checkbox" value="1" ' . checked( $this->settings[$key], 'yes', false ) . ' class="'.$data['class'].'" /> ' . $label . '</label><br />' . "\n";
				if ( isset( $data['description'] ) && $data['description'] != '' ) { $html .= '<span class="description">' . $data['description'] . '</span>' . "\n"; }
			$html .= '</fieldset>';
			$html .= '</td>' . "\n";
		$html .= '</tr>' . "\n";
    	
    	return $html;
    } // End generate_checkbox_html()

    /**
     * Generate Select HTML.
     *
     * @since 1.0.0
     * @return $html string
     */
    function generate_select_html ( $key, $data ) {
    	$html = '';
    	
    	if ( isset( $data['title'] ) && $data['title'] != '' ) { $title = $data['title']; }
    	$data['options'] = (isset( $data['options'] )) ? (array) $data['options'] : array();
    	$data['class'] = (isset( $data['class'] )) ? $data['class'] : '';
    	$data['css'] = (isset( $data['css'] )) ? $data['css'] : '';
    	
		$html .= '<tr valign="top">' . "\n";
			$html .= '<th scope="row" class="titledesc">' . $title . '</th>' . "\n";
			$html .= '<td class="forminp">' . "\n";
				$html .= '<fieldset><legend class="screen-reader-text"><span>' . $title . '</span></legend>' . "\n";
				$html .= '<label for="' . $this->plugin_id . $this->id . '_' . $key . '">';
				$html .= '<select name="' . $this->plugin_id . $this->id . '_' . $key . '" id="' . $this->plugin_id . $this->id . '_' . $key . '" style="'.$data['css'].'" class="select '.$data['class'].'">';
				
				foreach ($data['options'] as $option_key => $option_value) :
					$html .= '<option value="'.$option_key.'" '.selected($option_key, esc_attr($this->settings[$key]), false).'>'.$option_value.'</option>';
				endforeach;

				$html .= '</select>';
				if ( isset( $data['description'] ) && $data['description'] != '' ) { $html .= '<span class="description">' . $data['description'] . '</span>' . "\n"; }
			$html .= '</fieldset>';
			$html .= '</td>' . "\n";
		$html .= '</tr>' . "\n";
    	
    	return $html;
    } // End generate_select_html()
    
    /**
     * Generate Multiselect HTML.
     *
     * @since 1.0.0
     * @return $html string
     */
    function generate_multiselect_html ( $key, $data ) {
    	$html = '';
    	
    	if ( isset( $data['title'] ) && $data['title'] != '' ) { $title = $data['title']; }
    	$data['options'] = (isset( $data['options'] )) ? (array) $data['options'] : array();
    	$data['class'] = (isset( $data['class'] )) ? $data['class'] : '';
    	$data['css'] = (isset( $data['css'] )) ? $data['css'] : '';
    	
		$html .= '<tr valign="top">' . "\n";
			$html .= '<th scope="row" class="titledesc">' . $title . '</th>' . "\n";
			$html .= '<td class="forminp">' . "\n";
				$html .= '<fieldset><legend class="screen-reader-text"><span>' . $title . '</span></legend>' . "\n";
				$html .= '<label for="' . $this->plugin_id . $this->id . '_' . $key . '">';
				$html .= '<select multiple="multiple" style="'.$data['css'].'" class="multiselect '.$data['class'].'" name="' . $this->plugin_id . $this->id . '_' . $key . '[]" id="' . $this->plugin_id . $this->id . '_' . $key . '">';
				
				foreach ($data['options'] as $option_key => $option_value) :
					$html .= '<option value="'.$option_key.'" ';
					if (isset($this->settings[$key]) && in_array($option_key, (array) $this->settings[$key])) $html .= 'selected="selected"';
					$html .= '>'.$option_value.'</option>';
				endforeach;

				$html .= '</select>';
				if ( isset( $data['description'] ) && $data['description'] != '' ) { $html .= '<span class="description">' . $data['description'] . '</span>' . "\n"; }
			$html .= '</fieldset>';
			$html .= '</td>' . "\n";
		$html .= '</tr>' . "\n";
    	
    	return $html;
    } // End generate_select_html()
    
    /**
     * Validate Settings Field Data.
     *
     * Validate the data on the "Settings" form.
     *
     * @since 1.0.0
     * @uses method_exists()
     */
    function validate_settings_fields () {
    	foreach ( $this->form_fields as $k => $v ) {
    		if ( ! isset( $v['type'] ) || ( $v['type'] == '' ) ) { $v['type'] == 'text'; } // Default to "text" field type.
    		
    		if ( method_exists( $this, 'validate_' . $v['type'] . '_field' ) ) {
    			$field = $this->{'validate_' . $v['type'] . '_field'}( $k );
    			$this->sanitized_fields[$k] = $field;
    		} else {
    			$this->sanitized_fields[$k] = $this->settings[$k];
    		}
    	}
    } // End validate_settings_fields()
    
    /**
     * Validate Checkbox Field.
     *
     * If not set, return "no", otherwise return "yes".
     * 
     * @since 1.0.0
     * @return $status string
     */
    function validate_checkbox_field ( $key ) {
    	$status = 'no';
    	if ( isset( $_POST[$this->plugin_id . $this->id . '_' . $key] ) && ( 1 == $_POST[$this->plugin_id . $this->id . '_' . $key] ) ) {
    		$status = 'yes';
    	}
    	
    	return $status;
    } // End validate_checkbox_field()    
    
    /**
     * Validate Text Field.
     *
     * Make sure the data is escaped correctly, etc.
     * 
     * @since 1.0.0
     * @return $text string
     */
    function validate_text_field ( $key ) {
    	$text = (isset($this->settings[$key])) ? $this->settings[$key] : '';
    	
    	if ( isset( $_POST[$this->plugin_id . $this->id . '_' . $key] ) ) {
    		$text = esc_attr( woocommerce_clean( $_POST[$this->plugin_id . $this->id . '_' . $key] ) );
    	}
    	
    	return $text;
    } // End validate_text_field()

    /**
     * Validate Password Field.
     *
     * Make sure the data is escaped correctly, etc.
     * 
     * @since 1.0.0
     * @return $text string
     */
    function validate_password_field ( $key ) {
    	$text = (isset($this->settings[$key])) ? $this->settings[$key] : '';
    	
    	if ( isset( $_POST[$this->plugin_id . $this->id . '_' . $key] ) ) {
    		$text = esc_attr( woocommerce_clean( $_POST[$this->plugin_id . $this->id . '_' . $key] ) );
    	}
    	
    	return $text;
    } // End validate_password_field()
    
    
    /**
     * Validate Textarea Field.
     *
     * Make sure the data is escaped correctly, etc.
     * 
     * @since 1.0.0
     * @return $text string
     */
    function validate_textarea_field ( $key ) {
    	$text = (isset($this->settings[$key])) ? $this->settings[$key] : '';
    	
    	if ( isset( $_POST[$this->plugin_id . $this->id . '_' . $key] ) ) {
    		$text = esc_attr( woocommerce_clean( $_POST[$this->plugin_id . $this->id . '_' . $key] ) );
    	}
    	
    	return $text;
    } // End validate_textarea_field()
    
    /**
     * Validate Select Field.
     *
     * Make sure the data is escaped correctly, etc.
     * 
     * @since 1.0.0
     * @return $text string
     */
    function validate_select_field ( $key ) {
    	$value = (isset($this->settings[$key])) ? $this->settings[$key] : '';
    	
    	if ( isset( $_POST[$this->plugin_id . $this->id . '_' . $key] ) ) {
    		$value = esc_attr( woocommerce_clean( $_POST[$this->plugin_id . $this->id . '_' . $key] ) );
    	}
    	
    	return $value;
    } // End validate_select_field()

    /**
     * Validate Multiselect Field.
     *
     * Make sure the data is escaped correctly, etc.
     * 
     * @since 1.0.0
     * @return $text string
     */
    function validate_multiselect_field ( $key ) {
    	$value = (isset($this->settings[$key])) ? $this->settings[$key] : '';
    	
    	if ( isset( $_POST[$this->plugin_id . $this->id . '_' . $key] ) ) {
    		$value = array_map('esc_attr', array_map('woocommerce_clean', (array) $_POST[$this->plugin_id . $this->id . '_' . $key] ));
    	}
    	
    	return $value;
    } // End validate_select_field()
    
}