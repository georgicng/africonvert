<?php 

function af_logout(){
    /*if( $_SESSION['ajax_security'] != $_POST['security'] ){
			return wp_send_json(array('error' => true, 'message' => 'Session token has expired, please reload the page and try again'));			
	}*/
    wp_clear_auth_cookie();
    wp_logout();
    //ob_clean(); // probably overkill for this, but good habit
    return wp_send_json(array('error' => false, 'message' => "You've been logged out successfully.", 'user' => array( "id" => 0)));
    //wp_die();
}
add_action('wp_ajax_af_logout', 'af_logout');
