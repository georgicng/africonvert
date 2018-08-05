<?php
function scripts()
{
    
    wp_register_script(
        'angularjs',
        get_stylesheet_directory_uri() . '/js/lib/angular/angular.min.js'
    );

    wp_register_script(
        'angular-ui-router',
        get_stylesheet_directory_uri() . '/js/lib/angular-ui-router/release/angular-ui-router.min.js'
    );

    wp_register_script(
        'angular-resource',
        get_stylesheet_directory_uri() . '/js/lib/angular-resource/angular-resource.min.js'
    );

    
    wp_register_script(
        'ui-bootstrap',
        get_stylesheet_directory_uri() . '/js/lib/angular-bootstrap/ui-bootstrap.js'
    );

    wp_register_script(
        'ui-bootstrap-tpls',
        get_stylesheet_directory_uri() . '/js/lib/angular-bootstrap/ui-bootstrap-tpls.js'
    );
    

    wp_register_script(
        'angularjs-sanitize',
        get_stylesheet_directory_uri() . '/js/lib/angular-sanitize/angular-sanitize.min.js'
    );

    wp_register_script(
        'angularjs-animate',
        get_stylesheet_directory_uri() . '/js/lib/angular-animate/angular-animate.js'
    );
    wp_register_script(
        'ngToast',
        get_stylesheet_directory_uri() . '/js/lib/ngToast/dist/ngToast.min.js'
    );

    wp_register_script(
        'ng-file-upload-shim',
        get_stylesheet_directory_uri() . '/js/lib/ng-file-upload-shim/ng-file-upload-shim.min.js'
    );

    wp_register_script(
        'ng-file-upload',
        get_stylesheet_directory_uri() . '/js/lib/ng-file-upload/ng-file-upload.min.js'
    );

    wp_register_script(
        'loading-bar',
        get_stylesheet_directory_uri() . '/js/lib/angular-loading-bar/build/loading-bar.min.js'
    );
    wp_register_script(
        'ng-addthis',
        get_stylesheet_directory_uri() . '/js/lib/angular-addthis/dist/angular-addthis.min.js'
    );
    wp_register_script(
        'my-jquery',
        get_stylesheet_directory_uri() . '/js/lib/jquery/dist/jquery.min.js'
    );

    wp_register_script(
        'bootstrap',
        get_stylesheet_directory_uri() . '/js/lib/bootstrap/dist/js/bootstrap.min.js'
    );

    wp_register_script(
        'video-js',
        get_stylesheet_directory_uri() . '/js/lib/video.js/dist/video.js'
    );
    wp_register_script(
        'vjs-directive',
        get_stylesheet_directory_uri() . '/js/lib/vjs-video/dist/vjs-video.js'
    );

    wp_register_script(
        'moment-js',
        get_stylesheet_directory_uri() . '/js/lib/moment/moment.js'
    );

    wp_register_script(
        'angular-moment',
        get_stylesheet_directory_uri() . '/js/lib/angular-moment/angular-moment.min.js'
    );

    wp_register_script(
        're-captcha',
        get_stylesheet_directory_uri() . '/js/lib/angular-recaptcha/release/angular-recaptcha.min.js'
    );

    wp_register_script(
        'ng-lodash',
        get_stylesheet_directory_uri() . '/js/lib/ng-lodash/build/ng-lodash.min.js'
    );

     wp_register_script(
        'ng-infinite', 
        get_stylesheet_directory_uri() . '/js/lib/ngInfiniteScroll/build/ng-infinite-scroll.min.js'
    );

    wp_register_script(
        'ng-storage',
        get_stylesheet_directory_uri() . '/js/lib/ngstorage/ngStorage.js'
    );

    wp_register_script(
        'ng-password',
        get_stylesheet_directory_uri() . '/js/lib/angular-password/angular-password.js'
    );

    wp_register_script(
        'ng-page-title',
        get_stylesheet_directory_uri() . '/js/lib/ng-page-title/dist/ng-page-title.js'
    );

    wp_register_script(
        'addthis', '//s7.addthis.com/js/300/addthis_widget.js#pubid={pubid}&async=1'
    );

    wp_register_script(
        'g-analytics', get_stylesheet_directory_uri() . '/js/lib/angular-google-ga/dist/angular-google-ga.js'
    );

    wp_register_script(
        'read-more', get_stylesheet_directory_uri() . '/js/lib/trunk8/trunk8.js'
    );

    wp_enqueue_script(
        'app-script',
        get_stylesheet_directory_uri() . '/js/app/all.js',
        array( 'my-jquery', 'addthis', 'moment-js', 'angularjs', 'angular-ui-router', 'angularjs-sanitize', 'angularjs-animate', 'ui-bootstrap', 'ui-bootstrap-tpls', 'angular-resource', 'ng-lodash', 'ng-infinite', 'ngToast', 'ng-file-upload-shim', 'ng-file-upload', 'loading-bar', 'ng-addthis', 'video-js', 'vjs-directive', 're-captcha', 'angular-moment', 'ng-storage', 'ng-password', 'ng-page-title', 'g-analytics', 'read-more' )
    );

     wp_enqueue_script(
        'custom-script',
        get_stylesheet_directory_uri() . '/js/custom.js',
        array(),
        false,
        true
    );
/**
    wp_enqueue_script(
        'app-modules',
        get_stylesheet_directory_uri() . '/js/app/modules.js',
        array( 'my-jquery', 'addthis', 'angularjs', 'angular-ui-router', 'angularjs-sanitize', 'angularjs-animate', 'mobile-angular-ui', 'angular-resource', 'ng-lodash', 'ngToast', 'ng-file-upload-shim', 'ng-file-upload', 'loading-bar', 'ng-addthis', 'video-js', 'vjs-directive', 're-captcha' )
    );
    wp_enqueue_script(
        'app-routes',
        get_stylesheet_directory_uri() . '/js/app/routes.js',
        array( 'app-modules' )
    );
    wp_enqueue_script(
        'app-services',
        get_stylesheet_directory_uri() . '/js/app/services.js',
        array( 'app-modules' )
    );
    wp_enqueue_script(
        'app-controller',
        get_stylesheet_directory_uri() . '/js/app/controllers.js',
        array( 'app-modules' )
    );
    
*/
    $ajax_nonce = wp_create_nonce( 'ajax-login-nonce');    
    $_SESSION['ajax_security'] = $ajax_nonce;
    $rest_nonce = wp_create_nonce( 'wp_rest' );
    $custom_logo_id = get_theme_mod( 'custom_logo' );
    $image = wp_get_attachment_image_src( $custom_logo_id , 'full' );
    $logo = $image[0];
    wp_localize_script(
        'app-script',
        'local_env',
        array(
            'partials' => trailingslashit( get_template_directory_uri() ) . 'partials/',
            'api_base'   => get_rest_url(),
            'api_url'   => get_rest_url() . 'wp/v2',
            'jwt_url'   => get_rest_url() . 'jwt-auth/v1/token',
            'menu_base'   => get_rest_url() . 'wp-api-menus/v2',
            'widgets_url'   => get_rest_url() . 'wp-rest-api-sidebars/v1/sidebars',
            'home'   => get_home_url(),
            'wistia_api_base' => 'https://api.wistia.com/v1',
            'wistia_api_password' => get_option('wistia_client_api_password'),
            'wistia_upload_url' => 'https://upload.wistia.com/',
            'logo'   => $logo,
            'template_directory' => get_template_directory_uri() . '/',
            'nonce'              => $rest_nonce,
            'is_admin'           => current_user_can('administrator'),
            'user' =>  json_encode(userProfile()),
            'logged_in' => is_user_logged_in(),
            'security' => $ajax_nonce,
            'ajaxurl' => admin_url( 'admin-ajax.php' ),
            'loginAction' => 'af_login',
            'registerAction' => 'af_register',
            'logoutAction' => 'af_logout'
            )
    );
}
add_action( 'wp_enqueue_scripts', 'scripts' );
