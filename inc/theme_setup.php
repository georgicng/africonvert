<?php

function my_theme_setup()
{
    add_theme_support('post-thumbnails');
    set_post_thumbnail_size(300, 300, false);
    define('BP_AVATAR_THUMB_WIDTH', 80);
    define('BP_AVATAR_THUMB_HEIGHT', 80);
    define('BP_AVATAR_FULL_WIDTH', 250);
    define('BP_AVATAR_FULL_HEIGHT', 250);
    add_image_size('cover', 1200, 300, true);
    //add_image_size( 'body-thumb', 120, 120, true );
    //add_image_size( 'body-featured', 120, 120, true );
    //add_image_size( 'sidebar-thumb', 120, 120, true );
}
add_action('after_setup_theme', 'my_theme_setup');

/**
 * Registers an editor stylesheet for the theme.
 */
function wpdocs_theme_add_editor_styles()
{
    add_editor_style('custom-editor-style.css');
}
add_action('admin_init', 'wpdocs_theme_add_editor_styles');

function register_my_menus()
{
    register_nav_menus(
    array(
      'header-menu' => __('Header Menu'),
      'footer-menu' => __('Footer Menu')
    )
  );
    global $wistia_queue;
    $wistia_queue = new Wistia_Upload_Process();
    global $process_async;
    $process_async = new Wistia_Upload_Process_Async();
    global $email_queue;
    $email_queue = new Schedule_Email();
}
add_action('init', 'register_my_menus');

function remove_admin_bar()
{
    if (!current_user_can('administrator') && !is_admin()) {
        add_filter('show_admin_bar', '__return_false'); //show_admin_bar(false);
    }
}
add_action('after_setup_theme', 'remove_admin_bar');

function customize_my_wp_admin_bar($wp_admin_bar)
{
    $nodes = $wp_admin_bar->get_nodes();
    foreach ($nodes as $node) {

        // put a span before the title
        $node->meta['target'] = '_self';

        // update the Toolbar node
        $wp_admin_bar->add_node($node);
    }
}
add_action('admin_bar_menu', 'customize_my_wp_admin_bar', 80);

function my_add_link_target($html)
{
    $html = preg_replace('/(<a.*")>/', '$1 target="_self">', $html);
    return $html;
}
add_filter('image_send_to_editor', 'my_add_link_target', 10);

// add_filter('show_admin_bar', '__return_false');

function allow_contributor_uploads()
{
    $contributor = get_role('contributor');
    $contributor->add_cap('upload_files');
}

add_action('admin_init', 'allow_contributor_uploads');

function theme_prefix_setup()
{
    add_theme_support('custom-logo', array(
        'height'      => 100,
        'width'       => 400,
        'flex-width' => true,
    ));
}
add_action('after_setup_theme', 'theme_prefix_setup');

function theme_slug_widgets_init()
{
    register_sidebar(
            array(
            'name' => __('Home Sidebar', 'theme-slug'),
            'id' => 'sidebar-1',
            'description' => __('Widgets in this area will be shown only on homepage sidebar.', 'theme-slug'),
            'class'         => '',
            'before_widget' => '',
            'after_widget'  => '',
            'before_title'  => '',
            'after_title'   => ''
            )
        );
    register_sidebar(
                array(
                'name' => __('Blog Sidebar', 'theme-slug'),
                'id' => 'sidebar-2',
                'description' => __('Widgets in this area will be shown on all blog posts.', 'theme-slug'),
                'class'         => '',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => ''
                )
            );
    register_sidebar(
                array(
                'name' => __('Bottom Sidebar', 'theme-slug'),
                'id' => 'sidebar-3',
                'description' => __('Widgets in this area will be shown on before the footer.', 'theme-slug'),
                'class'         => '',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => ''
                )
            );
    register_sidebar(
                array(
                'name' => __('Footer Sidebar', 'theme-slug'),
                'id' => 'sidebar-4',
                'description' => __('Widgets in this area will be shown on the footer.', 'theme-slug'),
                'class'         => '',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => ''
                )
            );
    register_sidebar(
                array(
                'name' => __('Homepage Ad', 'theme-slug'),
                'id' => 'sidebar-5',
                'description' => __('Widgets for ads on the homepage.', 'theme-slug'),
                'class'         => '',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => ''
                )
            );
    register_sidebar(
                array(
                'name' => __('Homepage', 'theme-slug'),
                'id' => 'sidebar-6',
                'description' => __('Widgets content for the homepage.', 'theme-slug'),
                'class'         => '',
                'before_widget' => '',
                'after_widget'  => '',
                'before_title'  => '',
                'after_title'   => ''
                )
            );
}

add_action('widgets_init', 'theme_slug_widgets_init');

define('ALLOW_UNFILTERED_UPLOADS', true);

/**
 * Capture user login and add it as timestamp in user meta data
 *
 */
function user_last_login($user_login, $user)
{
    update_user_meta($user->ID, 'last_login', time());
}
add_action('wp_login', 'user_last_login', 10, 2);


// add the 'buddypress-activity' support for contest!
add_post_type_support('contest', 'buddypress-activity');

add_post_type_support('submission', 'buddypress-activity');
 
function bp_cpt_tracking_args()
{
    // Check if the Activity component is active before using it.
    if (! bp_is_active('activity')) {
        return;
    }
 
    bp_activity_set_post_type_tracking_args('contest', array(
        'action_id'                => 'new_contest',
        'bp_activity_admin_filter' => __('Published a new contest', 'afriflow'),
        'bp_activity_front_filter' => __('Contests', 'afriflow'),
        'contexts'                 => array( 'activity', 'member' ),
        'activity_comment'         => true,
        'bp_activity_new_post'     => __('%1$s posted a new <a href="%2$s">contest</a>', 'afriflow'),
        'bp_activity_new_post_ms'  => __('%1$s posted a new <a href="%2$s">contest</a>, on the site %3$s', 'afriflow'),
        'comment_action_id'                 => 'new_contest_comment',
        'bp_activity_comments_admin_filter' => __('Commented a contest', 'afriflow'),
        'bp_activity_comments_front_filter' => __('Contests Comments', 'afriflow'),
        'bp_activity_new_comment'           => __('%1$s commented on the <a href="%2$s">contest</a>', 'afriflow'),
        'bp_activity_new_comment_ms'        => __('%1$s commented on the <a href="%2$s">contest</a>, on the site %3$s', 'afriflow'),
        'position'                 => 100,
    ));

    bp_activity_set_post_type_tracking_args('contest', array(
        'action_id'                => 'new_entry',
        'bp_activity_admin_filter' => __('Published a new entry', 'afriflow'),
        'bp_activity_front_filter' => __('Submissionss', 'afriflow'),
        'contexts'                 => array( 'activity', 'member' ),
        'activity_comment'         => true,
        'bp_activity_new_post'     => __('%1$s posted a new <a href="%2$s">entry</a>', 'afriflow'),
        'bp_activity_new_post_ms'  => __('%1$s posted a new <a href="%2$s">entry</a>, on the site %3$s', 'afriflow'),
        'comment_action_id'                 => 'new_blog_page_comment',
        'bp_activity_comments_admin_filter' => __('Commented an entry', 'afriflow'),
        'bp_activity_comments_front_filter' => __('Entries Comments', 'afriflow'),
        'bp_activity_new_comment'           => __('%1$s commented on the <a href="%2$s">entry</a>', 'afriflow'),
        'bp_activity_new_comment_ms'        => __('%1$s commented on the <a href="%2$s">entry</a>, on the site %3$s', 'afriflow'),
        'position'                 => 100,
    ));

    bp_activity_set_post_type_tracking_args('submission', array(
        'action_id'                => 'like_entry',
        'bp_activity_admin_filter' => __('Liked a contest entry', 'afriflow'),
        'bp_activity_front_filter' => __('Submissions', 'afriflow'),
        'contexts'                 => array( 'activity', 'member' ),
        'activity_comment'         => true,
        'bp_activity_new_post'     => __('%1$s posted a new <a href="%2$s">entry</a>', 'afriflow'),
        'bp_activity_new_post_ms'  => __('%1$s posted a new <a href="%2$s">entry</a>, on the site %3$s', 'afriflow')
    ));

    bp_activity_set_post_type_tracking_args('submission', array(
        'action_id'                => 'dislike_entry',
        'bp_activity_admin_filter' => __('Disiked a contest entry', 'afriflow'),
        'bp_activity_front_filter' => __('Submissions', 'afriflow'),
        'contexts'                 => array( 'activity', 'member' ),
        'activity_comment'         => true,
        'bp_activity_new_post'     => __('%1$s posted a new <a href="%2$s">entry</a>', 'afriflow'),
        'bp_activity_new_post_ms'  => __('%1$s posted a new <a href="%2$s">entry</a>, on the site %3$s', 'afriflow')
    ));


    bp_activity_set_post_type_tracking_args('contest', array(
        'action_id'                => 'vote_entry',
        'bp_activity_admin_filter' => __('Voted for a contest entry', 'afriflow'),
        'bp_activity_front_filter' => __('Submissions', 'afriflow'),
        'contexts'                 => array( 'activity', 'member' ),
        'activity_comment'         => true,
        'bp_activity_new_post'     => __('%1$s posted a new <a href="%2$s">entry</a>', 'afriflow'),
        'bp_activity_new_post_ms'  => __('%1$s posted a new <a href="%2$s">entry</a>, on the site %3$s', 'afriflow')
    ));
}
add_action('bp_init', 'bp_cpt_tracking_args');


add_action('customize_register', 'af_customize_register');
function af_customize_register($wp_customize)
{
    $wp_customize->add_setting('fallback_image', array(
        'type' => 'option',
        'capability' => 'manage_options',
        'default' => '',
      ));
    $wp_customize->add_setting('wistia_admin_api_password', array(
      'type' => 'option',
      'capability' => 'manage_options',
      'default' => '',
    ));
    $wp_customize->add_setting('wistia_client_api_password', array(
      'type' => 'option',
      'capability' => 'manage_options',
      'default' => '',
    ));

    $wp_customize->add_setting('jwt_auth_ttl', array(
      'type' => 'option',
      'capability' => 'manage_options',
      'default' => '',
    ));
    
    //All our sections, settings, and controls will be added here
    $wp_customize->add_panel('ui-settings', array(
        'title' => __('Theme Options'),
        'description' => "Set theme reated settings", // Include html tags such as <p>.
        'priority' => 160, // Mixed with top-level-section hierarchy.
      ));
    $wp_customize->add_section("ui-images", array(
            'title' => "Image Settings",
            'panel' => 'ui-settings',
          ));
    $wp_customize->add_control(
        new WP_Customize_Media_Control(
            $wp_customize,
            'default_image',
            array(
                'label'      => __('Select a default image', 'africonvert'),
                'section'    => 'ui-images',
                'settings'   => 'fallback_image',
                'mime_type' => 'image'
            )
        )
    );
    
    $wp_customize->add_panel('wistia', array(
      'title' => __('Wistia Tokens'),
      'description' => "Wistia API Credentials", // Include html tags such as <p>.
      'priority' => 160, // Mixed with top-level-section hierarchy.
    ));
    $wp_customize->add_section("tokens", array(
          'title' => "Tokens",
          'panel' => 'wistia',
        ));
    
    $wp_customize->add_control('wistia_admin_api_password', array(
      'label' => __('Master Password'),
      'type' => 'text',
      'section' => 'tokens',
    ));
    $wp_customize->add_control('wistia_client_api_password', array(
      'label' => __('Client Password'),
      'type' => 'text',
      'section' => 'tokens',
    ));

    //jwt
    $wp_customize->add_panel('jwt', array(
      'title' => __('Authentication Tokens'),
      'description' => "User login options", // Include html tags such as <p>.
      'priority' => 160, // Mixed with top-level-section hierarchy.
    ));
    $wp_customize->add_section("session", array(
          'title' => "Session",
          'panel' => 'jwt',
        ));
    
    $wp_customize->add_control('jwt_auth_ttl', array(
      'label' => __('Login expiration (in seconds)'),
      'type' => 'text',
      'section' => 'session',
    ));
}
