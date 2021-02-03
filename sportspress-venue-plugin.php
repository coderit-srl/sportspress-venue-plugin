<?php

/*
Plugin Name: SportsPress Venue REST API
Plugin URI: http://URI_Of_Page_Describing_Plugin_and_Updates
Description: A simple plugin to get SportsPress Venue data via REST API
Version: 1.1
Author: CODERIT srl
Author URI: https://coderit.it
License: BSD-3-Clause
*/

add_action( 'rest_api_init', function () {
	register_rest_route( 'sportspress-venue/v1', '/venue/(?P<id>\d+)', array(
		'methods'  => 'GET',
		'callback' => 'get_item',
		'args'     => array(
			'id' => array(
				'validate_callback' => function ( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
		),
	));
	register_rest_route( 'sportspress-venue/v1', '/elementor/(?P<id>\d+)', array(
		'methods'  => 'GET',
		'callback' => 'get_elementor',
		'args'     => array(
			'id' => array(
				'validate_callback' => function ( $param, $request, $key ) {
					return is_numeric( $param );
				}
			),
		),
	));
} );

/**
 * Get one item from the collection
 *
 * @param WP_REST_Request $request Full data about the request.
 *
 * @return WP_Error|WP_REST_Response
 */
function get_item( $request ) {
	//get parameters from request
	$params    = $request->get_params();
	$id        = $params['id'];
	$term_meta = get_option( "taxonomy_$id" );
	if ( is_array( $term_meta ) ) {
		$latitude  = spvp_array_value( $term_meta, 'sp_latitude', '-37.8165647' );
		$longitude = spvp_array_value( $term_meta, 'sp_longitude', '144.9475055' );
		$address   = spvp_array_value( $term_meta, 'sp_address', '' );
		$data      = array( 'id' => $id, 'lat' => $latitude, 'lng' => $longitude, 'address' => $address );

		return new WP_REST_Response( $data, 200 );
	} else {
		return new WP_Error( 404, 'Not found', array( 'id' => $id ) );
	}
}

function get_elementor( $request ) {
	//get parameters from request
	$params    = $request->get_params();
	$id        = $params['id'];
	if ( $id ) {
		$data      = get_post_meta($id, '_elementor_data', true);
		return new WP_REST_Response( array('id' => $id, 'data'=> json_decode($data)), 200 );
	} else {
		return new WP_Error( 404, 'Not found', array( 'id' => $id ) );
	}
}


function spvp_array_value( $arr = array(), $key = 0, $default = null ) {
	return ( isset( $arr[ $key ] ) ? $arr[ $key ] : $default );
}
