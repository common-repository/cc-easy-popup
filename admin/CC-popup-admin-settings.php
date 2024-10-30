<?php
require_once( CPAS_POPUP_PATH . 'admin/CC-popup-admin-callbacks.php' );

class CCNewsletterAdminSettings extends Callbacks {
    
    private $slug = 'CC_popup_admin_settings';
    public function __construct() {
        add_action( 'admin_menu', array( $this, 'add_plugin_page' ) );
        add_action( 'admin_init', array( $this, 'page_init' ) );
		add_action( 'wp_ajax_CPAS_ajax_update_settings', array( $this, 'CPAS_ajax_update_settings' ) );
		add_action( 'wp_ajax_CPAS_ajax_getInputOptions', array( $this, 'CPAS_ajax_getInputOptions' ) );
		add_action( 'wp_ajax_CPAS_ajax_getAllPosts', array( $this, 'CPAS_ajax_getAllPosts' ) );
    }
	function CPAS_ajax_getAllPosts(){
		$data['result'] = 0;
		$data['html'] = '';
		if ($_SERVER['REQUEST_METHOD'] == 'POST' && $_POST['value'] == 'selected_posts'){
			$args = array(
			  'post_type' => array('post','page'),
			  'post_status' => 'publish',
			  'posts_per_page' => -1,
			  'ignore_sticky_posts' => true,
			);
			
			$qry = new WP_Query($args);
			$html  = '<tr class="dyn_row">';
			$html .= '<th scope="row">Posts &amp; Pages *</th><td>';
			$html .= '<select name="CC_newsletter_options[CC_popup_selected_posts]" aria-describedby="CC_popup_selected_posts-description" id="CC_popup_selected_posts" data-validation-rule="required"><option value="">-- SELECT --</option>';

			$postGroup = '<optgroup label="Posts">';
			$pageGroup = '<optgroup label="Pages">';
			$postOpt = '';
			$pageOpt = '';
			$options = get_option('CC_newsletter_options');
			$selected_post = isset($options['CC_popup_selected_posts']) ? $options['CC_popup_selected_posts'] : '';
			foreach ($qry->posts as $p) {
				if($p->post_type == 'post') {
					$postOpt .= sprintf('<option value="%s" %s>%s</option>',
						$p->post_name,
						($p->post_name == $selected_post) ? 'selected' : '',
						$p->post_title
					);
				}
				else {
					$pageOpt .= sprintf('<option value="%s" %s>%s</option>',
						$p->post_name,
						($p->post_name == $selected_post) ? 'selected' : '',
						$p->post_title
					);
				}
			}
			if ($postOpt) {
				$html .= $postGroup . $postOpt . '</optgroup>';
			}
			if ($pageOpt) {
				$html .= $pageGroup . $pageOpt . '</optgroup>';
			}

			$html .= '</select><p class="description" id="CC_popup_selected_posts-description">Select the post/page you want to show the newsletter.</p></td></tr>';

			$data['html'] 	= $html;
			$data['result'] = 1;
		}
		echo json_encode($data);wp_die();
	}


	function CPAS_ajax_getInputOptions(){
		$data['result'] = 0;
		$data['html'] = '';
		if ($_SERVER['REQUEST_METHOD'] == 'POST'){
			$html = '';
			$options = get_option('CC_newsletter_options');
			switch ($_POST['value']) {
				case 'on_after_secs':
					$value = isset($options['CC_popup_no_of_secs']) ? $options['CC_popup_no_of_secs'] : "0";
					$html = '<tr class="dyn_row1"><th scope="row">No of seconds *</th><td><input type="text" id="CC_popup_no_of_secs" name="CC_newsletter_options[CC_popup_no_of_secs]" aria-describedby="CC_popup_no_of_secs-description" value="'. $value .'" class="regular-text" data-validation-rule="required"><p class="description" id="CC_popup_no_of_secs-description">Set the number of seconds when the popup should be displayed - If you set 0 then it will work like <b>After page load</b>.</p></td></tr>';
					$data['result'] = 1;
				break;
				case 'on_scroll':
					$value = isset($options['CC_popup_scroll_position']) ? $options['CC_popup_scroll_position'] : "";
					$html = '<tr class="dyn_row1"><th scope="row">Scroll Position (%)*</th><td><input type="text" id="CC_popup_scroll_position" name="CC_newsletter_options[CC_popup_scroll_position]" aria-describedby="CC_popup_scroll_position-description" value="'. $value .'" class="regular-text" data-validation-rule="required"><p class="description" id="CC_popup_scroll_position-description">Set the percentage of vertical scroll position when the popup should be displayed - If you set 0 then it will work like <b>After page load</b>.</p></td></tr>';
					$data['result'] = 1;
				break;
			}
			$data['html'] = ($html) ? $html : '';
		}
		echo json_encode($data);wp_die();
	}


    
	function CPAS_ajax_update_settings() {
		$data['result'] = 0;
		$data['html'] = '';
		$option_name = 'CC_newsletter_options';
		wp_cache_delete($option_name);
		$options = get_option($option_name);
		if ($options !== false && isset($_POST[$option_name]) && $_POST[$option_name]) {
			global $wpdb;
			$postData = $_POST[$option_name];
			$wpdb->query($wpdb->prepare( "UPDATE $wpdb->options SET option_value = %s WHERE option_name = %s", serialize($postData), $option_name ));
		}
		elseif (isset($_POST[$option_name]) && $_POST[$option_name]){
			add_option( $option_name, $_POST[$option_name], null, 'yes' );
		}		
		echo json_encode(['response' => true]);wp_die();
	}
    /**
     * Add pages
     */
    public function add_plugin_page() {
        // Toplevel menu
        add_object_page('CC-Newsletter Popup Settings', CPAS_PLUGIN_NAME, 'manage_options', $this->slug, array( $this, 'create_admin_page' ), 'dashicons-email');

        // Settings menu
        add_submenu_page($this->slug, 'Settings', 'Settings', 'manage_options', $this->slug, array( $this, 'create_admin_page' ));

        // Subscriber menu
        add_submenu_page(
            $this->slug, 'Subscribers', 'Subscribers', 'manage_options', CPAS_PLUGIN_PREFIX . 'newsletter_admin_subscribers', array( $this, 'subscriber_list' )
        );
    }

    /**
     * Register and add settings
     */
    public function page_init()
    {        
        register_setting(
            'CC_newsletter_options_group', // Option group
            'CC_newsletter_options', // Option name
            array( $this, 'sanitize' ) // Sanitize
        );		
//        wp_enqueue_script( 'CC-popup-scripts',  CPAS_POPUP_URL . 'assets/js/CC-popup-admin.js', array('jquery') );

        // Add Fields
        $inputs = array(
            array(
                'id'=>'CC_popup_title', 'label'=>'Title *', 'callback'=>'input_text_creator'
            ),
            array('id'=>'CC_popup_editor', 'label'=>'Content', 'callback'=>'input_editor_creator', 'desc'=>'Set the popup body content (optional)'),
            array(
                'id'=>'CC_popup_subtext', 'label'=>'Sub Text', 'callback'=>'input_text_creator',
                'desc'=>'The text underneath to the email field (optional)'
            ),
        /*    array(
                'id'=>'CC_popup_is_namefield_needed', 'label'=>'Name Fields', 'callback'=>'input_checkbox_creator',
                'desc'=>'Check if you want to display the firstname and lastname fields (optional)', '_label'=>'Show Firstname and Lastname fields'
            ), */
            array(
                'id'=>'CC_popup_style', 'label'=>'Presets *', 'callback'=>'input_select_creator',
                'options' => array(
                    'classic'=>'Classic', 'modern'=>'Modern', 'trendy'=> 'Trendy'
                ),
                'desc' => 'Set the popup style from the given presets. For <b>Trendy</b> style, you can customize the popup background image.',
                'selected' => 'modern', 'validation-rule' => 'required'
            ),
            array(
                'id'=>'CC_popup_background_color', 'label'=>'Popup background color*', 'callback'=>'input_color_creator',
                'desc' => 'Choose your background color to be displayed in Popup.',
                'validation-rule' => 'required'
            ),
            array(
                'id'=>'CC_popup_title_color', 'label'=>'Popup Title color*', 'callback'=>'input_color_creator',
                'desc' => 'Choose your title color to be displayed in Popup for Modern and Trendy.',
                'validation-rule' => 'required'
            ),
            array(
                'id'=>'CC_popup_content_color', 'label'=>'Popup Content color*', 'callback'=>'input_color_creator',
                'desc' => 'Choose your Content color to be displayed in Popup for Modern and Trendy.',
                'validation-rule' => 'required'
            ),
            array(
                'id'=>'CC_popup_background_image', 'class'=>'media-input', 'label'=>'Popup Background Image*', 'callback'=>'input_chooseimage_creator',
                'desc' => 'Choose your background image for Trendy.'
            ),
            array(
                'id'=>'CC_popup_display_location', 'label'=>'Display Location *', 'callback'=>'input_select_creator',
                'options' => array(
                    'on_home_page'=>'Home Page', 'selected_posts'=>'For Selected Posts / Pages', 'all_posts'=> 'For All Posts / Pages'
                ),
                'desc' => 'Select the location where it should be displayed',
                'selected' => 'on_home_page', 'validation-rule' => 'required',
            ),
            array(
                'id'=>'CC_popup_display_type', 'label'=>'Display Options *', 'callback'=>'input_select_creator',
                'options' => array(
                    'on_load_page'=>'After page load', 'on_after_secs'=> 'After some seconds',
                    'on_scroll'=>'While Scrolling Down'
                ),
                'desc' => 'Select the options when it should be displayed',
                'selected' => 'on_load_page', 'validation-rule' => 'required'
            ),
            array(
                'id'=>'CC_popup_is_show_once', 'label'=>'Display Once', 'callback'=>'input_checkbox_creator',
                'desc'=>'Note: For tracking, we use browser cookies and if the user clear the cookies from his browser then the popup should be displayed again in a day. (optional)', '_label'=>'Display the popup only once for a day '
            )
        );

        // Popup Setting Section
        $sectionID = CPAS_PLUGIN_PREFIX . 'newsletter_settings_section';
        //exit;
        add_settings_section( $sectionID, 'Popup Settings', '', $this->slug );
        foreach ($inputs as $input) {
            $params = array('id' => $input['id'], 'label' => $input['label']);
            if (isset($input['options'])) {
                $params['options'] = $input['options'];
            }
            if (isset($input['desc'])) {
                $params['desc'] = $input['desc'];
            }
            if (isset($input['selected'])) {
                $params['selected'] = $input['selected'];
            }
            if (isset($input['_label'])) {
                $params['_label'] = $input['_label'];
            }
            if (isset($input['validation-rule'])) {
                $params['validation-rule'] = $input['validation-rule'];
            }
            add_settings_field( $input['id'], $input['label'], array( $this, $input['callback'] ), $this->slug, $sectionID, $params );
        }
    }
}

if( is_admin() ) {
	$my_settings_page = new CCNewsletterAdminSettings();
}
