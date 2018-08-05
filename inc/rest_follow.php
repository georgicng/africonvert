<?php

add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/follow', array (
        array(
                'methods' => 'POST',
                'callback' => 'gs_fu_follow',
                'args' => array(
                    'follow' => array(
                        'default' => null,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric( $param );
                        }
                    ),
                    'unfollow' => array(
                        'default' => null,
                        'validate_callback' => function ($param, $request, $key) {
                            return is_numeric( $param );
                        }
                    ),
                ),
                'permission_callback' => function () {
                    return current_user_can( 'read' );
                }
            )
    ));
});

function gs_fu_follow(WP_REST_Request $request)
{
    $user = get_current_user_id();
    if ($user != 0 && null != $request->get_param('follow')) {
        $id = $request->get_param( 'follow' );
        //check if already following
        $res = bp_follow_start_following (
            array(
                'leader_id'   => $id,
                'follower_id' => $user
            )
        );
        if($res){
            return array (
                "status" => "success",
                'data' => $res
            );
        } else {
            return new WP_Error('update_error', "Coun't complete the request");
        }
    }
    
    if ($user != 0 && null != $request->get_param('unfollow')) {
        $id = $request->get_param( 'unfollow' );
        //check if already following
        $res = bp_follow_stop_following (
            array(
                'leader_id'   => $id,
                'follower_id' => $user
            )
        );
        if($res){
            return array (
                "status" => "success",
                'data' => $res
            );
        } else {
            return new WP_Error('update_error', "Coun't complete the request");
        }
    }

    return new WP_Error('request_error', "Something went wrong");
}


//TODO: add pagination and per_post
add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/author/(?P<id>\d+)/followers', array(
    'methods' => 'GET',
    'callback' => 'gs_fu_getFollowers',
    'args' => array(
            'page' => array(
                'default' => 1,
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric( $param );
                }
            ),
            'per_page' => array(
                'default' => 10,
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric( $param );
                }
            ),
            'offset' => array(
                'default' => 0,
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric( $param );
                }
            )
        ),
    ) );
} );

function gs_fu_getFollowers(WP_REST_Request $request)
{
    $id = $request->get_param( 'id' );
    $args = array(
        'number' => $request->get_param( 'per_page' ),
        'paged' => $request->get_param( 'page' ),
        'offset' => $request->get_param( 'offset' ),
        'fields' => ['ID', 'user_login'],
        'total_count' => true
    );

    if (null != $id && $id != 0) {
        $users = bp_follow_get_followers ( array ('user_id' => $id ));
        if (!empty( $users )) {
            $args['include']  = array_map(function ($v) {
                    return($v);
                },$users);  
            //TODO: return user profiles, not ids by wpquery include ids from follow table
            $followers =  new WP_User_Query( $args );
            foreach ($followers->get_results() as $follower){
                 $follower->avatar = getAvatar($follower->ID);
            }           
            
            return array (
                'followers' => $followers->get_results(),
                'total' => $followers->get_total(),
                'pages' => ceil($followers->get_total() / $request->get_param( 'per_page' ))
            );
        } else {
            return array (
                'followers' => [],
                'total' => 0,
                'pages' => 0
            );
        }
    }

   return new WP_Error( 'social_error', 'User Id is required to complete request and it cannot be 0', array( 'status' => 404 ) );
}


//TODO: add pagination and per_post
add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/author/(?P<id>\d+)/following', array(
    'methods' => 'GET',
    'callback' => 'gs_fu_getFollowing',
    'args' => array(
            'page' => array(
                'default' => 1,
                'validate_callback' => 'is_numeric'
            ),
            'per_page' => array(
                'default' => 10,
                'validate_callback' => 'is_numeric'
            ),
            'offset' => array(
                'default' => 0,
                'validate_callback' => 'is_numeric'
            )
        ),
    ) );
} );

function gs_fu_getFollowing(WP_REST_Request $request)
{
    $id = $request->get_param( 'id' );
    $args = array(
        'number' => $request->get_param( 'per_page' ),
        'paged' => $request->get_param( 'page' ),
        'offset' => $request->get_param( 'offset' ),
        'fields' => ['ID', 'user_login'],
        'total_count' => true
    );

    if (null != $id && $id != 0) {
        $users = bp_follow_get_following ( array ('user_id' => $id ));
        if (!empty( $users )) {
           $args['include']  = array_map(function ($v) {
                    return($v);
                },$users);    
            //TODO: return user profiles, not ids by wpquery include ids from follow table
            $followings = new WP_User_Query( $args );
            foreach ($followings->get_results() as $following){
                 $following->avatar = getAvatar($following->ID);
            }             
            return array (
                'followings' => $followings->get_results(),
                'total' => $followings->get_total(),
                'pages' => ceil($followings->get_total() / $request->get_param( 'per_page' ))
            );
        } else {
            return array (
                'followings' => [],
                'total' => 0,
                'pages' => 0
            );
        }
    }

    return new WP_Error( 'social_error', 'User Id is required to complete request and it cannot be 0', array( 'status' => 404 ) );
}


function author_register_followerstat()
{
    register_rest_field( array('user'),
        'followership_stat',
        array(
            'get_callback'    => 'gs_fu_getStats',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

add_action( 'rest_api_init', 'author_register_followerstat' );

function gs_fu_getStats($object, $field_name, $request)
{    
    return bp_follow_total_follow_counts(array( 'user_id' => $object[ 'id' ]));
}

function author_register_followership()
{
    register_rest_field( array('user'),
        'followership',
        array(
            'get_callback'    => 'gs_fu_followsUser',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

add_action( 'rest_api_init', 'author_register_followership' );

function gs_fu_followsUser($object, $field_name, $request)
{
    $user = get_current_user_id();
    if (0 == $user || $object[ 'id' ] == $user) {
        return null;
    }
    return bp_follow_is_following( array( 'leader_id' => $object[ 'id' ], 'user_id' => $user) );
}

