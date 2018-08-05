<?php


function cp_get_submission_category($object, $field_name, $request)
{

    //global $cpt_onomy;
    //return $object;

    //return $object['id'];
    //$cat = get_the_terms( (int) $object['id'], 'contests' );
    
    $cat = wp_get_object_terms( $object['id'], 'contests' );
    //return $cat;

    if(is_wp_error( $cat ) || empty($cat)){
        return null;
    }
       
    $list = array();

    foreach ($cat as $item) {
        $list[] = $item->term_id;
    }

    return $list[0];
}

function cp_set_submission_category($value, $object, $field_name)
{
    error_log ("Submission update_callback value param: ".$value);
    error_log ("Submission update_callback id param: ".$object->ID);
    error_log ("Submission update_callback object param: ".json_encode($object));
    $value = (int) strip_tags( $value );
    if (! $value || ! is_integer( $value )) {
        return;
    }
    
    global $cpt_onomy;
    $ret = $cpt_onomy->wp_set_object_terms( $object->ID, $value , 'contests' );
    error_log ("Submission update_callback cpt_onomy return param: ".json_encode($ret));

    if (is_wp_error( $ret )){
        return false;
    }
    
    return $ret[0];
}

function cp_register_submission_cat()
{
    register_rest_field( 'submissions',
        'cat',
        array(
            'get_callback'    => 'cp_get_submission_category',
            'update_callback' => 'cp_set_submission_category',
            'schema'          => null,
        )
    );
}
add_action( 'rest_api_init', 'cp_register_submission_cat' );

function cp_get_submission_contest($object, $field_name, $request)
{
    $ct_cat = wp_get_object_terms( $object[ 'id' ], 'contests' );
     if( ! is_wp_error( $ct_cat ) && ! empty( $ct_cat ) ) {

        $contest = get_post($ct_cat[0]->term_id);
        $meta = get_fields($ct_cat[0]->term_id);
        $contest->acf = $meta;
            $toDay = date('Y-m-d');
        //echo $paymentDate; // echos today!
        $votingOpens = date('Y-m-d', strtotime($meta['voting_opens']));
        $votingCloses = date('Y-m-d', strtotime($meta['voting_closes']));
        $countdown = getCountdown($votingOpens, $votingCloses);

        if (($countdown['status'] == "running" || $meta['stage'] == "Vote") && !gs_vp_user_has_voted(get_current_user_id(), $contest->ID)) {
            $contest->can_vote = true;
        } else {
            $contest->can_vote = false;
        }
        return $contest;
        
    }    
    return  null;
   
}


function cp_register_submission_contest()
{
    register_rest_field( array('submissions'),
        'contest',
        array(
            'get_callback'    => 'cp_get_submission_contest',
            'update_callback' => null,
            'schema'          => null,
        )
    );
}

add_action( 'rest_api_init', 'cp_register_submission_contest' );

function cpt_submission_query_var($valid_vars)
{
    
    $valid_vars = array_merge( $valid_vars, array( 'contest' ) );
    return $valid_vars;
}
add_filter( 'rest_query_vars', 'cpt_submission_query_var', 10, 3 );

function cpt_submission_query($args, $request)
{

    if (isset( $request['contest'] ) && ! empty( $request['contest'] )) {
        $args['tax_query'] = array(
            array(
                'taxonomy' => 'contests',
                'field'    => 'term_id',
                'terms'    => $request['contest'],
            )
        );
    }

    return $args;
}
add_filter( 'rest_submissions_query', 'cpt_submission_query', 10, 3);

function cpt_rest_prepare_submissions($response, $post, $request)
{
    $_data = $response->data;

    if (isset( $_data['author'] ) && ! empty( $_data['author'] )) {        
        $user_info = get_userdata( $_data['author'] );
        $_data['author_name'] = $user_info->display_name;
    }
    
    $thumbnail_id = get_post_thumbnail_id( $post->ID );
	$thumbnail = wp_get_attachment_image_src( $thumbnail_id );
	$cover = wp_get_attachment_image_src( $thumbnail_id, 'cover' );
	$full = wp_get_attachment_image_src( $thumbnail_id, 'full' );
	$_data['featured_image_thumbnail_url'] = $thumbnail[0];
	$_data['featured_image_cover_url'] = $cover[0];
	$_data['featured_image_url'] = $full[0];

    $response->data = $_data; 

    return $response;
}
add_filter( 'rest_prepare_submissions', 'cpt_rest_prepare_submissions', 10, 3);


function cpt_upload(WP_REST_Request $request)
{
    write_log("Files: ".json_encode($_FILES));

    //check if tyhe person has upload for contest before

    $post_id = wp_insert_post(
        array(
            'post_author'		=>	get_current_user_id(),
            'post_name'		=>	sanitize_title($request['name']),
            'post_title'		=>	$request['name'],
            'post_content' => $request['description'],
            'post_status'		=>	'draft',
            'post_type'		=>	'submissions'
        )
    );

    
    $project_id = get_field( "wistia_project_id", $request['category'] );
    write_log("Submission post created: ".json_encode($post_id));

    global $cpt_onomy;
    $ret = $cpt_onomy->wp_set_object_terms( $post_id, $request['category'] , 'contests' );
    write_log("Submission update_callback cpt_onomy return param: ".json_encode($ret));

    require_once( ABSPATH . 'wp-admin/includes/image.php' );
    require_once( ABSPATH . 'wp-admin/includes/file.php' );
    require_once( ABSPATH . 'wp-admin/includes/media.php' );
    
    // Let WordPress handle the upload.
    // Remember, 'my_image_upload' is the name of our file input in our form above.
    $attachment_id = media_handle_upload( 'thumbnail', $post_id );
    
    if ( is_wp_error( $attachment_id ) ) {
        write_log("submission_thumb_errors: ".json_encode($attachment_id));
    } else {
        set_post_thumbnail( $post_id, $attachment_id );
    }
    

    $upload_dir = wp_upload_dir();
    $wistia_path = '/wistia/'.$project_id;
    $upload_dirname = $upload_dir['basedir'].$wistia_path;
    if ( ! file_exists( $upload_dirname ) ) 
        wp_mkdir_p( $upload_dirname );
        
    $storage = new \Upload\Storage\FileSystem($upload_dirname);
    $media = new \Upload\File('media', $storage);
    
    try {

        // Optionally you can rename the file on upload
        $name = $post_id."_".clean($request['name']);
        $media->setName($name);
        
        $type = get_field('media_type', $request['category']);
        switch($type){
            case "Video":
            $type = array('video/mp4', 'video/x-flv','video/3gpp');
            if (have_rows('media_rules', $post->id)) :
                while (have_rows('media_rules', $post->id)) :
                    the_row();
                    switch (get_sub_field('field')) {
                        case "min-size":
                            $min = get_sub_field('value');
                            break;
                        case "max-size":
                            $max = get_sub_field('value');
                            break;
                    }
                endwhile;
            endif;    
        }
        
        $type = ($type)? $type : array('video/mp4', 'video/x-flv','video/3gpp');
        
        if($max){
            if(is_numeric($max))
                $max = $max."B";
            else
                $max = str_replace("B","", $max);
        } else {
            $max = "50M";
        }
        
        if($min){
            if(is_numeric($min))
                $min = $min."B";
            else
                $min = str_replace("B","", $min);
        } else {
            $min = 0;
        }
        
        $media->addValidations(
            array(
                new \Upload\Validation\Mimetype($type),
                new \Upload\Validation\Size($max, $min)
            )
        );
        
       /* $media->addValidations(array(
            // Ensure file is of type "image/png"
            new \Upload\Validation\Mimetype(rules['validation']['pattern']),

            //You can also add multi mimetype validation
            //new \Upload\Validation\Mimetype(array('image/png', 'image/gif'))

            // Ensure file is no larger than 5M (use "B", "K", M", or "G")
            new \Upload\Validation\Size(rules['validation']['size']['max'])
        ));*/
    
        $media->upload();
        $file_name = $media->getNameWithExtension();

        
        if ( !empty( $project_id ) ){
            $item = array(
                'name' => $request['name'],
                'description' => $request['description'],
                'link' => $upload_dir['baseurl'].$wistia_path."/".$file_name,
                'file' => $upload_dir['basedir'].$wistia_path."/".$file_name,
                'project' => $project_id,
                'post_id' => $post_id
            );

            //dispatch upload
            global $wistia_queue;
            $wistia_queue->push_to_queue( $item );
            $wistia_queue->save()->dispatch();
            global $process_async;
            $process_async->data( $item )->dispatch();
            write_log("wistia upload async task scheduled successfully : ".json_encode($item));
        } else {
            write_log("wistia upload async task schedule failed");
        }
        

    } catch (\Exception $e) {
        if ($errors = $media->getErrors())
            write_log("media file upload errors: ".json_encode($errors));
        else
            write_log("media file upload other exeception: ".json_encode($e));
    }

    return get_post($post_id);

}

add_action( 'rest_api_init', function () {
        register_rest_route( 'wp/v2', '/upload', array(
            'methods' => 'POST',
            'callback' => 'cpt_upload',
            'args' => array(
                'media' => array(
                    'required' => false,
                    'validate_callback' => function($param, $request, $key) {
                        $parameters = $request->get_file_params();                        
                        error_log("Paras check".json_encode($request->get_file_params));
                        return true; 
                        
                        if(!file_exists($parameters['media']['tmp_name']) || !is_uploaded_file($parameters['media']['tmp_name'])){
                            return false;
                        }
                        
                    }
                ),
                'thumbnail' => array(
                    'required' => false,
                    'validate_callback' => function($param, $request, $key) {
                        $parameters = $request->get_file_params();
                        error_log("Paras check".json_encode($request->get_file_params));
                        return true;
                        if(!file_exists($parameters['thumbnail']['tmp_name']) || !is_uploaded_file($parameters['thumbnail']['tmp_name'])){
                            return false;
                        }
                        return true;  
                    }
                ),
                'name' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    }
                ),
                'description' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return is_string( $param );
                    }
                ),
                'category' => array(
                    'required' => true,
                    'validate_callback' => function($param, $request, $key) {
                        return is_integer( intval($param) );
                    }
                ),
            ),
            'permission_callback' => function () {
                return current_user_can( 'read' );
            }
        ) 
    );
});