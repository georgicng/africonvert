<?php

function cpt_contest(WP_REST_Request $request)
{
    global $cpt_onomy;
    $term = $cpt_onomy->get_term($request->get_param('id'), 'contests');

    if (empty($term)) {
        return new WP_Error('category_error', 'category does not exist', array( 'status' => 404 ));
    }

    return $term;
}

add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/submission_category/(?P<id>\d+)', array(
        'methods' => 'GET',
        'callback' => 'cpt_contest',
    ));
});

function cpt_contests(WP_REST_Request $request)
{
    $terms = get_terms('contests', 'orderby=count&hide_empty=0');

    if (empty($terms)) {
        return new WP_Error('category_error', 'No record found', array( 'status' => 404 ));
    }

    return $terms;
}

add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/submission_category', array(
        'methods' => 'GET',
        'callback' => 'cpt_contests',
    ));
});

function cpt_allowed_query_var($valid_vars)
{
    $valid_vars = array_merge($valid_vars, array( 'type' ));
    return $valid_vars;
}
add_filter('rest_query_vars', 'cpt_allowed_query_var', 10, 3);

//http://wordpress.stackexchange.com/questions/226589/filtering-multiple-custom-fields-with-wp-rest-api-2
function cpt_contest_query($args, $request)
{
    if (isset($request['type']) && ! empty($request['type'])) {
        $today = date("Ymd");
        switch ($request['type']) {
            case "submit":
                //$args = array_merge( $args, array('meta_key' => 'stage', 'meta_value' => 'Submit') );
                 $meta = array(
                    'relation' => 'AND', //changed to AND from OR
                    array(
                        'key' => 'stage',
                        'value' => 'Submit'
                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'entry_opens',
                            'value' => $today,
                            'compare' => '<=',
                            'type' => 'DATE'
                        ),
                        array(
                            'key' => 'entry_closes',
                            'value' => $today,
                            'compare' => '>=',
                            'type' => 'DATE'
                        )
                    ),
                );
                $args['meta_query'] = $meta;
                break;
            case "vote":
                //$args = array_merge( $args, array('meta_key' => 'stage', 'meta_value' => 'Vote') );
                $meta = array(
                    'relation' => 'AND', // Optional, defaults to "AND" -- changed from OR
                    array(
                        'key' => 'stage',
                        'value' => 'Vote'
                    ),
                    array(
                        'relation' => 'AND',
                        array(
                            'key' => 'voting_opens',
                            'value' => $today,
                            'compare' => '<=',
                            'type' => 'DATE'
                        ),
                        array(
                            'key' => 'voting_closes',
                            'value' => $today,
                            'compare' => '>=',
                            'type' => 'DATE'
                        )
                    )
                );
                $args['meta_query'] = $meta;
                break;
            case "past":
                $args = array_merge($args, array('meta_key' => 'stage', 'meta_value' => 'Complete'));
                break;
        }
        unset($args['type']);
    }

    return $args;
}
add_filter('rest_contests_query', 'cpt_contest_query', 10, 3);

function cpt_rest_prepare_contests($data, $post, $request)
{
    $_data = $data->data;
    
    if (!empty($request['type'])) {
        $today = date_create(date("Ymd"));
        switch ($request['type']) {
            case "submit":
                $open = get_post_meta($post->ID, 'entry_opens', true);
                $close = get_post_meta($post->ID, 'entry_closes', true);
                $_data['countdown'] = getCountdown($open, $close);
                $_data['stage'] = 'submission';
                $_data['user_entry'] = get_user_entry($_data['id']);
                $_data['entry_count'] =get_contest_entry_count($_data['id']);
                buildValidation($post, $_data);
                break;
            case "vote":
                $open = get_post_meta($post->ID, 'voting_opens', true);
                $close = get_post_meta($post->ID, 'voting_closes', true);
                $_data['countdown'] = getCountdown($open, $close);
                $_data['stage'] = 'voting';
                break;
            case "past":
                $_data['stage'] = 'completed';
                break;
        }
    }
    
    $thumbnail_id = get_post_thumbnail_id($post->ID);
    $thumbnail = wp_get_attachment_image_src($thumbnail_id);
    $cover = wp_get_attachment_image_src($thumbnail_id, 'cover');
    $full = wp_get_attachment_image_src($thumbnail_id, 'full');
    $_data['featured_image_thumbnail_url'] = $thumbnail[0];
    $_data['featured_image_cover_url'] = $cover[0];
    $_data['featured_image_url'] = $full[0];
    
    $data->data = $_data;

    return $data;
}
add_filter('rest_prepare_contests', 'cpt_rest_prepare_contests', 10, 3);

//WP_HTTP_Response $response, WP_REST_Server $handler, WP_REST_Request $request
function cpt_rest_validate_contests($response, $handler, $request)
{
    $route = $request->get_route();

    $pattern = "/contests\/(?P<id>\d+)/";

    $match = preg_match($pattern, $route, $args);
    
    if (! $match) {
        return $response;
    }

    if (!isset($request['type'])) {
        return new WP_Error(
            'no-stage',
            'Please specify stage',
            array( 'status' => 501, )
        );
    }

    $stage = get_field('stage', $args['id']);
    if (($stage == 'Vote' && $request['type'] != 'vote')
        || ($stage == 'Submit' && $request['type'] != 'submit')
        || ($stage == 'Complete' && $request['type'] != 'complete')
    ) {
        return new WP_Error(
            'invalid-stage',
            'Wrong stage indicated',
            array( 'status' => 501)
        );
    } else {
        return $response;
    }
}
add_filter('rest_pre_dispatch', 'cpt_rest_validate_contests', 999, 3);
//add_filter( 'rest_request_before_callbacks', 'cpt_rest_validate_contests', 10, 3 );
