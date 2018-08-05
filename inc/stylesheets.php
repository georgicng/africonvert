<?php
function stylesheets()
{
    wp_enqueue_style(
        'font-awesome',
        get_stylesheet_directory_uri() . '/fontAwesome/css/font-awesome.css'
    );

    wp_enqueue_style(
        'bootstrap',
        get_stylesheet_directory_uri() . '/css/bootstrap.min.css'
    );
    
    wp_enqueue_style(
        'main',
        get_stylesheet_directory_uri() . '/style.css'
    );
    /*
        wp_enqueue_style(
                'main-sub',
                get_stylesheet_directory_uri() . '/css/style2.css'
        );
        wp_enqueue_style(
                'profile',
                get_stylesheet_directory_uri() . '/css/profile.css'
        );
        wp_enqueue_style(
                'blog',
                get_stylesheet_directory_uri() . '/css/blog.css'
        );
        wp_enqueue_style(
                'search',
                get_stylesheet_directory_uri() . '/css/search.css'
        );
        wp_enqueue_style(
                'vote',
                get_stylesheet_directory_uri() . '/css/vote.css'
        );
        */
    wp_enqueue_style(
        'font',
        get_stylesheet_directory_uri() . '/css/font.css'
    );

    wp_enqueue_style(
        'ngToast-css',
        get_stylesheet_directory_uri() . '/js/lib/ngToast/dist/ngToast.min.css'
    );

    wp_enqueue_style(
        'loading-bar-css',
        get_stylesheet_directory_uri() . '/js/lib/angular-loading-bar/build/loading-bar.min.css'
    );

    wp_enqueue_style(
        'vjs-css',
        get_stylesheet_directory_uri() . '/js/lib/video.js/dist/video-js.css'
    );
}

add_action('wp_enqueue_scripts', 'stylesheets');
