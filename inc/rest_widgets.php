<?php

function cpt_widget(WP_REST_Request $request)
{

    $the_widget = '';

    $args = $request->get_param('args');
    $instance = $request->get_param('instance');
    $widget = $request->get_param('id');
	// Start output buffering to capture WP_Widget::widget() output.
    ob_start();
    the_widget($widget, $instance, $args);
    $the_widget = ob_get_contents();
    ob_end_clean();

    // Returns a string of the widgets output.
    return array('content' => $the_widget);

}

add_action('rest_api_init', function () {
    register_rest_route('wp/v2', '/widgets/(?P<id>\w+)', array(
        'methods' => 'POST',
        'callback' => 'cpt_widget',
        'args' => array(
            'args' => array(
                'default' => array(),
                'validate_callback' => function ($param, $request, $key) {
                    return is_array($param);
                },
            ),
            'instance' => array(
                'default' => array(),
                'validate_callback' => function ($param, $request, $key) {
                    return is_array($param);
                },
            ),
        ),
    ));
});
