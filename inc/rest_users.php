<?php

function list_author_used_terms($author_id, $cpt, $tax)
{

    $posts = get_posts( array('post_type' => $cpt, 'posts_per_page' => -1, 'author' => $author_id) );
    $author_terms = array();
    foreach ($posts as $p) {
        $terms = wp_get_object_terms( $p->ID, $tax);
        foreach ($terms as $t) {
            $author_terms[] = $t->term_id;
        }
    }
    if (!empty($author_terms)) {
        $args = array (
        'include' => array_unique($author_terms),
        'post_type' => 'contests'
        );
        return get_posts( $args);
    }
    return array();
}

function sendVerificationEmail($token, $email)
{
    $headers = 'From: admin <admin@gaiproject.com>';
    $subject = 'Confirm Account';    // The unique token can be inserted in the message with %s
    $message = 'Thank you for signing up. Please <a href="'.home_url('confirm').'/%s">confirm</a> to complete your registration';

    return wp_mail($email, $subject, sprintf($message, $token), $headers);
}

function sendResetPasswordEmail($user, $password)
{
    $from = 'admin@gaiproject.com';
            
    if (!(isset($from) && is_email($from))) {
        $sitename = strtolower( $_SERVER['SERVER_NAME'] );
        if (substr( $sitename, 0, 4 ) == 'www.') {
            $sitename = substr( $sitename, 4 );
        }
        $from = 'admin@'.$sitename;
    }
            
    $to = $user->user_email;
    $subject = 'Your new password';
    $message = 'Your new password is: '.$password;
        
    $headers = array("From: $from");
        
    return wp_mail( $to, $subject, $message );
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/author/(?P<id>\d+)/contests', array(
    'methods' => 'GET',
    'callback' => 'cpt_author_contests',
    ) );
} );

function cpt_register_contests($data)
{
    return list_author_used_terms($data['id'], 'submissions', 'contests');
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/users/register', array(
    'methods' => 'POST',
    'callback' => 'cpt_register_user',
    'args' => array(
                    'username' => array(
                        'default' => null,
                        'required' => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return ! empty($param);
                        }
                    ),
                    'email' => array(
                        'default' => null,
                        'required' => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return (! empty($param) && is_email( $param ));
                        }
                    ),
                    'password' => array(
                        'default' => null,
                        'required' => true,
                        'validate_callback' => function ($param, $request, $key) {
                            return ! empty($param);
                        }
                    ),
                )
    ) );
} );

function cpt_register_user($data)
{

    $userdata = array(
        'user_login'  =>  $data['username'],
        'user_email'    =>  $data['email'],
        'user_pass'   =>   $data['password'],
        'first_name'  =>$data['first_name'],
        'last_name' =>  $data['last_name'],
        'description' => $data['description'],
        );
        
         
    $user_id = wp_insert_user( $userdata );
        
    if (is_wp_error($user_id)) {
        return $user_id;
    } else {
        $response = wp_remote_post( get_rest_url()."jwt-auth/v1/token", array(
                'method' => 'POST',
                'timeout' => 45,
                'redirection' => 5,
                'httpversion' => '1.0',
                'blocking' => true,
                'headers' => array(),
                'body' => array( 'username' => $data['username'], 'password' => $data['password'] ),
                'cookies' => array()
            )
        );

        if (is_wp_error( $response )) {
            return $response;
        } else {
            return array('success'=>true, 'data' => $response['body']);
        }
    }
}

add_action( 'user_register', 'verify_user_email' );

function verify_user_email($id)
{
    global $email_queue;
    $user_info = get_userdata($id);

    //Send confirmation email -- consider changing to action that sends only after welcome email is successful 
    $token = sha1(uniqid());
    add_user_meta( $id, 'confirmation_token', $token, true); 
    
    //$meta['subject'] = "Confirm Your Afriflow Account";
    //$template_data['link'] = home_url('confirm')."/".$token;
    //sendMail($meta, "confirmation", $template_data);
    $item['meta']['subject'] = "Confirm Your Afriflow Account";
    $item['template'] = "confirmation";
    $item['data']['link'] =  home_url('confirm')."/".$token; 
    $email_queue->push_to_queue( $item ); 
    
    $email_queue->save()->dispatch();
}



add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/users/reset', array(
      'methods' => 'POST',
      'callback' => 'cpt_reset_password',
      'args' => array(
            'email' => array(
                'default' => null,
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return (! empty($param) && is_email( $param ));
                }
            ),
        ),
      )
    );
});

function cpt_reset_password($data)
{
    // Get user data by field and data, fields are id, slug, email and login
    $user_info = get_user_by( 'email', $data['email'] );

    if ($user_info != false) {
         $random_password = wp_generate_password();
        $update_user = wp_update_user( array ( 'ID' => $user_info->ID, 'user_pass' => $random_password ) );
            
    // if  update user return true then lets send user an email containing the new password
        if (! is_wp_error($update_user)) {
            $template_data = array ( 'name' => $user_info->user_nicename, 'password' => $random_password );
            $meta = array ('to' => $user_info->user_email, 'subject' => 'Afriflow Password Reset', 'from' => 'afriflow@afriflow.com');
            if (sendMail($meta, "reset", $template_data)) {
                return array('success'=>true, 'message'=>'Password reset successful, please login with your new password');
            } else {
                return new WP_Error('email_error', "Couldn't send mail, please try again");
            }
        } else {
              return new WP_Error('update_error', "Couldn't update password");
        }
    } else {
        return new WP_Error('user_error', 'User doesnt exist');
    }
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/users/verify', array(
      'methods' => 'GET',
      'callback' => 'cpt_verify_email',
      'args' => array(
            'token' => array(
                'required' => true,
                'validate_callback' => function ($param, $request, $key) {
                    return (! empty($param) && is_string( $param ));
                }
            ),
        ),
      )
    );
});

function cpt_verify_email($data)
{

    $token  = $data['token'];
    $args = array(
        'meta_key' => 'confirmation_token',
        'meta_value' => $token,
        'compare' => '='
    );
    $users = get_users($args);
    
    if (count($users) < 1) {
        return WP_Error('no_token', 'Token doesn\'t exist');
    }
    
    $user = $users[0];

    update_field("confirmed", true, "user_".$user->ID);
  
	// Remove role
	$user->remove_role( 'subscriber' );
	
	// Add role
	$user->add_role( 'author' );
    
    global $email_queue;
    $user_info = get_userdata($user->ID);

    $item['meta'] = array ('to' => $user_info->user_email, 'subject' => 'Welcome to Afriflow', 'from' => 'afriflow@afriflow.com');
    $item['template'] = "signup";
    $item['data'] = array ( 'name' => $user_info->user_nicename );    
    $email_queue->push_to_queue( $item ); 
    
    if (is_user_logged_in() && $user->ID == get_current_user_id()) {
        return array (
            status => 'success',
            data =>  getUserProfile($user->user_email)
        );
    } else {
        return array (
            status => 'success',
            data =>  true
        );
    }
}

add_action( 'rest_api_init', function () {
    register_rest_route( 
        'wp/v2', 
        '/users/verify/(?P<id>\d+)', 
        array(
            'methods' => 'GET',
            'callback' => 'cpt_resend_verification',            
            'permission_callback' => function () {
                    return is_user_logged_in() && current_user_can( 'edit_user', get_current_user_id() );
            }
        )
    );
});

function cpt_resend_verification($data)
{

    $user  = $data['id'];

    $user_info = get_userdata( $user );

    if ($user_info) {
        $token = get_user_meta( $user, 'confirmation_token', true);

        if (empty($token)) {
            $token = sha1(uniqid());
            add_user_meta( $user, 'confirmation_token', $token, true);
        }
        $template_data = array ( 'name' => $user_info->user_nicename, 'link' => home_url('confirm')."/".$token );
        
        $meta = array ('to' => $user_info->user_email, 'subject' => 'Confirmation Mail', 'from' => 'afriflow@afriflow.com');
        if (sendMail($meta, "confirmation", $template_data)) {
             return array (
                status => 'success',
                data =>  true
             );
        }

        return array (
            status => 'fail',
            data =>  false
        );
    } else {
        //mail admin about an anomaly...logged in user doesn't exist
        return new WP_Error('user_unavailable', 'There is no such user with the given ID');
    }
}

add_action( 'rest_api_init', function () {
    register_rest_field( 'user',
        'fields',
        array(
            'update_callback' => 'cpt_update_acf_cb',
        )
    );
});

function cpt_update_acf_cb($value, $object, $field_name)
{
    if (! $value || ! is_array( $value )) {
        return;
    }

    foreach ($value as $key => $value) {
        update_field($key, $value, "user_".$object->ID);
    }
    
    return true;
}

function user_register_cover()
{
    register_rest_field( array('user'),
        'cover_pic',
        array(
            'get_callback'    => 'cpt_getCover',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

add_action( 'rest_api_init', 'user_register_cover' );

function cpt_getCover($object, $field_name, $request)
{
    return getCover( $object[ 'id' ]);
}

function user_register_avatar()
{
    register_rest_field( array('user'),
        'profile_pic',
        array(
            'get_callback'    => 'cpt_getAvatar',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

add_action( 'rest_api_init', 'user_register_avatar' );

function cpt_getAvatar($object, $field_name, $request)
{
    return bp_core_fetch_avatar( array('item_id' => $object[ 'id' ],'object' => 'user','html' => false));
}

add_action( 'rest_api_init', 'add_profile_data' );
function add_profile_data()
{
    register_rest_field( 'user', 'profile',
        array(
            'get_callback'    => 'update_get_profile',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

/**
 * Get the value of the "starship" field
 *
 * @param array $object Details of current post.
 * @param string $field_name Name of field.
 * @param WP_REST_Request $request Current request
 *
 * @return mixed
 */
function update_get_profile($object, $field_name, $request)
{
    $route = '/wp/v2/users/(?P<id>\d+)';
    $method = $request->get_method();
    $path   = $request->get_route();
    $match = preg_match( '@^' . $route . '$@i', $path, $args );
    if ($match && $method == 'POST') {
        $user_info = get_userdata($object[ 'id' ]);
        return getUserProfile( $user_info->user_email );
    }
    return null;
}

function cpt_rest_prepare_users($data, $post, $request)
{
    $_data = $data->data;

    if (isset( $_data['type'] ) && $_data['type'] == "user") {
		$user = get_user_by('id', $_data['id']);
        $_data['user_info'] = array (
			"username" => $user->user_login,
			"name" => $user->user_nicename
		);
    }

    
    $data->data = $_data;
    

    return $data;
}
add_filter( 'rest_prepare_user', 'cpt_rest_prepare_users', 10, 3);
