<?php

/* --------------------------------------------------------- */
/* !Load the admin scrips - 2.0.2 */
/* --------------------------------------------------------- */

function CC_POPUP_my_enqueue() {
	wp_enqueue_script( 'ajax-script', plugins_url( '/assets/js/CC-popup-admin.js', __FILE__ ), array('jquery') );

	// in JavaScript, object properties are accessed as ajax_object.ajax_url, ajax_object.we_value
	wp_localize_script( 'ajax-script', 'ajax_object',
			array( 'ajax_url' => admin_url( 'admin-ajax.php' ), 'we_value' => 1234 ) );
      wp_enqueue_media();
}
add_action( 'admin_enqueue_scripts', 'CC_POPUP_my_enqueue' );

