<?php

class Wistia_Upload_Process extends WP_Background_Process {

	/**
	 * @var string
	 */
	protected $action = 'wistia_upload';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $item Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function task( $item ) {
		if (empty($item['attempt'])){
			$item['attempt'] = 1;
		} else {
			$item['attempt'] += 1;
		}
		// Actions to perform
		write_log("wistia_task_schedule called with : ".json_encode($item));
		$args =  array(
				'api_password'=>get_option('wistia_admin_api_password'), 
				'project_id' => $item['project'], 
				'url' =>  $item['link'],
				'name' => $item['name'],
				'description'  => $item['description'] 
		);
		$response = wp_remote_post("https://upload.wistia.com/", array("body" => $args));
		write_log("wistia_task_post response: ".json_encode($response));
		
		$api_response = wp_remote_retrieve_body( $response );
		
		if ( (is_wp_error( $response ) && $item['attempt'] <= 5) || (is_wp_error( $api_response ) && $item['attempt'] <= 5) || (empty($api_response) && $item['attempt'] <= 5) || (strpos($api_response, 'hashed_id') === FALSE && $item['attempt'] <= 5) ){
		    write_log("wistia_task_post response is error");
		    return $item;
		} else if ( (is_wp_error( $response ) && $item['attempt'] > 5) || (is_wp_error( $api_response ) && $item['attempt'] > 5) || (empty($api_response) && $item['attempt'] > 5) || (strpos($api_response, 'hashed_id') === FALSE && $item['attempt'] > 5) ) {
		    $uploads = get_option( 'gai_failed_uploads' );
			if ( $uploads  !== false ) {
				$uploads[] = $item;
				update_option( 'gai_failed_uploads', $uploads);			    
			} else {
				$uploads[] = $item;
				update_option( 'gai_failed_uploads', $uploads, "no" );
			}
			return false;
		} else {
			$data = json_decode( $api_response, true);
			write_log("wistia_task_post response data: ".json_encode($data));
			
			update_field('wistia_id', $data['id'], $item['post_id']);
			update_field('wistia_hash', $data['hashed_id'], $item['post_id']);//"progress":0.0, "progress":1.0,
			//update_field('wistia_progess', $data['progress'], $item['post_id']);
			update_field('type', $data['type'], $item['post_id']);
			//update_field('embed_code', $data['type'], $item['post_id']);
			//update_field('url', $data['type'], $item['post_id']);
			update_field('thumbnail', $data['thumbnail']['url'], $item['post_id']);
			return false;
		}
		  
	}

	/**
	 * Complete
	 *
	 * Override if applicable, but ensure that the below actions are
	 * performed, or, call parent::complete().
	 */
	protected function complete() {
		parent::complete();

		// Show notice to user or perform some other arbitrary task...
	}

}