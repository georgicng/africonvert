<?php

function my_rest_prepare_attachment( $data, $post, $request ) {

	$_data = $data->data;
	if ( isset($_data['media_type']) && 'image' == $_data['media_type'] )
		$_data['is_image'] = true;
	else
		$_data['is_image'] = false;
	$data->data = $_data;

	return $data;
}
add_filter( 'rest_prepare_attachment', 'my_rest_prepare_attachment', 10, 3 );

function cp_get_featured_image_thumbnail_url( $object, $field_name, $request ) {
    $thumbnail_id = get_post_thumbnail_id( $object[ 'id' ] );
	$thumbnail = wp_get_attachment_image_src( $thumbnail_id );
	return $thumbnail[0];
}

function cp_register_featured_image() {
    register_rest_field( array('contests', 'submissions'),
        'featured_image_thumbnail_url',
        array(
            'get_callback'    => 'cp_get_featured_image_thumbnail_url',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}
add_action( 'rest_api_init', 'cp_register_featured_image' );