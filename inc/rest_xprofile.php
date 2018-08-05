<?php

function cpt_xprofile(WP_REST_Request $request)
{


		$oReturn = new stdClass();
		$oReturn->status = '';
		
		$clicked_pic = $request->get_param( 'type' );
		$remove = $request->get_param( 'remove' );
		$picture_code = $request->get_param( 'image' );
		$user_id = get_current_user_id();
		$bp_upload = xprofile_avatar_upload_dir('',$user_id);

		if ($remove == 'true' && $clicked_pic == 'cover_pic'){				
			delete_user_meta( $user_id, 'bbp_cover_pic');
			$oReturn->status = 'success';
			$oReturn->data = getUserProfile(get_current_user_email());
			return  $oReturn;
		}	
		
		$basedir = $bp_upload['path'];
		$baseurl = $bp_upload['url'];
		if(!file_exists($basedir)){@wp_mkdir_p( $basedir );}
		$filename = $clicked_pic.'_'.uniqid($user_id."_").'.jpg';
		$outputFile = $basedir.'/'.$filename;
		$imageurl = $outputFileURL = $baseurl.'/'.$filename;
		
		if(strstr($picture_code,'data:image/')){
			 $picture_code_arr = explode(',', $picture_code);
			$picture_code = $picture_code_arr[1];
		}
		
		$quality = 70;
		if(file_exists($outputFile)){@unlink($outputFile);}
		$data = base64_decode($picture_code);
		$image = imagecreatefromstring($data);
		$imageSave = imagejpeg($image, $outputFile, $quality);
		imagedestroy($image);
		if(!$imageSave){$oReturn->error = 'Image Save Error'; return  $oReturn;}
		if($outputFile && $clicked_pic=='cover_pic'){			
			update_user_meta( $user_id, 'bbp_cover_pic', $imageurl);
			bp_activity_add( array(
					'user_id'   => $user_id,
					'component' => 'profile',
					'type'      => 'new_cover'
				) );
			$oReturn->status = 'success';
			$oReturn->data = getUserProfile(get_current_user_email());			
			
		}elseif($outputFile && $clicked_pic=='profile_pic'){
			$imgdata = @getimagesize( $outputFile );
			$img_width = $imgdata[0];
			$img_height = $imgdata[1];
			$upload_dir = wp_upload_dir();
			$existing_avatar_path = str_replace( $upload_dir['basedir'], '', $outputFile );
			$args = array(
				'item_id'       => $user_id,
				'original_file' => $existing_avatar_path,
				'crop_x'        => 0,
				'crop_y'        => 0,
				'crop_w'        => $img_width,
				'crop_h'        => $img_height
			);
			
			if (bp_core_avatar_handle_crop( $args ) ) {
				//$imageurl = bp_core_fetch_avatar( array( 'item_id' => $user_id,'html'=>false,'type' => 'full'));
				// Add the activity
				bp_activity_add( array(
					'user_id'   => $user_id,
					'component' => 'profile',
					'type'      => 'new_avatar'
				) );
				$oReturn->status = 'success';
				$oReturn->data = getUserProfile(get_current_user_email());
				//$oReturn->imageurl = $imageurl;
			}else{
				$oReturn->status = 'error';
				$oReturn->message = $error;
			}
		}
				
		return  $oReturn;

}

add_action( 'rest_api_init', function () {
    register_rest_route( 'wp/v2', '/xprofile', array(
        'methods' => 'POST',
        'callback' => 'cpt_xprofile',
        'args' => array(
			'image' => array(
                'default' => "",
				'validate_callback' => function($param, $request, $key) {
					return is_string( $param );
				}
			),
            'type' => array(
                'default' => "",
				'validate_callback' => function($param, $request, $key) {
					return is_string( $param );
				}
			),
            'remove' => array(
                'default' => 'false',
				'type' => 'string',
				'enum' => array( 
					'true',
					'false'
				),
			),
		),
		'permission_callback' => function () {
			return current_user_can( 'read' );
		}
    ) );
} );