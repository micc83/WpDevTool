<?php
/**
 * Redirect all emails sent through wp_mail to a custom address
 *
 * @since 	0.0.3
 * @param 	string 	$email	The catch all email
 */
function wpdevtool_redirect_wp_mail( $email ) {
	
	if ( !get_option( 'wpdevtool_redirect_emails' ) )
		return $email;
		
	$email['to'] = get_option( 'wpdevtool_redirect_email' );
	
	return $email;
}
add_filter( 'wp_mail', 'wpdevtool_redirect_wp_mail' );
