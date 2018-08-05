<?php

add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/users/(?P<id>\d+)/activities', array(
    'methods' => 'GET',
    'callback' => 'activity_get_activities',
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
            ),
            'max' => array(
                'default' => false,
                'validate_callback' => function ($param, $request, $key) {
                    return is_numeric( $param ) || is_bool($param);
                }
            ),
            /*'sort' => array(
                'default' => 'DESC',
                'validate_callback' => 'is_string'
            )
            'object' => array(
                'default' => false,
                'validate_callback' => 'is_string'
            )*/
        ),
        'permission_callback' => function ($request) {
            return true; //$request->get_param( 'id' ) == get_current_user_id();
        },
    ) );
} );

/**
	* Returns an Array with all activities
	* @param int pages: number of pages to display (default unset)
	* @param int offset: number of entries per page (default 10 if pages is set, otherwise unset)
	* @param int limit: number of maximum results (default 0 for unlimited)
	* @param String sort: sort ASC or DESC (default DESC)
	* @param String comments: 'stream' for within stream display, 'threaded' for below each activity item (default unset)
	* @param Int userid: userID to filter on, comma-separated for more than one ID (default unset)
	* @param String component: object to filter on e.g. groups, profile, status, friends (default unset)
	* @param String type: action to filter on e.g. activity_update, profile_updated (default unset)
	* @param int itemid: object ID to filter on e.g. a group_id or forum_id or blog_id etc. (default unset)
	* @param int secondaryitemid: secondary object ID to filter on e.g. a post_id (default unset)
	* @return array activities: an array containing the activities
*/

function activity_get_activities(WP_REST_Request $request)
{
    $params = [];
    $id = $request->get_param( 'id' );
    $user_id = bp_get_following_ids( array('user_id' => $id) ); 
    if (is_string($user_id)){
        $user_id .= ','.$id;  
    }  else {
        $user_id = (string) $id;  
    }           
    $params['page']=$request->get_param( 'page' );
    $params['per_page']=$request->get_param( 'per_page' );
    $params['max']=$request->get_param( 'max' );
    $params['filter'] = array('user_id' => $user_id);
    $params['offset']=$request->get_param( 'offset' );
    $params['sort']=$request->get_param( 'sort' );
    $params['display_comments'] = false;
    $params['count_total'] = true;
            
    /*add code to return profile image of user       
    global $activities_template;
    if (bp_has_activities($params)) {
           return $activities_template->activities;
    } else {
        return [];
    }*/

     $resultset = BP_Activity_Activity::get($params);

      if (count($resultset['activities']) > 0 ) {
        //$response = array();
        foreach($resultset['activities'] as $item){
            $conv = (array) $item;
            $conv['avatar'] = getAvatar($item->user_id);
            $response[] = $conv;           
        }
        return array(
            "activities" => $response, 
            "total" => $resultset['total'], 
            "pages" => ceil($resultset['total'] / $params['per_page'])
        );
      } else {
          array(
            "activities" => [], 
            "total" => 0, 
            "pages" => 0
        );
      }
            
  //echo '<pre>';print_r($oReturn);echo '</pre>';
    return new WP_Error('activity_error', "cannot load activities");
}
 
