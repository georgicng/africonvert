<?php

function default_comments_on( $data ) {
    $cpt = array (
        "contests",
        "submissions"
    );
    
    if( in_array($data['post_type'], $cpt) ) {
        $data['comment_status'] = 'open';
    }

    return $data;
}

add_filter( 'wp_insert_post_data', 'default_comments_on' );

function cpt_acf_profile_avatar($avatar, $id_or_email, $size, $default, $alt)
{
    // Get user by id or email
    if (is_numeric( $id_or_email )) {
        $id   = (int) $id_or_email;
        $user = get_user_by( 'id', $id );
    } elseif (is_object( $id_or_email )) {
        if (! empty( $id_or_email->user_id )) {
            $id   = (int) $id_or_email->user_id;
            $user = get_user_by( 'id', $id );
        }
    } else {
        $user = get_user_by( 'email', $id_or_email );
    }
    if (! $user) {
        return $avatar;
    }
    // Get the user id
    $user_id = $user->ID;
    // Get the file id
    $image_id = get_user_meta($user_id, 'cpt_acf_profile_avatar', true); // CHANGE TO YOUR FIELD NAME
    // Bail if we don't have a local avatar
    if (! $image_id) {
        return $avatar;
    }
    // Get the file size
    $image_url  = wp_get_attachment_image_src( $image_id, 'thumbnail' ); // Set image size by name
    // Get the file url
    $avatar_url = $image_url[0];
    // Get the img markup
    $avatar = '<img alt="' . $alt . '" src="' . $avatar_url . '" class="avatar avatar-' . $size . '" height="' . $size . '" width="' . $size . '"/>';
    // Return our new avatar
    return $avatar;
}

/*

add_filter('get_avatar', 'cpt_acf_profile_avatar', 10, 5);

add_filter( 'acf/rest_api/item_permissions/update', function( $permission, $request, $type ) {
    if ( 'user' == $type && method_exists( $request, 'get_param' ) && get_current_user_id() == $request->get_param( 'id' ) ) {
        return true;
    }
    return $permission;
}, 10, 3 );

*/

/* Add a new activity stream item for when a user start following somebody */
function af_start_following_activity($follow)
{
    global $bp;
    if (!function_exists( 'bp_activity_add' )) {
        return false;
    }
    $user_following = bp_loggedin_user_id();
    $user_following_link =  bp_core_get_userlink( $user_following );
    $user_followed_id =$follow[0]->follower_id;//we use server method otherwise it doesn't work if somebody use the follow button from the all members page instead of the single user page
    $userlink = bp_core_get_userlink( $user_followed_id );
 
    bp_activity_add( array(
    'user_id' => $user_following,
    'action' => sprintf( __( '%1$s started following %2$s', 'buddypress' ), $user_following_link, $userlink),
    'component' => 'activity',
    'type' => 'new_following',
    'item_id' => $user_followed_id
    ) );
}
add_action( 'bp_follow_start_following', 'af_start_following_activity' );
 
/* Add a new activity stream item for when a user stop following somebody */
function af_stop_follow_activity($follow)
{
    global $bp;
    if (!function_exists( 'bp_activity_add' )) {
        return false;
    }
    $user_following = bp_loggedin_user_id();
    $user_following_link =  bp_core_get_userlink( $user_following );
    $user_followed_id =$follow[0]->follower_id;
    $userlink = bp_core_get_userlink( $user_followed_id );
 
    bp_activity_add( array(
    'user_id' => $user_following,
    'action' =>  sprintf( __( '%1$s stopped following %2$s', 'buddypress' ), $user_following_link, $userlink),
    'component' => 'activity',
    'type' => 'stop_following',
    'item_id' => $user_followed_id
    ) );
}
add_action( 'bp_follow_stop_following', 'af_stop_follow_activity' );

function custom_contest_register_activity_actions()
{
    // Your plugin is creating a custom BuddyPress component
    $component_id = buddypress()->activity->id;
    // You can also use one of the BuddyPress component
    // $component_id = buddypress()->activity->id;
 
    bp_activity_set_action(
        $component_id,
        'enter_contest',
        __( 'Entered a contest', 'afriflow' ),
        'submission_format_activity_action',
        __( 'Submissions', 'afriflow' ),
        array( 'activity', 'member' )
    );

    bp_activity_set_action(
        $component_id,
        'vote_contest',
        __( 'Voted  for a contest', 'afriflow' ),
        'vote_format_activity_action',
        __( 'Votes', 'afriflow' ),
        array( 'activity', 'member' )
    );

    bp_activity_set_action(
        $component_id,
        'vote_like',
        __( 'Liked a contest entry', 'afriflow' ),
        'like_format_activity_action',
        __( 'Contest Likes', 'afriflow' ),
        array( 'activity', 'member' )
    );

    bp_activity_set_action(
        $component_id,
        'vote_dislike',
        __( 'Disliked a contest entry', 'afriflow' ),
        'like_format_activity_action',
        __( 'Contest Dislikes', 'afriflow' ),
        array( 'activity', 'member' )
    );

     bp_activity_set_action(
        $component_id,
        'new_following',
       __( 'Started following a user', 'afriflow' ),
        'follow_format_activity_action',
        __( 'Follow', 'afriflow' ),
        array( 'activity', 'member' )
    );

     bp_activity_set_action(
        $component_id,
        'stop_following',
        __( 'Stopped following a user', 'afriflow' ),
        'follow_format_activity_action',
        __( 'Unfollow', 'afriflow' ),
        array( 'activity', 'member' )
    );
}
add_action( 'bp_register_activity_actions', 'custom_contest_register_activity_actions' );

function follow_format_activity_action($action = '', $activity = null)
{
        // Bail if not a rendez vous activity posted in a group
    if (buddypress()->activity->id != $activity->component || empty( $action )) {
        return $action;
    }
        $subject = getActivityUserLink($activity->user_id);
        $object = getActivityUserLink($activity->item_id);
    if ($activity->type == "new_following") {
        $action = ' ' . sprintf( __( '%s started following %s', 'afriflow' ), $subject, $object );
    } elseif ($activity->type == "stop_following") {
        $action = ' ' . sprintf( __( '%s stopped following %s', 'afriflow' ), $subject, $object );
    }
        return $action;
}

function like_format_activity_action($action = '', $activity = null)
{
        
    if (buddypress()->activity->id != $activity->component || empty( $action )) {
        return $action;
    }
    $subject = getActivityUserLink($activity->user_id);
    $object = getActivityEntryLink($activity->secondary_item_id);
    if ($activity->type == "vote_like") {
        $action .= ' ' . sprintf( __( '%s liked %s', 'afriflow' ), $subject, $object );
    } elseif ($activity->type == "vote_dislike") {
        $action .= ' ' . sprintf( __( '%s disliked %s', 'afriflow' ), $subject, $object );
    }
    return $action;
}

function vote_format_activity_action($action = '', $activity = null)
{
        
    if (buddypress()->activity->id != $activity->component || empty( $action )) {
        return $action;
    }
    $subject = getActivityUserLink($activity->user_id);
    $predicate = getActivityContestLink($activity->item_id, 'vote');
    $object = getActivityEntryLink($activity->secondary_item_id);
    if ($activity->type == "vote_contest") {
        $action .= ' ' . sprintf( __( '%s voted for %s in the %s contest', 'afriflow' ), $subject, $object, $predicate );
    }
    return $action;
}

function submission_format_activity_action($action = '', $activity = null)
{
        
    if (buddypress()->activity->id != $activity->component || empty( $action )) {
        return $action;
    }
    $subject = getActivityUserLink($activity->user_id);
    $object = getActivityContestLink($activity->item_id, 'stage');
    if ($activity->type == "enter_contest") {
        $action .= ' ' . sprintf( __( '%s entered %s contest with an entry', 'afriflow' ), $subject, $object);
    }
    return $action;
}

function follow_reformat_new_follower_notification($output, $total_items, $link, $text, $item_id, $secondary_item_id)
{
       
    if ($total_items == 1) {
            $link = getUserLink( $item_id  );
            $user = '<a href="' . $link . '">' . getUsername( $item_id ) . '</a>';
            $text = sprintf( __( '%s is now following you', 'bp-follow' ), $user );
            return $text;
    } else {
        $link = getUserLink( get_current_user_id()  );
        $users = '<a href="' . $link . '">' . $total_items . '</a>';
        $text = sprintf( __( '%s more users are now following you', 'bp-follow' ), $users );
        return $text;
    }
}
    add_filter( 'bp_follow_new_followers_notification', 'follow_reformat_new_follower_notification', 10, 6 );

function xprofile_reformat_new_avatar($action, $activity)
{
        
    $userlink = getActivityUserLink( $activity->user_id );
    if ($activity->user_id == get_current_user_id()) {
        return $action   = sprintf( __( '%s changed your profile picture', 'buddypress' ), $userlink );
    } else {
        return $action   = sprintf( __( '%s changed their profile picture', 'buddypress' ), $userlink );
    }
}
    add_filter( 'bp_xprofile_format_activity_action_new_avatar', 'xprofile_reformat_new_avatar', 10, 2 );

function xprofile_reformat_profile_update($action, $activity)
{
        
    $profile_link = getActivityUserLink( $activity->user_id );
    if ($activity->user_id == get_current_user_id()) {
        return $action   = sprintf( __( '%s updated your profile', 'buddypress' ), $profile_link );
    } else {
        return $action= sprintf( __( "%s's profile was updated", 'buddypress' ), $profile_link  );
    }
}
    add_filter( 'bp_xprofile_format_activity_action_updated_profile', 'xprofile_reformat_profile_update', 10, 2 );

function modifiedUserPermissionCallback($request)
{
    $user = get_user_by( 'id', $request['id'] );
    
    if (is_wp_error( $user )) {
            return $user;
    }

        $types = get_post_types( array( 'show_in_rest' => true ), 'names' );

    if (get_current_user_id() === $user->ID) {
        return true;
    }

    if ('edit' === $request['context'] && ! current_user_can( 'list_users' )) {
        return new WP_Error( 'rest_user_cannot_view', __( 'Sorry, you are not allowed to list users.' ), array( 'status' => rest_authorization_required_code() ) );
    } elseif (! count_user_posts( $user->ID, $types ) && ! current_user_can( 'publish_posts' )) {
        return new WP_Error( 'rest_user_cannot_view', __( 'Sorry, you are not allowed to list users.' ), array( 'status' => rest_authorization_required_code() ) );
    }

        return true;
}

 add_filter( 'rest_pre_dispatch', 'getEndpoints', 10, 3 );

function getEndpoints($response, $server, $request)
{
    $route = '/wp/v2/users/(?P<id>\d+)';
    $method = $request->get_method();
    $path   = $request->get_route();
    $match = preg_match( '@^' . $route . '$@i', $path, $args );

    if ($match && $method == 'GET') {
         $endpoints = $server->get_routes();

         $handlers = $endpoints["/wp/v2/users/(?P<id>[\\d]+)"];

        if (! empty ($handlers)) {
            foreach ($handlers as $handler) {
                $callback  = $handler['callback'];
                $response = null;

                // Fallback to GET method if no HEAD method is registered.
                $checked_method = $method;
                if ('HEAD' === $method && empty( $handler['methods']['HEAD'] )) {
                    $checked_method = 'GET';
                }
                if (empty( $handler['methods'][ $checked_method ] )) {
                    continue;
                }

                if (! is_callable( $callback )) {
                    $response = new WP_Error( 'rest_invalid_handler', __( 'The handler for the route is invalid' ), array( 'status' => 500 ) );
                }

                if (! is_wp_error( $response )) {
                    // Remove the redundant preg_match argument.
                    unset( $args[0] );

                    $request->set_url_params( $args );
                    $request->set_attributes( $handler );

                    $defaults = array();

                    foreach ($handler['args'] as $arg => $options) {
                        if (isset( $options['default'] )) {
                            $defaults[ $arg ] = $options['default'];
                        }
                    }

                    $request->set_default_params( $defaults );

                    $check_required = $request->has_valid_params();
                    if (is_wp_error( $check_required )) {
                        $response = $check_required;
                    } else {
                        $check_sanitized = $request->sanitize_params();
                        if (is_wp_error( $check_sanitized )) {
                            $response = $check_sanitized;
                        }
                    }
                }

                /**
                    * Filters the response before executing any REST API callbacks.
                    *
                    * Allows plugins to perform additional validation after a
                    * request is initialized and matched to a registered route,
                    * but before it is executed.
                    *
                    * Note that server filter will not be called for requests that
                    * fail to authenticate or match to a registered route.
                    *
                    * @since 4.7.0
                    *
                    * @param WP_HTTP_Response $response Result to send to the client. Usually a WP_REST_Response.
                    * @param WP_REST_Server   $handler  ResponseHandler instance (usually WP_REST_Server).
                    * @param WP_REST_Request  $request  Request used to generate the response.
                    */
                $response = apply_filters( 'rest_request_before_callbacks', $response, $handler, $request );

                if (! is_wp_error( $response )) {
                        $permission = modifiedUserPermissionCallback($request);

                    if (is_wp_error( $permission )) {
                        $response = $permission;
                    } elseif (false === $permission || null === $permission) {
                        $response = new WP_Error( 'rest_forbidden', __( 'Sorry, you are not allowed to do that.' ), array( 'status' => 403 ) );
                    }
                }

                if (! is_wp_error( $response )) {
                    /**
                        * Filters the REST dispatch request result.
                        *
                        * Allow plugins to override dispatching the request.
                        *
                        * @since 4.4.0
                        * @since 4.5.0 Added `$route` and `$handler` parameters.
                        *
                        * @param bool            $dispatch_result Dispatch result, will be used if not empty.
                        * @param WP_REST_Request $request         Request used to generate the response.
                        * @param string          $route           Route matched for the request.
                        * @param array           $handler         Route handler used for the request.
                        */
                    $dispatch_result = apply_filters( 'rest_dispatch_request', null, $request, $route, $handler );

                    // Allow plugins to halt the request via server filter.
                    if (null !== $dispatch_result) {
                        $response = $dispatch_result;
                    } else {
                        $response = call_user_func( $callback, $request );
                    }
                }

                /**
                    * Filters the response immediately after executing any REST API
                    * callbacks.
                    *
                    * Allows plugins to perform any needed cleanup, for example,
                    * to undo changes made during the {@see 'rest_request_before_callbacks'}
                    * filter.
                    *
                    * Note that server filter will not be called for requests that
                    * fail to authenticate or match to a registered route.
                    *
                    * Note that an endpoint's `permission_callback` can still be
                    * called after server filter - see `rest_send_allow_header()`.
                    *
                    * @since 4.7.0
                    *
                    * @param WP_HTTP_Response $response Result to send to the client. Usually a WP_REST_Response.
                    * @param WP_REST_Server   $handler  ResponseHandler instance (usually WP_REST_Server).
                    * @param WP_REST_Request  $request  Request used to generate the response.
                    */
                $response = apply_filters( 'rest_request_after_callbacks', $response, $handler, $request );

                    

                return $response;
            }
        }
    }

    return false;
}


function save_contest_project_meta( $post_id, $post, $update ) {

    /*
     * In production code, $slug should be set only once in the plugin,
     * preferably as a class property, rather than in each function that needs it.
     */
    $post_type = get_post_type($post_id);

    // If this isn't a 'book' post, don't update it.
    if ( "contests" != $post_type ) return;

    // - Check if wistia project has been created, else Create wistia project and store id in acf wistia_id --- 
    // post name of project to https://api.wistia.com/v1/projects.json to create project.

    $wistia_api = get_field('wistia_project_id', $post_id);

    if( $wistia_api ) {
        return;
    } else {
        $name = get_the_title($post_id);
        $token = get_option('wistia_admin_api_password');
        $response = wp_remote_post(
            'https://api.wistia.com/v1/projects.json',
            array (
                'body' => array (
                    'name' => $name,
                    'api_password' => $token
                )
            )
        );
        error_log (wp_remote_retrieve_response_code( $response ));
        if (wp_remote_retrieve_response_code( $response ) >= 200 && wp_remote_retrieve_response_code( $response ) < 300){
            $data = json_decode(wp_remote_retrieve_body( $response ));
            error_log (var_dump($data));
            update_field('wistia_project_id', $data->hashedId, $post_id);
        }

        
    }

}
//add_action( 'save_post', 'save_contest_project_meta', 10, 3 );

function my_load_field($field) {

	$field['readonly'] = 1;
	return $field;
}

//add_filter("acf/load_field/name=wistia_project_id", "my_load_field");

add_filter('acf/validate_value/name=entry_closes', 'validate_entry_end_date_func', 10, 4);
function validate_entry_end_date_func($valid, $value, $field, $input) {
  if (!$valid) {
    return $valid;
  }
  $start_key = 'field_586bbe992ba97';
  $start_value = $_POST['acf'][$start_key];
  $end_value = $value;
  if ($end_value <= $start_value) {
   $valid = 'entry end date must be greater than entry start date';
  }
  return $valid;
}

add_filter('acf/validate_value/name=voting_opens', 'validate_voting_start_date_func', 10, 4);
function validate_voting_start_date_func($valid, $value, $field, $input) {
  if (!$valid) {
    return $valid;
  }
  $start_key = 'field_586bbebf2ba98';
  $start_value = $_POST['acf'][$start_key];
  $end_value = $value;
  if ($end_value <= $start_value) {
   $valid = 'voting start value must be greater than entry close date';
  }
  return $valid;
}

add_filter('acf/validate_value/name=voting_closes', 'validate_voting_end_date_func', 10, 4);
function validate_voting_end_date_func($valid, $value, $field, $input) {
  if (!$valid) {
    return $valid;
  }
  $start_key = 'field_586bbdba2ba95';
  $start_value = $_POST['acf'][$start_key];
  $end_value = $value;
  if ($end_value <= $start_value) {
   $valid = 'voting end value must be greater than voting start date';
  }
  return $valid;
}

add_filter('wp_get_attachment_image_src', 'set_default_thumbnail', 10, 4);
function set_default_thumbnail($image, $attachment_id, $size, $icon) {
    if ( get_post_type( get_the_ID() ) == 'submissions' ) {
        return $image;
    }
    if(empty($image)){        
	    $image= wp_get_attachment_image_src( get_alt_image_id(), $size, $icon );
    }
    return $image;
}

function rest_widget_set_post(){
    //error_log(json_encode($_SERVER));
	if (isset($_SERVER['HTTP_REFERER'])){
		$route = '/sidebars/(?P<id>[\w-]+)';
		$referer = '/(?P<type>[\w-]+)/(?P<id>[\d-]+)';
		$match = preg_match( '@' . $route . '$@i', $_SERVER['REQUEST_URI'], $args );
		if ( $match ) {
			$match = preg_match( '@' . $referer . '$@i', $_SERVER['HTTP_REFERER'], $argz);
			if ( $match ) {                
                //error_log(json_encode($argz));
                global $post;
                global $wp_query;
                //error_log(json_encode($post)); 
                //error_log(json_encode($wp_query)); 
                $wp_query = new WP_Query(array(
                    'p' => intval($argz['id'])
                ));
                $post = get_post(intval($argz['id']));
                //error_log(json_encode($post));
				setup_postdata( $post );
			}			
		}	
	}
}  
add_action('rest_api_init', 'rest_widget_set_post');

function af_site_widgets() {
    register_widget( 'Af_Category_Tabs' );
    register_widget( 'Af_Category' );
    register_widget( 'Af_Recent_Comments' );
    register_widget( 'Af_Related_Posts' );
  
}
add_action( 'widgets_init', 'af_site_widgets' );

function custom_rewrite_basic() {
    //add_rewrite_rule('^members/me/*', 'index.php', 'top');
    add_rewrite_rule('^wp-json/*', 'index.php?p=2', 'top');
  }
  add_action('init', 'custom_rewrite_basic');

  add_filter( 'page_rewrite_rules', 'wpse7243_page_rewrite_rules' );
  function wpse7243_page_rewrite_rules( $rewrite_rules )
  {
      // The most generic page rewrite rule is at end of the array
      // We place our rule one before that
      end( $rewrite_rules );
      $last_pattern = key( $rewrite_rules );
      $last_replacement = array_pop( $rewrite_rules );
      $rewrite_rules +=  array(
          'about/$' => 'index.php?page_id=2',
          $last_pattern => $last_replacement,
      );
      return $rewrite_rules;
  }

function gai_acf_trigger_collation( $post_id ) {
    
    // bail early if no ACF data
    if( empty($_POST['acf']) ) {
        
        return;
        
    }
    
    
    // array of field values
    $fields = $_POST['acf'];


    // specific field value
    $field = $_POST['acf']['field_58a720c108135'];
    $pre = get_field('stage', $post_id);
    if($field == "Post-Vote" && $pre == "Vote"){
        collateContest( $post_id );
    } 
    if($field == "Complete" && $pre == "Result"){
        $objects = $cpt_onomy->get_objects_in_term( $post_id, 'contests' );
        
        $args = array(
           'post_type' => 'movies',
           'post__in' => $objects,
           'meta_key'   => 'winner',
           'meta_value' => true
        );

        $query = new WP_Query( $args );
        if ( $query->have_posts() ) {
            // The 2nd Loop
            while ( $query->have_posts() ) {
                $query->the_post();
                scheduleWinningEmail( $query->post->ID );
            }
        
            // Restore original Post Data
            wp_reset_postdata();
        }
    }  

    
}

add_action('acf/save_post', 'gai_acf_trigger_collation', 1);

function gai_select_winner() {
    
        if ( ! current_user_can( 'manage_options' ) && ( ! wp_doing_ajax() ) ) {
            wp_die( __( 'You are not allowed to access this part of the site' ) );
        }
        
        $winner = get_post_meta($_GET['post'], 'winner', true );
        if($winner){
            wp_die( __( 'You cannot have two winner' ) );
        }

        $candidate = get_post_meta($_GET['winner'], 'candidate', true );
        if(!$candidate){
            wp_die( __( 'Selected winner is not a candidate' ) );
        }

        update_post_meta( $_GET['post'], 'winner', $_GET['winner']);
        update_post_meta( $_GET['winner'], 'winner', true);
        wp_redirect(admin_url( 'post.php?action=edit&post=' . $_GET['post'] ));
    }
    add_action( 'admin_post_select_winner', 'gai_select_winner' );

    
function custom_post_status(){
	register_post_status( 'rejected', array(
		'label'                     => _x( 'Rejected', 'post' ),
		'public'                    => true,
		'exclude_from_search'       => false,
		'show_in_admin_all_list'    => true,
		'show_in_admin_status_list' => true,
		'label_count'               => _n_noop( 'Rejected <span class="count">(%s)</span>', 'Rejected <span class="count">(%s)</span>' ),
	) );
}
add_action( 'init', 'custom_post_status' );

add_action('admin_footer-post.php', 'wpb_append_post_status_list');
function wpb_append_post_status_list(){
    global $post;
    $complete = '';
    $label = '';
    if($post->post_type == 'submissions'){
        if($post->post_status == 'rejected'){
            $complete = ' selected="selected"';
            $label = '<span id="post-status-display"> Rejected</span>';
        }
        echo '
        <script>
        jQuery(document).ready(function($){
        $("select#post_status").append("<option value=\"rejected\" '.$complete.'>Rejected</option>");
        $(".misc-pub-section label").append("'.$label.'");
        });
        </script>
        ';
    }
}

function on_all_status_transitions( $new_status, $old_status, $post ) {
	if ($post->post_type != "submissions")
		return;
	
	if (  $old_status == "draft" && $new_status == "publish") 
		scheduleValidationEmail($post->ID, true);
	
	if (  $old_status == "draft" && $new_status == "rejected") 
		scheduleValidationEmail($post->ID, false);
}
add_action(  'transition_post_status',  'on_all_status_transitions', 10, 3 );

// Render your admin menu outside the class
function gai_adminMenu()
{
	add_submenu_page( 'edit.php?post_type=contests', __( 'Contest Stat', 'gai' ), __( 'Contest Stat', 'gai' ), 'manage_options', 'contests_result', 'render_admin_page' );
	
}

// Create your menu outside the class
add_action( 'admin_menu', 'gai_adminMenu' );

// Render your page outside the class
function render_admin_page(){
		
    ?>
	<div class="wrap">
		<h2>Contest Stats</h2>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<form method="post">
							<?php
							 $list_table = new Contest_Stat();
							 $list_table->prepare_items();    
							 $list_table->display(); ?>
						</form>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	</div>
<?php   
}

function gai_contest_results_meta( $post ) {
	$stage = get_field("stage", $post->ID);
	if ($stage == "Result" || $stage == "Complete"){
		add_meta_box( 
			'contest-result-meta',
			__( 'Contest Results' ),
			'render_contest_results_meta',
			'contests',
			'normal',
			'default'
		);
	}
    
}

add_action( 'add_meta_boxes_contests', 'gai_contest_results_meta' );

function render_contest_results_meta(){
    ?>
	<div class="wrap">
		<h2>Contest Results</h2>

		<div id="poststuff">
			<div id="post-body" class="metabox-holder columns-2">
				<div id="post-body-content">
					<div class="meta-box-sortables ui-sortable">
						<form method="post">
							<?php
							 $list_table = new Submission_Stat();
							 $list_table->prepare_items();    
							 $list_table->display(); ?>
						</form>
					</div>
				</div>
			</div>
			<br class="clear">
		</div>
	</div>
<?php   
}