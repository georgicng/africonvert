<?php
/**
 * Plugin Name: WP REST API Batch Requests
 * Description: Enabled a multi / batch requests endpoint for the WP RES API
 * Author: Joe Hoyle
 * Version: 1.0-alpha1
 * Plugin URI: https://github.com/WP-API/WP-API
 * License: GPL2+
 */
/**
 * Documentation
 *
 * The endpoint `/wp-json/wp/v2/multi` allows a client to send multiple requests to the
 * server for processing in a single HTTP request. Each child request can have it's own
 * HTTP Method, Body, Headers, URL Params and Path.
 *
 * If the HTTP Method, Body or Headers are not supplied for each request, they will be
 * inherited from the HTTP request.
 *
 * The client must send an array of "request" objects in the `requests` param, the "request"
 * object can be a Path for shorthand (enabling the inheritance of HTTP Method, Body and Headers
 * from the HTTP request.)
 *
 * When not supplying only string Paths in the `requests` array, the "request" object must be in the
 * form of `{ path: '/some/path', headers: [], body: {}, method: 'POST' }`
 *
 * Example 1: Fetch all recent posts and all recent pages
 *
 * `curl example.com/wp-json/wp/v2/multi?requests[]=/wp/v2/posts&requests[]=/wp/v2/pages`
 *
 * Example 2: Delete 2 posts
 *
 * `curl -X DELETE example.com/wp-json/wp/v2/multi?requests[]=/wp/v2/posts/1&requests[]=/wp/v2/posts/2`
 *
 * Responses are in the form of a WP REST API enveloped response object. The HTTP request will return an
 * array of enveloped response objects in the order the `requests` parameter was passed.
 */
add_action( 'rest_api_init', 'wp_api_batch_register_route' );
function wp_api_batch_register_route() {
	register_rest_route( 'wp/v2', 'multi', array(
		'methods' => WP_REST_Server::ALLMETHODS,
		'args'    => array(
			'requests' => array(
			),
		),
		'callback' => 'wp_api_batch_serve_request',
	));
}
function wp_api_batch_serve_request( $request ) {
	global $wp_rest_server;
	$responses = array();
	foreach ( $request['requests'] as $single_request ) {
		if ( is_string( $single_request ) ) {
			$single_request = array(
				'path'    => $single_request,
			);
		}
		$single_request = array_merge( $single_request, array(
			'method'  => $_SERVER['REQUEST_METHOD'],
			'body'    => $_POST,
			'headers' => $_SERVER,
		));
		$parsed_url = parse_url( $single_request['path'] );
		$rest_request = new WP_REST_Request( $single_request['method'], $parsed_url['path'] );
		$rest_request->set_headers( $wp_rest_server->get_headers( $single_request['headers'] ) );
		$rest_request->set_body_params( $single_request['body'] );
		if ( ! empty( $parsed_url['query'] ) ) {
			$rest_request->set_query_params( parse_str( $parsed_url['query'] ) );
		}
		$result = $wp_rest_server->dispatch( $rest_request );
		$result = rest_ensure_response( $result );
		$result = apply_filters( 'rest_post_dispatch', rest_ensure_response( $result ), $wp_rest_server, $rest_request );
		$result = $wp_rest_server->envelope_response( $result, $rest_request->get_param( '_embed' ) );
		$responses[] = $result;
	}
	return $responses;
}