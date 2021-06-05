<?php

/**
 * Plugin Name: Simple Giveaways Webhook Extension
 * Version:     1.1.1
 * Author:      Viktor Lavron
 * Author URI:  http://lavron.dev
 *
 */
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

require( __DIR__ . '/includes/admin-settings.php' );


add_action( 'sg_process_registration', 'vl_add_webhook' );
function vl_add_webhook( $registration ) {

    $phone_placeholder = '+123456789';

    $url         = get_option( 'giveasap_webhook' )['url'];
    $posted_data = $registration->posted_data['sg_form'];

    if ( ! isset( $posted_data['phone'] ) || $posted_data['phone'] == '' ) {
        $posted_data['phone'] = $phone_placeholder;
    }

    if ( isset( $posted_data['user_email'] ) ) {
        if ( ! isset( $posted_data['email'] ) ) {
            $posted_data['email'] = $posted_data['user_email'];
        }
        unset ( $posted_data['user_email'] );
    }

    $posted_data = json_encode( $posted_data );


    $curl = curl_init();

    curl_setopt_array( $curl, array(
        CURLOPT_URL            => $url,
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING       => '',
        CURLOPT_MAXREDIRS      => 10,
        CURLOPT_TIMEOUT        => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION   => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST  => 'POST',
        CURLOPT_POSTFIELDS     => $posted_data,
        CURLOPT_HTTPHEADER     => array(
            'Content-Type: application/json'
        ),
    ) );

    $response = curl_exec( $curl );

//    vl_update_report_page( $posted_data );

    curl_close( $curl );

}
