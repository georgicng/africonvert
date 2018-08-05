<?php
function get_latest_notification_count()
{
    $user_id = get_current_user_id();
    $since = get_user_meta( $user_id, "_notification_last_seen", true);
    if ($since) {
        $arg = array(
            'user_id' => $user_id,
            'date_query' => array(
                array(
                    'after' => $since,
                ),
            )
        );
        return BP_Notifications_Notification::get_total_count( $args );
    }
    
    return false;
}

function bp_get_notification_description($notification) {
		$bp = buddypress();
		// Callback function exists.
		if ( isset( $bp->{ $notification->component_name }->notification_callback ) && is_callable( $bp->{ $notification->component_name }->notification_callback ) ) {
			$description = call_user_func( $bp->{ $notification->component_name }->notification_callback, $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1, 'string', $notification->id );

		// @deprecated format_notification_function - 1.5
		} elseif ( isset( $bp->{ $notification->component_name }->format_notification_function ) && function_exists( $bp->{ $notification->component_name }->format_notification_function ) ) {
			$description = call_user_func( $bp->{ $notification->component_name }->format_notification_function, $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1 );

		// Allow non BuddyPress components to hook in.
		} else {

			/** This filter is documented in bp-notifications/bp-notifications-functions.php */
			$description = apply_filters_ref_array( 'bp_notifications_get_notifications_for_user', array( $notification->component_action, $notification->item_id, $notification->secondary_item_id, 1, 'string', $notification->component_action, $notification->component_name, $notification->id ) );
		}

		/**
		 * Filters the full-text description for a specific notification.
		 *
		 * @since 1.9.0
		 * @since 2.3.0 Added the `$notification` parameter.
		 *
		 * @param string $description  Full-text description for a specific notification.
		 * @param object $notification Notification object.
		 */
		return apply_filters( 'bp_get_the_notification_description', $description, $notification );
	}

add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/users/(?P<id>\d+)/notifications', array(
    'methods' => 'GET',
    'callback' => 'notifications_get_notifications',
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
            'sort' => array(
                'default' => 'DESC',
                'validate_callback' => function ($param, $request, $key) {
                    return is_string( $param );
                }
            )
        ),
        'permission_callback' => function ($request) {
            
            write_log(get_current_user_id());
            return ($request->get_param( 'id' ) == get_current_user_id());
            
        },
    ) );
} );

/**
    * Returns an array with notifications for the current user
    * @param none there are no parameters to be used
    * @return array notifications: the notifications as a link
*/
    
function notifications_get_notifications(WP_REST_Request $request)
{             
    if ($request->get_param( 'id' ) != get_current_user_id()){
        return new WP_Error("rest_forbidden", "Sorry, you are not allowed to do that.", ["status" => 403]);
    }
    $params['page']=$request->get_param( 'page' );
    $params['per_page']=$request->get_param( 'per_page' );
    $params['user_id']= $request->get_param( 'id' );
    $params['sort']=$request->get_param( 'sort' );
            
           
    $resultset = BP_Notifications_Notification::get($params);

    if (is_array($resultset)) {
        if(count($resultset) > 0){
            $notifications = array();
            foreach($resultset as $notif){
                $conv = (array) $notif;
                $conv['description'] = bp_get_notification_description($notif);
                $conv['avatar'] = getAvatar($notif->item_id);
                $notifications[] = $conv;
            }
            
            $response = new WP_REST_Response( $notifications );

            // Add a custom status code
            $response->set_status( 201 );

            // Add a custom header
            $count = get_latest_notification_count();
            if ($count)
                $response->header( 'Latest', $count );
            else
                $response->header( 'Latest', count($notif) );

            //$response->header( 'Last-Check', get_user_meta( get_current_user_id(), "_notification_last_seen", true) );

            return $response;
        } else {
            return [];
        }
        
    }
            
    return new WP_Error('notif_error', "cannot load notifications");
    
    
}

add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/users/(?P<id>\d+)/notifications/seen', array(
    'methods' => 'GET',
    'callback' => 'notification_set_last_seen',
    'permission_callback' => function () {
        return is_user_logged_in();
    }
    ) );
} );

function notification_set_last_seen(WP_REST_Request $request)
{
    
    $user_id = get_current_user_id();
    if (update_user_meta( $user_id, "_notification_last_seen", date('l j F Y h:i:s A') )) {
        return array(
            'status' => 'success',
            'data' => true
        );
    } else {
        return new WP_Error('notification_error', "cannot update user info");
    }
}


add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/users/(?P<id>\d+)/notifications/(?P<nid>\d+)/read', array(
    'methods' => 'GET',
    'callback' => 'mark_notification_as_read',
    'permission_callback' => function () {
        return is_user_logged_in();
    }
    ) );
} );

function mark_notification_as_read(WP_REST_Request $request)
{
    $id = (int) $request->get_param( 'nid' );
    //return $id;
    if (bp_notifications_mark_notification( $id)) {
        return array(
            'status' => 'success',
            'data' => true
        );
    } else {
        return new WP_Error('notification_error', "cannot update notification");
    }
}
