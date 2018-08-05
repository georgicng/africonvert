<?php
/*
This code will send an email and a BP notification to members when someone they are following
publishes a post. There is a settings page in  WP Admin > Settings > Buddypress Followers Notify
where you can set the Sender Name, Sender Email and select which post type to notify on.
Version: 1.1.0
*/

/*  Add settings page to WP Admin */

add_action( 'admin_menu', 'bp_follow_notify_add_admin_menu' );
add_action( 'admin_init', 'bp_follow_notify_settings_init' );

function bp_follow_notify_add_admin_menu(  ) { 

	add_options_page( 'Buddypress Folllowers Notify', 'Buddypress Followers Notify', 'manage_options', 'buddypress_folllowers_notify', 'bp_follow_notify_options_page' );

}


function bp_follow_notify_settings_init(  ) { 

	register_setting( 'pluginPage', 'bp_follow_notify_settings' );

	add_settings_section(
		'bp_follow_notify_pluginPage_section', 
		__( '', 'bp_follow_notify' ), 
		'bp_follow_notify_settings_section_callback', 
		'pluginPage'
	);

	add_settings_field( 
		'bp_follow_notify_sender_name', 
		__( 'Sender Name', 'bp_follow_notify' ), 
		'bp_follow_notify_sender_name_render', 
		'pluginPage', 
		'bp_follow_notify_pluginPage_section' 
	);

	add_settings_field( 
		'bp_follow_notify_sender_email', 
		__( 'Sender Email', 'bp_follow_notify' ), 
		'bp_follow_notify_sender_email_render', 
		'pluginPage', 
		'bp_follow_notify_pluginPage_section' 
	);
	
	add_settings_field( 
		'bp_follow_notify_post_types', 
		__( 'Post Types', 'bp_follow_notify' ), 
		'bp_follow_notify_post_types_render', 
		'pluginPage', 
		'bp_follow_notify_pluginPage_section' 
	);

}


function bp_follow_notify_sender_name_render(  ) { 

	$options = get_option( 'bp_follow_notify_settings' );
	?>
	<input type='text' name='bp_follow_notify_settings[bp_follow_notify_sender_name_render]' value='<?php echo $options['bp_follow_notify_sender_name_render']; ?>'>
	<?php

}


function bp_follow_notify_sender_email_render(  ) { 

	$options = get_option( 'bp_follow_notify_settings' );
	?>
	<input type='text' name='bp_follow_notify_settings[bp_follow_notify_sender_email]' value='<?php echo $options['bp_follow_notify_sender_email']; ?>'>
	<?php

}

function bp_follow_notify_post_types_render(  ) { 

	$options = get_option( 'bp_follow_notify_settings' );
	$args = array(
	   'public'   => true
	);
	$post_types = get_post_types( $args );
	?>
	<select name='bp_follow_notify_settings[bp_follow_notify_post_types][]' multiple='multiple'>
    <?php foreach ( $post_types as $post_type ) { ?>
    	<?php $selected = in_array( $post_type, $options['bp_follow_notify_post_types'] ) ? ' selected="selected" ' : ''; ?>
		<option value='<?php echo $post_type; ?>' <?php echo $selected; ?>><?php echo $post_type; ?></option>
    <?php } ?>
	</select>

<?php

}

function bp_follow_notify_settings_section_callback(  ) { 

	echo __( '', 'bp_follow_notify' );

}

function bp_follow_notify_options_page(  ) { 

	?>
	<form action='options.php' method='post'>
		
		<h2>Buddypress Folllowers Notify</h2>
		
		<?php
		settings_fields( 'pluginPage' );
		do_settings_sections( 'pluginPage' );
		submit_button();
		?>
		
	</form>
	<?php

}

add_action( 'new_to_publish', 'bp_follow_notify_author_followers', 99, 1 );
add_action( 'draft_to_publish', 'bp_follow_notify_author_followers', 99, 1 );

function  bp_follow_notify_author_followers( $post ){
	
	$options = get_option( 'bp_follow_notify_settings' );
	
	if ( !in_array( get_post_type(), $options['bp_follow_notify_post_types'] ) ) {
		return;
	}
				
	$author_id = $post->post_author;
	$author_name = get_the_author_meta( 'display_name' , $author_id );
	$counts  = bp_follow_total_follow_counts( array( 'user_id' => $author_id ) );
	
	if ( $counts['followers'] > 0 ) {
		
		$followers = bp_follow_get_followers( array( 'user_id' => $author_id ) )  ;
						
		foreach($followers as $follower){
			
			/* Email Notification */
			
			$user = get_user_by( 'id' , $follower );
			$follower_email = $user->user_email;
			$blog_name = wp_specialchars_decode( get_option( 'blogname' ), ENT_QUOTES );
			$subject = '[' . $blog_name . '] New Post From ' . $author_name;
			
			$message = sprintf( __( '

				%2$s
				
				%3$s
				
				Link: %4$s

				-----------

				You are receiving this email because you are following %1$s.', 'bp-follow-notify' ),

					$author_name,
					$post->post_title,
					wp_trim_words( $post->post_content ),
					get_permalink( $post->ID )
				);
		
			$sender_name = ( !empty( $options['bp_follow_notify_sender_name']) ? $options['bp_follow_notify_sender_name'] : $blog_name );
			$sender_email = ( !empty( $options['bp_follow_notify_sender_email'] ) ? $options['bp_follow_notify_sender_email'] : 'no-reply@' . $blog_name );
		
			$headers = '';
			
			$headers  = "MIME-Version: 1.0" . "\r\n";
			$headers .= "Content-type: text/html; charset=" . get_bloginfo('charset') . "" . "\r\n";
			$headers .= "From: " . $sender_name . " <" . $sender_email . ">" . "\r\n";
			
 	
			if( wp_mail( $follower_email, $subject , $message, $headers ) ) {
				
				//echo "sending success";
			} else {
				
				//echo "sending failed";
			}	
			
			/* BP Notification */
			
			if ( bp_is_active( 'notifications' ) ) {
				
				bp_notifications_add_notification( array(
					'user_id'           => $user->ID,
					'item_id'			=> $post->ID,
					'secondary_item_id' => $author_id,
					'component_name'    => 'bp_follow_notify',
					'component_action'  => 'follow_new_post',
					'date_notified'     => bp_core_current_time(),
					'is_new'            => 1,
				) );
				
			}				
			
		} //end loop		

	} 	
	
 }

/*  Register component */

function bp_follow_notify_filter_notifications_get_registered_components( $component_names = array() ) {
	// Force $component_names to be an array
	if ( ! is_array( $component_names ) ) {
		$component_names = array();
	}
	
	array_push( $component_names, 'bp_follow_notify' );
	
	return $component_names;
}
add_filter( 'bp_notifications_get_registered_components', 'bp_follow_notify_filter_notifications_get_registered_components' );


/* Format screen notfications */ 

add_filter( 'bp_notifications_get_notifications_for_user', 'bp_follow_notify_format_notifications', 9, 5  );

function bp_follow_notify_format_notifications( $action, $item_id, $secondary_item_id, $total_items, $format = 'string' ) {
		
	if ( 'follow_new_post' === $action ) {
		
		if ( (int) $total_items > 1 ) {
			$title = sprintf( __( '%d new posts have been published', 'bp-follow-notify' ), (int) $total_items );
			$link  = bp_get_notifications_permalink();
		} else {
			$title = sprintf( __( '%s has published a new post.', 'bp-follow-notify' ), bp_core_get_user_displayname( $secondary_item_id ) );
			$link = get_permalink( $item_id );
		}
		
		// WordPress Toolbar
		if ( 'string' === $format ) {
			$return = apply_filters( 'bp_follow_notify_notification', '<a href="' . esc_url( $link ) . '" title="' . esc_attr( title ) . '">' . esc_html( $title ) . '</a>', $title, $link );
		// Deprecated BuddyBar
		} else {
			$return = apply_filters( 'bp_follow_notify_notification', array(
				'text' => $title,
				'link' => $link
			), $link, (int) $total_items, $title, $title );
		}
		
		return $return;
		
	}
	
	return $action;

}

?>