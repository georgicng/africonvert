<?php 

function post_get_votes( $object, $field_name, $request ) {
	global $wpdb;
	$id = $object[ 'id' ];
    if ($object[ 'type' ] == "submissions")
        $stat = gs_vp_get_vote_count($id);
    elseif ($object[ 'type' ] == "contests")
	    $stat = gs_vp_get_vote_total($id);
	
    return  $stat;
}


function post_register_votes() {
    register_rest_field( array('contests','submissions'),
        'vote_count',
        array(
            'get_callback'    => 'post_get_votes',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

add_action( 'rest_api_init', 'post_register_votes' );