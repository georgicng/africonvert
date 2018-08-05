<?php

function afri_schedule_cron()
{
    $timestamp = wp_next_scheduled( 'afri_contest_cron' );

    if ($timestamp == false) {
        wp_schedule_event( time(), 'daily', 'afri_contest_cron' );
    }

    if ( ! wp_next_scheduled( 'clear_upload' ) ) {
        wp_schedule_event( time(), 'hourly', 'clear_upload' );
      }
      
}
add_action( 'init', 'afri_schedule_cron' );

function afri_contest_open_submissions()
{
    write_log("Cron Job: Open Submissions Called sucessfully at ".date('Y-m-d H:i:s'));
    $args = array(
        'post_type'  => 'contests',
        'meta_key'   => 'entry_opens',
        'orderby'    => 'meta_value_datetime',
        'order'      => 'ASC',
        'meta_query' => array(
            array(
                'key'     => 'stage',
                'value'   => 'Pending',
            ),
        ),
    );
    $my_query = new WP_Query( $args );

    $posts = $my_query->posts;

	// Get just post_title and post_content of each post
	if ($my_query->post_count > 0) {
	    $items = [];
        foreach($posts as $post){
            $today = date("Ymd");
            $date = date(get_post_meta($post->ID, 'entry_opens', true));
                write_log("Cron Job: Open Submissions post: ".json_encode($post));
                    write_log("Cron Job: Open Submissions dates: ".json_encode([$date, $today, $today >= $date]));
            if($today >= $date) {
                update_field('stage', 'Submit', $post->ID); 
                write_log("Cron Job: ".$post->ID." status changed");
                $item['meta'] = [
                    "to" => get_bloginfo("admin_email"),
                    "subject" => $post->post_title." Contest Opened for Submission",
                    "from" => get_bloginfo("admin_email")
                ];
                $item['template'] = "contest_status";
                $item['data'] = [
                    "link" => admin_url('post.php?post='.$post->ID.'&action=edit'),
                    "title" => $post->post_title,
                    "name" => "Admin",
                    "status" => "Submit"
                ]; 
                $items[] = $item;
                
                
            } 
        }
        if(!empty($items)){
            write_log("Cron Job: items: ".json_encode($items));
            global $email_queue;
            foreach($items as $item){
                $email_queue->push_to_queue( $item );
            } 
            $email_queue->save()->dispatch();
        }
    } 
    
}
add_action( 'afri_contest_cron', 'afri_contest_open_submissions' );

function afri_contest_close_submissions()
{
    write_log("Cron Job: Close Submissions Called sucessfully at ".date('Y-m-d H:i:s'));
    $args = array(
        'post_type'  => 'contests',
        'meta_key'   => 'entry_closes',
        'orderby'    => 'meta_value_datetime',
        'order'      => 'ASC',
        'meta_query' => array(
            array(
                'key'     => 'stage',
                'value'   => 'Submit',
            ),
        ),
    );
    $my_query = new WP_Query( $args );

    $posts = $my_query->posts;
    write_log("Cron Job: Close Submissions posts: ".json_encode($posts));

	// Get just post_title and post_content of each post
	if ($my_query->post_count > 0) {
        $items = [];
        foreach($posts as $post){
            $today = date("Ymd");
            $date =date(get_post_meta($post->ID, 'entry_closes', true));
            if($today >= $date){
                //equal or less to cover for missed
                update_field('stage', 'Post-Submit', $post->ID); //send mail about closed
                write_log("Cron Job: ".$post->ID." status changed");
                $item['meta'] = [
                    "to" => get_bloginfo("admin_email"),
                    "subject" => $post->post_title." Contest Closed for Submission",
                    "from" => get_bloginfo("admin_email")
                ];
                $item['template'] = "contest_status";
                $item['data'] = [
                    "link" => admin_url('post.php?post='.$post->ID.'&action=edit'),
                    "title" => $post->post_title,
                    "name" => "Admin",
                    "status" => "Post-Submit"
                ];               
                $items[] = $item;
            } 
        }
        if(!empty($items)){
            write_log("Cron Job: items: ".json_encode($items));
            global $email_queue;
            foreach($items as $item){
                $email_queue->push_to_queue( $item );
            } 
            $email_queue->save()->dispatch();
        }
    } 
    
}
add_action( 'afri_contest_cron', 'afri_contest_close_submissions' );

function afri_contest_open_voting()
{
    write_log("Cron Job: Open Voting Called sucessfully at ".date('Y-m-d H:i:s'));
    $args = array(
        'post_type'  => 'contests',
        'meta_key'   => 'voting_opens',
        'orderby'    => 'meta_value_datetime',
        'order'      => 'ASC',
        'meta_query' => array(
            array(
                'key'     => 'stage',
                'value'   => 'Post-Submit',
            ),
        ),
    );
    $my_query = new WP_Query( $args );

    $posts = $my_query->posts;

	// Get just post_title and post_content of each post
	if ($my_query->post_count > 0) {
        $items = [];
        foreach($posts as $post){
            $today = date("Ymd");
            $date =date(get_post_meta($post->ID, 'voting_opens', true));
            if($today >= $date){
                update_field('stage', 'Vote', $post->ID);
                write_log("Cron Job: ".$post->ID." status changed");
                scheduleVoteLinkEmail($post->ID);
                $item['meta'] = [
                    "to" => get_bloginfo("admin_email"),
                    "subject" => $post->post_title." Contest Opened for Voting",
                    "from" => get_bloginfo("admin_email")
                ];
                $item['template'] = "contest_status";
                $item['data'] = [
                    "link" => admin_url('post.php?post='.$post->ID.'&action=edit'),
                    "title" => $post->post_title,
                    "name" => "Admin",
                    "status" => "Vote"
                ];               
                
                $items[] = $item;
            }
        }
        if(!empty($items)){
            write_log("Cron Job: items: ".json_encode($items));
            global $email_queue;
            foreach($items as $item){
                $email_queue->push_to_queue( $item );
            } 
            $email_queue->save()->dispatch();
        }
    } 
    
}
add_action( 'afri_contest_cron', 'afri_contest_open_voting' );

function afri_contest_close_voting()
{
    write_log("Cron Job: Close Voting Called sucessfully at ".date('Y-m-d H:i:s'));
    $args = array(
        'post_type'  => 'contests',
        'meta_key'   => 'voting_closes',
        'orderby'    => 'meta_value_datetime',
        'order'      => 'ASC',
        'meta_query' => array(
            array(
                'key'     => 'stage',
                'value'   => 'Vote',
            ),
        ),
    );
    $my_query = new WP_Query( $args );

    $posts = $my_query->posts;

	// Get just post_title and post_content of each post
	if ($my_query->post_count > 0) {
       $items = [];
       foreach($posts as $post){
            $today = date("Ymd");
            $date =date(get_post_meta($post->ID, 'voting_closes', true));
            if($today >= $date){
                update_field('stage', 'Post-Vote', $post->ID);
                write_log("Cron Job: ".$post->ID." status changed");
                collateContest($post->ID);
                $item['meta'] = [
                    "to" => get_bloginfo("admin_email"),
                    "subject" => $post->post_title." Contest Closed for Voting",
                    "from" => get_bloginfo("admin_email")
                ];
                $item['template'] = "contest_status";
                $item['data'] = [
                    "link" => admin_url('post.php?post='.$post->ID.'&action=edit'),
                    "title" => $post->post_title,
                    "name" => "Admin",
                    "status" => "Post-Vote"
                ];               
                
                $items[] = $item;
            }
       }
        if(!empty($items)){
            global $email_queue;
            foreach($items as $item){
                $email_queue->push_to_queue( $item );
            } 
            $email_queue->save()->dispatch();
        }
    } 
    
}
add_action( 'afri_contest_cron', 'afri_contest_close_voting' );


function afri_clear_completed_upload() {
    write_log("Cron Job: Clear Upload Called sucessfully at ".date('Y-m-d H:i:s'));
    $upload_dir = wp_upload_dir();
    $wistia_dir = $upload_dir['basedir']."/wistia";
    write_log("Cron Job: Clear Upload wistia dir: ".$wistia_dir);
    if ( ! file_exists( $wistia_dir ) || !(new \FilesystemIterator($wistia_dir))->valid() ) {
        return;
    }      

    $projects = scandir($wistia_dir);

    foreach ($projects as $project){
        $project_dir = $wistia_dir.'/'.$project;
        write_log("Cron Job: Clear Upload project dir: ".$project_dir);
        if (!in_array($project,['.','..']) && is_dir($project_dir)){            
            if ( file_exists( $project_dir ) ){
                if (!(new \FilesystemIterator($project_dir))->valid()){
                    rmdir($project_dir);
                    continue;
                }
                $medias = scandir($project_dir); 
                $args = ['api_password'=>get_option('wistia_admin_api_password'), 'project_id'=>$project];
                $response = wp_remote_get("https://api.wistia.com/v1/medias.json?".http_build_query($args));
                write_log("Cron Job: Clear Upload response: ".json_encode($response));
                if ( !is_wp_error( $response ) ) {
                    $api_response = wp_remote_retrieve_body( $response );
                    write_log("Cron Job: Clear Body body: ".json_encode($api_response));
                    if ( !is_wp_error( $api_response ) && !empty( $api_response)) {
                        $data = json_decode( $api_response, true );
                        write_log("Cron Job: Clear Upload data: ".json_encode($data));
                        if (!empty($data)){
                            foreach ($medias as $media){
                                $media_path = $project_dir."/".$media;
                                write_log("Cron Job: Clear Upload media dir: ".$media_path);
                                if (!in_array($media,['.','..']) && is_dir($media_path) == false && strpos($media, "_") !== false){
                                    $split = explode("_", $media);
                                    $id = $split[0];
                                    $hash = get_field("hashed_id", $id);
                                    write_log("Cron Job: Clear Upload sub_hash: ".json_encode($hash));
                                    if( $hash ) {
                                        $obj = array_find($data, 'hashed_id', $hash);
                                        write_log("Cron Job: Clear Upload wistia_data: ".json_encode($obj));
                                        if (!is_empty($obj) && $obj['progress'] == 1.0 ){
                                            unlink($media_path);
                                        }                                    
                                    }                                    
                                }                                
                            }
                        }                        
                    }                    
                }  
            }  
        }
    }
}

add_action( 'afri_clear_upload', 'afri_clear_completed_upload' );