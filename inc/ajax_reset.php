<?php 

function af_forgotPassword(){
	 
	// First check the nonce, if it fails the function will break
    if( !check_ajax_referer( 'ajax-login-nonce', 'security', false) ){
			return wp_send_json_error('Session token has expired, please reload the page and try again');
			
		}
	
	global $wpdb;
	
	$account = $_POST['login'];
	
	if( empty( $account ) ) {
		$error = 'Enter an username or e-mail address.';
	} else {
		if(is_email( $account )) {
			if( email_exists($account) ) 
				$get_by = 'email';
			else	
				$error = 'There is no user registered with that email address.';			
		}
		else if (validate_username( $account )) {
			if( username_exists($account) ) 
				$get_by = 'login';
			else	
				$error = 'There is no user registered with that username.';				
		}
		else
			$error = 'Invalid username or e-mail address.';		
	}	
	
	if(empty ($error)) {
		// lets generate our new password
		//$random_password = wp_generate_password( 12, false );
		$random_password = wp_generate_password();

			
		// Get user data by field and data, fields are id, slug, email and login
		$user = get_user_by( $get_by, $account );
			
		$update_user = wp_update_user( array ( 'ID' => $user->ID, 'user_pass' => $random_password ) );
			
		// if  update user return true then lets send user an email containing the new password
		if( $update_user ) {
			
			$from = 'WRITE SENDER EMAIL ADDRESS HERE'; // Set whatever you want like mail@yourdomain.com
			
			if(!(isset($from) && is_email($from))) {		
				$sitename = strtolower( $_SERVER['SERVER_NAME'] );
				if ( substr( $sitename, 0, 4 ) == 'www.' ) {
					$sitename = substr( $sitename, 4 );					
				}
				$from = 'admin@'.$sitename; 
			}
			
			$to = $user->user_email;
			$subject = 'Your new password';
			$sender = 'From: '.get_option('name').' <'.$from.'>' . "\r\n";
			
			$message = 'Your new password is: '.$random_password;
				
			$headers[] = 'MIME-Version: 1.0' . "\r\n";
			$headers[] = 'Content-type: text/html; charset=iso-8859-1' . "\r\n";
			$headers[] = "X-Mailer: PHP \r\n";
			$headers[] = $sender;
				
			$mail = wp_mail( $to, $subject, $message, $headers );
			if( $mail ) 
				$success = 'Check your email address for you new password.';
			else
				$error = 'System is unable to send you mail containg your new password.';						
		} else {
			$error = 'Oops! Something went wrong while updaing your account.';
		}
	}
	
	if( ! empty( $error ) )
		wp_send_json_error(array('loggedin'=>false, 'message'=>__($error)));
			
	if( ! empty( $success ) )
		wp_send_json(array('loggedin'=>false, 'message'=>__($success)));
				
	//die();
}
add_action( 'wp_ajax_nopriv_af_forgotpassword', 'af_forgotPassword' );