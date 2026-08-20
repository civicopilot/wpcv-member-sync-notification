<?php
/**
 * Plugin Name: WPCV Member Sync User Notification
 * Description: Sends the WordPress new-user notification when CiviCRM WP Member Sync creates a user.
 * Version:     0.1
 * Author:      Civicopilot
 * Author URI:  https://civicopilot.com/
 * License:     GPL-3.0-or-later
 * Text Domain: wpcv-member-sync-notification
 */

defined( 'ABSPATH' ) || exit;

// Notification scope can be overridden in wp-config.php with admin, user, or both.
if ( ! defined( 'WPCV_MEMBER_SYNC_NOTIFICATION_SCOPE' ) ) {
	define( 'WPCV_MEMBER_SYNC_NOTIFICATION_SCOPE', 'user' );
}

/**
 * Suppress the admin email during a user-only notification.
 *
 * Some notification plugins, including Better Notifications for WP (BNFW),
 * prepare an admin email even when wp_new_user_notification() is called with
 * the "user" scope. BNFW skips that email when its recipient is empty.
 *
 * @see https://wordpress.org/plugins/bnfw/
 *
 * @param array $email Admin notification email data.
 * @return array
 */
function wpcv_member_sync_suppress_admin_notification( $email ) {
	$email['to'] = '';
	return $email;
}

/**
 * Send WordPress's new-user notification after CiviCRM WP Member Sync creates a user.
 *
 * Member Sync creates users with wp_insert_user(), but does not call
 * wp_new_user_notification(). This callback supplies that missing step. Better
 * Notifications for WP can then replace the standard WordPress message with
 * its configured "New User Registration - For User" notification.
 *
 * The post-insert action runs before Member Sync restores its user-sync
 * filters. It is intended for follow-up operations that may update the new
 * user, including generation of the password-setup key.
 *
 * @param array        $civi_contact CiviCRM contact data. Not needed here.
 * @param int|WP_Error $user_id      ID returned by wp_insert_user(), or an error.
 * @return void
 */
function wpcv_member_sync_trigger_user_notification( $civi_contact, $user_id ) {
	// Member Sync passes through the result of wp_insert_user().
	if ( is_wp_error( $user_id ) ) {
		return;
	}

	$allowed_scopes = array( 'admin', 'user', 'both' );
	$scope          = in_array( WPCV_MEMBER_SYNC_NOTIFICATION_SCOPE, $allowed_scopes, true ) ? WPCV_MEMBER_SYNC_NOTIFICATION_SCOPE : 'user';

	// Limit admin-recipient suppression to user-only notification calls.
	if ( 'user' === $scope ) {
		add_filter( 'wp_new_user_notification_email_admin', 'wpcv_member_sync_suppress_admin_notification' );
	}

	wp_new_user_notification( $user_id, null, $scope );

	if ( 'user' === $scope ) {
		remove_filter( 'wp_new_user_notification_email_admin', 'wpcv_member_sync_suppress_admin_notification' );
	}
}

// Run immediately after CiviCRM Member Sync inserts a new WordPress user.
add_action( 'civi_wp_member_sync_post_insert_user', 'wpcv_member_sync_trigger_user_notification', 10, 2 );
