<?php

function post_get_comment_count($object, $field_name, $request)
{
    $comments_count = wp_count_comments( $object[ 'id' ] );
    return array(
        "moderated" => $comments_count->moderated,
        "approved" => $comments_count->approved,
        "spam" => $comments_count->spam,
        "trash" => $comments_count->trash,
        "total" => $comments_count->total_comments
    );
}

function post_register_comment_count()
{
    register_rest_field( array('post','contests','submissions'),
        'comment_count',
        array(
            'get_callback'    => 'post_get_comment_count',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

add_action( 'rest_api_init', 'post_register_comment_count' );

function get_comment_response($response, $comment, $request)
{    
    if ($request->get_method() == "GET" && is_array($request->get_param('parent')) && $request->get_param('parent')[0] == 0  ) {
            $replies = get_comments(
                array (
                    'parent'=> $comment->comment_ID,
                    'number'=>2
                )
            );

            $replies_arr = array();

            foreach($replies as $reply) {
                $data = (array)$reply;
                $data['user_avatar'] = getAvatar($reply->user_id);
                 $replies_arr[] = $data;
            }
                
            $response->data['replies'] = $replies_arr;
            $count = get_comments(
                array (
                    'parent'=> $comment->comment_ID,
                    'count'=>true
                )
            );
            $response->data['replies_count'] = $count;
    }

    $response->data['author_avatar'] = getAvatar($response->data['author']);

    return  $response;
}

add_filter( 'rest_prepare_comment', 'get_comment_response', 10, 3 );

add_filter( 'rest_allow_anonymous_comments', '__return_true' );
