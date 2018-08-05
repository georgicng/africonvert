<?php

function af_login()
{

        // Get variables
        $user_login         = $_POST['username'];
        $user_password      = $_POST['password'];


    /* Check CSRF token!wp_verify_nonce($_POST['security'], 'ajax-login-nonce') 
    if ( $_SESSION['ajax_security'] != $_POST['security'] ) {
        return wp_send_json_error('Session token has expired, please reload the page and try again');
    } else */
	
	// Check if input variables are empty
    if (empty($user_login) || empty($user_password)) {
        return wp_send_json_error('Please fill all form fields');
    } else { // Now we can insert this account

        $user = wp_signon( array('user_login' => $user_login, 'user_password' => $user_password), false );

        if (is_wp_error($user)) {
            return wp_send_json_error($user->get_error_message());
        } else {
            //$user_data = get_userdata($user->ID);
            $user_data = array(
            "id" => $user->ID,
            "firstName" => $user->first_name,
            "lastName" => $user->last_name,
            "displayName" => $user->display_name,
            "userName" => $user->user_login,
            "role" => implode(', ', $user->roles),
            "email" => $user->user_email,
            "confirmed" => get_field('confirmed', 'user_'. $user->ID )
            );
			wp_set_current_user($user->ID);
            return wp_send_json(array('error' => false, 'message'=> 'Login successful, reloading page...', 'data' => $user_data));
        }
    }
        // wp_die();
}
add_action('wp_ajax_nopriv_af_login', 'af_login');
