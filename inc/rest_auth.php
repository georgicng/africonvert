<?php

function jwt_auth_set_ttl( $default ) { 
    $ttl = get_option('jwt_auth_ttl', DAY_IN_SECONDS);
    return $default + $ttl;
}

add_filter( 'jwt_auth_expire', 'jwt_auth_set_ttl' );

function jwt_auth_ttl_filter( $token ) {
    
    global $ttl;

    if ( $token['exp'] )
        $ttl = $token['exp'];

    return $token;
}

add_filter( 'jwt_auth_token_before_sign', 'jwt_auth_ttl_filter' );


function jwt_auth_user_filter( $data ) {
    global $ttl;
    if ( $data['token'] ) {
        $data['ttl'] = $ttl;
        $data['user_data'] = getUserProfile($data['user_email']);
        unset($data['user_email']);
        unset($data['user_nicename']);
        unset($data['user_display_name']);
    }
        
    return $data;
}

add_filter( 'jwt_auth_token_before_dispatch', 'jwt_auth_user_filter' );
