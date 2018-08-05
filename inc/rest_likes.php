<?php 

function post_get_likes( $object, $field_name, $request ) {
	global $wpdb;
	$id = $object[ 'id' ];
	$user = get_current_user_id();
	return  gs_lp_get_like_info( $id, $user );
}


function post_register_likes() {
    register_rest_field( array('post','contests','submissions'),
        'likes',
        array(
            'get_callback'    => 'post_get_likes',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

add_action( 'rest_api_init', 'post_register_likes' );