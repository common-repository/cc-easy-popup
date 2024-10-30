<?php
/**
 * Popup init action
 */
function CPAS_popup_enqueue_scripts() {
	// Register
    wp_register_style( 'CPAS-popup-styles',  CPAS_POPUP_URL . 'assets/css/style.css' );
    
    // Enqueue
    wp_enqueue_script( 'CPAS-popup-scripts',  CPAS_POPUP_URL . 'assets/js/CC-newsletter-popup.js', array('jquery') );
    wp_enqueue_style( 'CPAS-popup-styles' );
}
add_action( 'wp_enqueue_scripts', 'CPAS_popup_enqueue_scripts' );

function CPAS_popup_init(){
	$popupId 		= 'CC_popup_id';
	$popupClass		= 'modern';
	$popupTitle 	= 'Encourage the users to leave their e-mails.';
	$popupContent	= "Let them know what kind of content they will receive.";
	$popupSubText 	= "";
	$popupLocation 	= "on_home_page";
	$popupDisplay 	= "on_load_page";

	$isNameFieldNeeded = false;
	$isPopupDisplayOnlyOnce = false;

	// ----------- Set the values from db ---------- 
	global $wpdb;
	$options = $wpdb->get_row($wpdb->prepare("SELECT option_value FROM $wpdb->options WHERE option_name = '%s'", 'CC_newsletter_options'));
	$options = (isset($options->option_value) && $options->option_value) ? unserialize($options->option_value) : array();
	//echo "<script>console.log('".json_encode($options)."')</script>";
	if ($options) {
		$popupClass 	= isset($options['CC_popup_style']) && $options['CC_popup_style'] ? $options['CC_popup_style'] : $popupClass;
		$popupTitle 	= isset($options['CC_popup_title']) && $options['CC_popup_title'] ? $options['CC_popup_title'] : $popupTitle;
		$popupContent 	= isset($options['CC_popup_editor']) && $options['CC_popup_editor'] ? $options['CC_popup_editor'] : $popupContent;
		$popupSubText 	= isset($options['CC_popup_subtext']) && $options['CC_popup_subtext'] ? $options['CC_popup_subtext'] : $popupSubText;
		$popupLocation 	= isset($options['CC_popup_display_location']) && $options['CC_popup_display_location'] ? $options['CC_popup_display_location'] : $popupLocation;
		$popupDisplay 	= isset($options['CC_popup_display_type']) && $options['CC_popup_display_type'] ? $options['CC_popup_display_type'] : $popupDisplay;

		$isNameFieldNeeded = isset($options['CC_popup_is_namefield_needed']) ? true : false;
		$isPopupDisplayOnlyOnce = isset($options['CC_popup_is_show_once']) ? true : false;
		//echo "<pre>";print_r($options);echo "</pre>";
	}
	
	// ----------- Popup HTML -----------------------
  $html  = "<style>";
   $html .= ".CC-newsletter-popup.".$options['CC_popup_style']."{background-color:".$options['CC_popup_background_color'].";}";
        $html .= ".CC-newsletter-popup.".$options['CC_popup_style']." .content{color:".$options['CC_popup_content_color'].";}";
        $html .= ".CC-newsletter-popup.".$options['CC_popup_style']." h2{color:".$options['CC_popup_title_color'].";}";
  switch($options['CC_popup_style']) {
    case "trendy":
        $html .= ".CC-newsletter-popup.".$options['CC_popup_style'].".trendy{background:url(".$options['CC_popup_background_image'].");}";
      break;
  }
  
  $html .= "</style>";
	$html .= "<div id='". $popupId ."' class='CC-newsletter-popup-overlay'>";
	$html .= "<div class='CC-newsletter-popup ". $popupClass ."'><h2>". $popupTitle ."</h2>";
	$html .= "<a class='close' href='#'>×</a><div class='content'>". $popupContent ."</div>";
	/* $html .= "<div class='email-section'>";
	if ($isNameFieldNeeded) {
		$html .= "<input type=='text' name='CC-newsletter-popup-firstname' class='name' placeholder='Firstname' />";
		$html .= "<input type=='text' name='CC-newsletter-popup-lastname' class='name' placeholder='Lastname' />";
	}
	$html .= "<input type='text' name='CC-newsletter-popup-email' class='email' placeholder='Your e-mail'/><button>Subscribe</button>"; */
	$html .= "<small>". $popupSubText ."</small></div></div></div>";
	echo $html;

	// ----------- Popup display options --------------
	$script = null;
	$fnPopupOpen = $isPopupDisplayOnlyOnce ? "CCPopup.open('CC_popup_id','verifyCookie');" : "CCPopup.open('CC_popup_id');";
	switch ($popupDisplay) {
		case 'on_load_page':
			$script = "jQuery(document).ready(function(){ $fnPopupOpen });";
		break;
		case 'on_leave_page':
			$script  = "jQuery(document).ready(function(){";
			$script .= "jQuery(window).on('beforeunload', function(e){";
			$script .= "$fnPopupOpen return 'Have you looked into our newsletter popup!';";
			$script .= "});});";
		break;
		case 'on_scroll':
			$percentage = isset($options['CC_popup_scroll_position']) ? $options['CC_popup_scroll_position'] : 0;
			$script  = "jQuery(document).ready(function(){var isPopupShown = false;jQuery(window).scroll(function(){";
			$script .= "var userPercentage = parseInt($percentage);";
			$script .= "var scrollHeight = jQuery(document).height() - jQuery(window).height();";
			$script .= "var currentPercentage = (jQuery(window).scrollTop() / scrollHeight) * 100;";
			$script .= "currentPercentage = Math.round(currentPercentage);";
			$script .= "if(currentPercentage === userPercentage && isPopupShown === false){";
			$script .= "isPopupShown = true;$fnPopupOpen";
			$script .= "}});});";
		break;
		case 'on_after_secs':
			$no_of_secs_delay = isset($options['CC_popup_no_of_secs']) ? $options['CC_popup_no_of_secs'] : 0;
			$millisecs = $no_of_secs_delay * 1000;
			$script = "jQuery(document).ready(function(){window.setTimeout(function(){ $fnPopupOpen }, $millisecs);});";
		break;
	}

	// ----------- Popup Display Location ---------------
	if ($script) {
		switch( $popupLocation ){
			case 'on_home_page':
				$homePageURL = str_replace(array('http://','https://'), '', home_url()) . '/';
				$currentURL = $_SERVER['HTTP_HOST'].$_SERVER['REQUEST_URI'];
				if ($homePageURL === $currentURL) {
					echo "<script type='text/javascript'>$script</script>";
				}
			break;
			case 'selected_posts':
				$post = get_post();
				$postLocation = isset($options['CC_popup_selected_posts']) ? $options['CC_popup_selected_posts'] : '';
				if ($post->post_name === $postLocation) {
					echo "<script type='text/javascript'>$script</script>";
				}
			break;
			default:
				echo "<script type='text/javascript'>$script</script>";
			break;
		}
	}
}
add_action( 'wp_footer', 'CPAS_popup_init' );
