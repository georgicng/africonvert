<?php

function my_rest_prepare_post( $data, $post, $request ) {

	$_data = $data->data;
	$thumbnail_id = get_post_thumbnail_id( $post->ID );
	$thumbnail = wp_get_attachment_image_src( $thumbnail_id );
	$cover = wp_get_attachment_image_src( $thumbnail_id, 'cover' );
	$full = wp_get_attachment_image_src( $thumbnail_id, 'full' );
	$_data['featured_image_thumbnail_url'] = $thumbnail[0];
	$_data['featured_image_cover_url'] = $cover[0];
	$_data['featured_image_url'] = $full[0];
	$data->data = $_data;

	return $data;
}
add_filter( 'rest_prepare_post', 'my_rest_prepare_post', 10, 3 );

function my_rest_post_query( $args, $request ) {

	if ( isset( $request['filter'] ) && isset( $request['filter']['posts_per_page'] ) && ! empty( $request['filter']['posts_per_page'] ) ) {
		if ( $request['filter']['posts_per_page'] > 0 ) {
			$request['per_page'] = $request['filter']['posts_per_page'];
		} else {
			$count_query = new WP_Query();
			unset( $query_args['paged'] );
			$query_result = $count_query->query( $query_args );
			$total_posts = $query_result->found_posts;
			$request['per_page'] = $total_posts;
		}
	}

	return $args;
}
add_filter( 'rest_post_query', 'my_rest_post_query', 10, 2 );

















