<?php

// REGISTER
function af_register()
{
      
        $userdata = array(
        'user_login'  =>  $_POST['username'],
        'user_email'    =>  $_POST['email'],
        'user_pass'   =>   $_POST['password'],
        'first_name'  =>$_POST['first_name'],
        'last_name' =>  $_POST['last_name'],
        'description' => $_POST['description'],
        );

        //Check CSRF token
    if (!check_ajax_referer( 'ajax-login-nonce', 'security', false)) {
        wp_send_json_error('Session token has expired, please reload the page and try again');
    }
        // Check if input variables are empty
    if (empty($userdata['user_login']) || empty($userdata['user_email']) || empty($userdata['user_pass'])) {
        wp_send_json_error('Please fill all form fields');
    }
        
         
    $user_id = wp_insert_user( $userdata );
        
    if (is_wp_error($user_id)) {
        wp_send_json_error($user_id);
    } else {
        //send_user_confirmation_mail('admin@gaiproject.com', $userdata['user_email'], home_url('confirm'), $_POST );
        //$user = get_userdata($user_id);
        $response = wp_remote_post( get_rest_url()."jwt-auth/v1/token", array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array( 'username' => $userdata['user_login'], 'password' => $userdata['user_pass'] ),
                'cookies' => array()
            )
        );

        if (is_wp_error( $response )) {
            wp_send_json_error($response->get_error_message());
        } else {
            wp_send_json($response['body']);
        }
    }
}
add_action('wp_ajax_nopriv_af_register', 'af_register');

