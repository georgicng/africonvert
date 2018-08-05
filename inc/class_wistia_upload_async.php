<?php

class Wistia_Upload_Process_Async extends WP_Async_Request {

	/**
	 * @var string
	 */
	protected $action = 'wistia_upload_aysnc';

	/**
	 * Task
	 *
	 * Override this method to perform any actions required on each
	 * queue item. Return the modified item for further processing
	 * in the next pass through. Or, return false to remove the
	 * item from the queue.
	 *
	 * @param mixed $_POST Queue item to iterate over
	 *
	 * @return mixed
	 */
	protected function handle() {
		// Actions to perform
		write_log("wistia_task_handle called with : ".json_encode($_POST));
		//loadPackage(__DIR__."/vendor/wistia-php");		
		$client   = new Automattic\Wistia\Client( ['token' => get_option('wistia_client_api_password')] );
		$data = $client->post( 'https://upload.wistia.com/', [ 'project_id' => $_POST['project'], 'url' =>  $_POST['link'],'name' => $_POST['name'],
		'description'  => $_POST['description'] ] );
		//$data = $client->create_media( item['file'], [ 'project_id' => $_POST[project], 'name' => $_POST['name'],
		//'description'  => $_POST['description'] ] );
		write_log("wistia_task_post response: ".json_encode($data));
		if (is_wp_error( $data )){
			write_log($data);
		} else {
			update_field('wistia_id', $data['id'], $_POST['post_id']);
			update_field('wistia_hash', $data['hashed_id'], $_POST['post_id']);
			update_field('type', $data['type'], $_POST['post_id']);
			//update_field('embed_code', $data['type'], $_POST['post_id']);
			//update_field('url', $data['type'], $_POST['post_id']);
			update_field('thumbnail', $data['thumbnail']['url'], $_POST['post_id']);
			write_log($data);
		}
		  
	}


}