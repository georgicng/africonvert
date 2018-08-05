<?php

function userProfile()
{
    if (is_user_logged_in()) {
        $user = get_userdata(get_current_user_id());
        return array(
            "id" => $user->ID,
            "firstName" => $user->first_name,
            "lastName" => $user->last_name,
            "displayName" => $user->display_name,
            "userName" => $user->user_login,
            "role" => implode(', ', $user->roles),
            "email" => $user->user_email,
        );
    } else {
        return array("id" => 0);
    }
}

/**
 * Display last login time
 *
 */
 
function wpb_lastlogin()
{
    $last_login = get_the_author_meta('last_login');
    $the_login_date = human_time_diff($last_login);
    return $the_login_date;
}

function getUserProfile($email)
{
    $user = get_user_by('email', $email);
    $trim = array();
    $trim['user_name'] = $user->user_login;
    $trim['first_name'] = $user->first_name;
    $trim['last_name'] = $user->last_name;
    $trim['description'] = $user->description;
    $trim['email'] = $user->user_email;
    $trim['name'] = $user->display_name;
    $trim['url'] = $user->user_url;
    $trim['id'] = $user->ID;
    $trim['joined'] = $user->user_registered;
    $trim['last-login'] = wpb_lastlogin();
    $trim['last-active'] = bp_get_user_last_activity($user->ID);
    $trim['fields'] = array(
        'phone' => get_field('phone', 'user_'.$user->ID), //xprofile_get_field_data(2, $user->ID, false),
        'confirmed' => intval(get_field('confirmed', 'user_'.$user->ID)),
        'avatar' =>getAvatar($user->ID),
        'cover' => getCover($user->ID)
    );
    //bp_attachments_get_attachment( 'url', array( 'item_id' => $user->ID ) )
    return $trim;
}

function get_current_user_email()
{
    global $current_user;
    get_currentuserinfo();

    $email = $current_user->user_email;

    return $email;
}


function getCover($userid)
{
    $image = get_user_meta($userid, 'bbp_cover_pic', true);
    if (empty($image)) {
        return get_template_directory_uri().'/img/bg2.jpg';
    }
    return $image;
}

function getAvatar($userid)
{
    $image = bp_core_fetch_avatar(array('item_id' => $userid,'object' => 'user', 'type' => 'full','html' => false));
    if (empty($image)) {
        return get_template_directory_uri().'/img/custom_avatar.png';
    }
    return $image;
}

function getUsername($userid)
{
    if ($userid == 0) {
        return false;
    }
    $user = get_user_by('id', $userid);
    return $user->user_login;
}

function getContestInfo($id, $prop = '')
{
    $post = get_post($id);
    if (is_null($post) || $post->post_type != 'contest') {
        return false;
    }
    switch ($prop) {
        case 'description':
            return $post->post_excerpt;
        case 'image':
            return get_the_post_thumbnail_url($id);
        default:
            return $post->post_name;
    }
}

function getContestLink($id, $stage)
{
    if ($stage == 'submit') {
        return '/submit/'.$id;
    }
    if ($stage == 'vote') {
        return '/vote/'.$id;
    }
}

function getActivityContestLink($id, $stage)
{
    return '<a href="' . getContestLink($id, $stage) . '">' . getContestInfo($id) . '</a>';
}

function getActivityContestContent($id)
{
    return '<div><a href="' . getContestLink($id, $stage) . '">'.'<image src="'.getContestInfo($id, 'image').'"><p>' . getContestInfo($id) . '</p></a></div>';
}

function getEntryInfo($id, $prop)
{
    $post = get_post($id);
    if (is_null($post) || $post->post_type != 'submission') {
        return false;
    }
    switch ($prop) {
        case 'description':
            return $post->post_excerpt;
        case 'image':
            return get_the_post_thumbnail_url($id);
        default:
            return $post->post_name;
    }
}

function getEntryLink($contest_id, $entry_id)
{
    return '/submit/'.$contest_id.'/'.$entry_id;
}

function getActivityEntryLink($id)
{
    return '<a href="' . getEntryLink($userid) . '">' . getEntryInfo($id) . '</a>';
}

function getActivityEntryContent($id)
{
    return '<div><a href="' . getEntryLink($userid) . '">'.'<image src="'.getEntryInfo($id, 'image').'"><p>' . getEntryInfo($id) . '</p></a></div>';
    ;
}

function getUserLink($userid)
{
    return 'members/'.$userid;
}

function getActivityUserLink($userid)
{
    if ($userid == get_current_user_id()) {
        return 'You';
    } else {
        return '<a href="' . getUserLink($userid) . '">' . getUsername($userid) . '</a>';
    }
}

function getPeriod($length, $progress, $deadline)
{
    if ($progress  < 0) {
        return 'future';
    } elseif ($deadline < 0) {
        return 'past';
    } elseif ($deadline == 0) {
        return 'closing';
    } else {
        return 'running';
    }
}

function getCountdown($open, $close)
{
    $today = date_create(date("Ymd"));
    $open = date_create($open);
    $close = date_create($close);
    $length = (int) $open->diff($close)->format('%R%a');
    $progress = (int) $open->diff($today)->format('%R%a');
    $deadline = (int) $today->diff($close)->format('%R%a');
    return array(
        'length' => $length,
        'progress' => $progress,
        'deadline' => $deadline,
        'status' => getPeriod($length, $progress, $deadline)
    );
}

/*function get_author_submission_entry($author_id, $contest_id)
{

    // get the author's posts
    $posts = get_posts(
        array(
            'posts_per_page' => -1,
            'author' => $author_id,
            'post_type' => 'submissions',
            'tax_query' => array(
                array(
                    'taxonomy' => 'contests',
                    'field'    => 'term_id',
                    'terms'    => $contest_id,
                ),
            ),
        )
    );
    if (count($posts) > 0) {
        return $posts;
    } else {
        return false;
    }
} */

function get_user_entry($term_id)
{
    $userid = get_current_user_id();
    if ($userid == 0) {
        return false;
    }

    $sql = "SELECT * FROM afri_posts  INNER JOIN afri_postmeta AS cpt_onomy_pm1 ON 
    (afri_posts.ID = cpt_onomy_pm1.post_id AND cpt_onomy_pm1.meta_key = '_custom_post_type_onomies_relationship') 
    WHERE 1=1   AND afri_posts.post_author=$userid  AND afri_posts.post_type = 'submissions' AND 
    ( cpt_onomy_pm1.meta_value=$term_id ) GROUP BY afri_posts.ID ORDER BY afri_posts.post_date DESC LIMIT 0, 10";
    global $wpdb;
    $post = $wpdb->get_row($sql, "ARRAY_A");

    if (!is_null($post)) {
        $acf = get_fields($post["ID"]);
        $post['acf'] = $acf;
        return  $post;
    } else {
        return false;
    }
}

function get_contest_entry_count($id)
{
    global $cpt_onomy;
    $term = $cpt_onomy->get_term($id, 'contests');
    return $term->count;
}

/*function compileEmailTemplate($arr, $filename)
{
    if (!is_array($arr) || empty($arr) ||  empty($filename)) {
        return false;
    }

    $template = file_get_contents(get_template_directory()."/email_templates/".$filename.".html");


    if ($template == false) {
        return false;
    }

    foreach ($arr as $key => $value) {
        $html = str_replace("{{ $key }}", $value, $template);
    }

    return $html;
}*/

function sendMail($meta, $template, $template_data)
{
    $to = $meta['to'];

    $subject = $meta['subject'];
    $from = $meta['from'];

    $headers = "From: " . $meta['from'] . "\r\n";
    $headers .= "Reply-To: ". $meta['from'] . "\r\n";
    //$headers .= "CC: susan@example.com\r\n";
    $headers .= "MIME-Version: 1.0\r\n";
    $headers .= "Content-Type: text/html; charset=ISO-8859-1\r\n";

    //$message = compileEmailTemplate($template_data, $template);
    //loadPackage(get_template_directory()."/lib/plates");

    // Create new Plates instance
    $templates = new League\Plates\Engine(get_template_directory()."/email_templates");

    // Preassign data to the layout
    $templates->addData(emailTemplateParams(), 'layout');

    // Render a template
    $message = $templates->render($template, $template_data);

    if (!empty($message) && filter_var($to, FILTER_VALIDATE_EMAIL)) {
        return wp_mail($to, $subject, $message, $headers);
    }

    return false;
}

function emailTemplateParams()
{
    return array(
        "logo" => get_template_directory_uri()."/img/logo.png",
        "about" => home_url('about'),
        "home" => home_url(),
        "contact" => home_url('contact'),
        "contests" => home_url('submit'),
        "address" => "Your address goes here",
        "facebook" => "http://facebook.com",
        "twitter" => "http://twitter.com",
        "instagram" => "http://instagram.com",
    );
}

function loadPackage($dir)
{
    $composer = json_decode(file_get_contents("$dir/composer.json"), 1);
    $namespaces = $composer['autoload']['psr-4'];

    // Foreach namespace specified in the composer, load the given classes
    foreach ($namespaces as $namespace => $classpaths) {
        if (!is_array($classpaths)) {
            $classpaths = array($classpaths);
        }
        spl_autoload_register(function ($classname) use ($namespace, $classpaths, $dir) {
            // Check if the namespace matches the class we are looking for
            if (preg_match("#^".preg_quote($namespace)."#", $classname)) {
                // Remove the namespace from the file path since it's psr4
                $classname = str_replace($namespace, "", $classname);
                $filename = preg_replace("#\\\\#", "/", $classname).".php";
                foreach ($classpaths as $classpath) {
                    $fullpath = $dir."/".$classpath."/$filename";
                    if (file_exists($fullpath)) {
                        include_once $fullpath;
                    }
                }
            }
        });
    }
}

if (!function_exists('write_log')) {
    function write_log($log, $file="custom")
    {
        if (true === WP_DEBUG) {
            if (!function_exists('get_home_path')) {
                require_once(ABSPATH.'/wp-admin/includes/file.php');
            }
            $path = get_home_path()."logs";
            if (is_array($log) || is_object($log)) {
                error_log(print_r($log, true)."\r\n", 3, $path.'/'.$file.'.log');
            } else {
                error_log($log."\r\n", 3, $path.'/'.$file.'.log');
            }
        }
    }
}

function get_alt_image_id()
{
    //TODO: write theme option to set and retrieve this
    return intval(get_option("fallback_image", '0'));
}

function clean($string)
{
    $string = str_replace(' ', '-', $string); // Replaces all spaces with hyphens.
 
    return preg_replace('/[^A-Za-z0-9\-]/', '', $string); // Removes special chars.
}

 function buildValidation($post, array &$param)
 {
     $type = get_field('media_type', $post->id);
     if ($type == 'Document') {
         $validation = array('pattern' => '.doc,.docx,.xlsx,.xls,.csv,.ppt,.pptx,.txt');
         if (have_rows('file_rules', $post->id)) :
             while (have_rows('file_rules', $post->id)) :
                 the_row();
         switch (get_sub_field('field')) {
                     case "min-size":
                         $validation['size']['min'] = get_sub_field('value');
                         break;
                     case "max-size":
                         $validation['size']['max'] = get_sub_field('value');
                         break;
                 }
         endwhile;
         endif;
         $param['validation']  = $validation;
     } elseif ($type == 'PDF') {
         $validation = array('pattern' => '.pdf');
         if (have_rows('file_rules', $post->id)) :
             while (have_rows('file_rules', $post->id)) :
                 the_row();
         switch (get_sub_field('field')) {
                     case "min-size":
                         $validation['size']['min'] = get_sub_field('value');
                         break;
                     case "max-size":
                         $validation['size']['max'] = get_sub_field('value');
                         break;
                 }
         endwhile;
         endif;
         $param['validation']  = $validation;
     } elseif ($type == 'Image') {
         //$validation = array( 'pattern' => '.jpg, .png');
         $validation = array( 'pattern' => 'image/*');
         if (have_rows('media_rules', $post->id)) :
             while (have_rows('media_rules', $post->id)) :
                 the_row();
         switch (get_sub_field('field')) {
                     case "min-size":
                         $validation['size']['min'] = get_sub_field('value');
                         break;
                     case "max-size":
                         $validation['size']['min'] = get_sub_field('value');
                         break;
                     case "min-height":
                         $validation['height']['min'] = get_sub_field('value');
                         break;
                     case "max-height":
                         $validation['height']['max'] = get_sub_field('value');
                         break;
                     case "min-width":
                         $validation['width']['min'] = get_sub_field('value');
                         break;
                     case "max-width":
                         $validation['width']['max'] = get_sub_field('value');
                         break;
                     case "ratio":
                         $validation['ratio'] = get_sub_field('value');
                         break;
                 }
         endwhile;
         endif;
         $param['validation']  = $validation;
     } elseif ($type == 'Audio') {
         $validation = array( 'pattern' => '.mp3');
         if (have_rows('media_rules', $post->id)) :
             while (have_rows('media_rules', $post->id)) :
                 the_row();
         switch (get_sub_field('field')) {
                     case "min-size":
                         $validation['size']['min'] = get_sub_field('value');
                         break;
                     case "max-size":
                         $validation['size']['max'] = get_sub_field('value');
                         break;
                     case "min-duration":
                         $validation['duration']['min'] = get_sub_field('value');
                         break;
                     case "max-duration":
                         $validation['duration']['max'] = get_sub_field('value');
                         break;
                 }
         endwhile;
         endif;
         $param['validation']  = $validation;
     } elseif ($type == 'Video') {
         $validation = array( 'pattern' => 'video/*');
         if (have_rows('media_rules', $post->id)) :
             while (have_rows('media_rules', $post->id)) :
                 the_row();
         switch (get_sub_field('field')) {
                     case "min-size":
                         $validation['size']['min'] = get_sub_field('value');
                         break;
                     case "max-size":
                         $validation['size']['max'] = get_sub_field('value');
                         break;
                     case "min-duration":
                         $validation['duration']['min'] = get_sub_field('value');
                         break;
                     case "max-duration":
                         $validation['duration']['max'] = get_sub_field('value');
                         break;
                 }
         endwhile;
         endif;
         $param['validation']  = $validation;
     } else {
         $param['validation']  = null;
     }
 }
 
 function scheduleVoteLinkEmail($id, $subject, $template)
 {
     global $email_queue;
    
     $args = array(
        'post_type'  => 'submisions',
        'tax_query' => array(
                'taxonomy' => 'contests',
                'field'    => 'term_id',
                'terms'    => $id,
            ),
        'post_status'    => 'publish',
    );
     $my_query = new WP_Query($args);

     $posts = $my_query->posts;

     // Get just post_title and post_content of each post
     if ($posts) {
         array_walk(
            $posts,
            function ($post) {
                $meta = [];
                $author = $post->post_author;
                $item['meta'] = [
                    "to" => get_the_author_meta('user_email', $author),
                    "subject" => "Votes for your Flow",
                    "from" => get_bloginfo("admin_email")
                ];
                $item['template'] = "vote_link_notification";
                $item['data'] = [
                    "link" => home_url()."/vote/".$id."/".$post->ID,
                    "name" => get_the_author_meta('display_name', $author)
                ];
                
                $email_queue->push_to_queue($item);
            }
        );
     }
    
     $email_queue->save()->dispatch();
 }

 function scheduleValidationEmail($id, $approval = true)
 {
     if (empty($id)) {
         return;
     }
    
     global $email_queue;
    
     $post = get_post($id);
    
     $author = $post->post_author;
     $item['meta'] = [
        "to" => get_the_author_meta('user_email', $author),
        "subject" => $approval?"Success!!!":"We are very sorry.",
        "from" => get_bloginfo("admin_email")
    ];
     $item['template'] = $approval?"submission_approval":"submission_rejection";
     $item['data'] = [
        "link" => home_url()."/vote/".$id."/".$post->ID,
        "name" => get_the_author_meta('display_name', $author)
    ];
    
     $email_queue->push_to_queue($item);
   
     $email_queue->save()->dispatch();
 }

 function scheduleWinningEmail($id)
 {
     if (empty($id)) {
         return;
     }
    
     global $email_queue;
    
     $posts = get_post($id);
     global $cpt_onomy;
     $terms = wp_get_object_terms($post->ID, 'contests');
     //TODO: ensure that all things are in place
     if ($terms && !is_wp_error($terms)) {
         $contest = $terms[0]->name;
     } else {
         $contest = "";
     }
    
     $author = $post->post_author;
     $item['meta'] = [
        "to" => get_the_author_meta('user_email', $author),
        "subject" => "Subject; Congratulations!!",
        "from" => get_bloginfo("admin_email")
    ];
     $item['template'] = "winner";
     $item['data'] = [
        "name" => get_the_author_meta('display_name', $author),
        "contest" => $contest
    ];
    
     $email_queue->push_to_queue($item);
   
     $email_queue->save()->dispatch();
 }

 /**
 * Get all stat
 *
 * @param $args array
 *
 * @return array
 */
function gai_get_all_stat($args = array())
{
    global $wpdb;

    $defaults = array(
        'number'     => 20,
        'offset'     => 0,
        'orderby'    => 'id',
        'order'      => 'ASC',
    );

    $args      = wp_parse_args($args, $defaults);
    $cache_key = 'stat-all';
    $items     = wp_cache_get($cache_key, 'gai');

    if (false === $items) {
        $items = $wpdb->get_results('SELECT * FROM ' . $wpdb->prefix . 'stats ORDER BY ' . $args['orderby'] .' ' . $args['order'] .' LIMIT ' . $args['offset'] . ', ' . $args['number']);

        wp_cache_set($cache_key, $items, 'gai');
    }

    return $items;
}

/**
 * Fetch all stat from database
 *
 * @return array
 */
function gai_get_stat_count()
{
    global $wpdb;

    return (int) $wpdb->get_var('SELECT COUNT(*) FROM ' . $wpdb->prefix . 'stats');
}

/**
 * Fetch a single stat from database
 *
 * @param int   $id
 *
 * @return array
 */
function gai_get_stat($id = 0)
{
    global $wpdb;

    return $wpdb->get_row($wpdb->prepare('SELECT * FROM ' . $wpdb->prefix . 'stats WHERE id = %d', $id));
}


function collateContest($id)
{
    if (empty($id)) {
        return;
    }
    
    global $wpdb;
    $sql = "SELECT distinct post_id as post, count(id) as vote_count ".
     "FROM `{$wpdb->prefix}gs_vote_post` ".
     "where contest_id = %d ".
     "group by post_id ".
     "order by vote_count desc ";

    $result = $wpdb->get_results($wpdb->prepare($sql, $id), ARRAY_A);
    $max = max($result);
    $contenders = array_filter($result, function ($x) use ($max) {
        //global $max;
        return ($x['vote_count'] == $max['vote_count']);
    });

    if (count($contenders) == 1) {
        update_post_meta($contenders[0]['post'], 'winner', true);
        update_post_meta($id, 'winner', $contenders[0]['post']);
    } elseif (count($contenders) > 1) {
        array_walk($contenders, function ($x) {
            update_post_meta($x['post'], 'candidate', true);
        });
    }
    //email admin to check results
    update_field('stage', 'Result', $id);
    
    $admin_email = get_bloginfo('admin_email');
    $subject = "Results Ready for ".get_the_title($id);
    $comment = "<p>Hi Admin,</p>".
    "<p>Results have been collated for the contest ".get_the_title($id)." successfully</p>".
    "<div><a href='".admin_url("/post.php?post=".$id."&action=edit")."'>View Now</a></div>";
    
    //send email
    mail($admin_email, "$subject", $comment);
}

/*function array_find($array, $key, $value)
{
    if (empty($searched) || empty($parents)) {
        return false;
      }

      foreach ($parents as $key => $value) {
        $exists = true;
        foreach ($searched as $skey => $svalue) {
          $exists = ($exists && IsSet($parents[$key][$skey]) && $parents[$key][$skey] == $svalue);
        }
        if($exists){ return $parents[$key]; }
      }

      return [];
}*/

function array_find($array, $key, $value)
{
    if (empty($array) || empty($key) || empty($value)) {
        return [];
    }
    
    foreach ($array as $item) {
        foreach ($item as $skey => $svalue) {
            if ($key == $skey && $value == $svalue) {
                return $item;
            }
        }
    }
    
    return [];
}
