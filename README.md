# WPCV Member Sync User Notification

Sends the WordPress new-user notification when CiviCRM WP Member Sync creates a user.

## Description

WPCV Member Sync User Notification connects CiviCRM WP Member Sync user creation to WordPress's standard new-user notification system.

The notification is sent only to the newly created user by default. No administrator notification is requested.

The plugin can be used with [Better Notifications for WP (BNFW)](https://wordpress.org/plugins/bnfw/) to provide a customized HTML welcome email. Without BNFW, WordPress sends its standard new-user notification.

## Notification Scope

The default notification scope is `user`. To request only the administrator notification or both notifications, define one of the following in `wp-config.php`:

```php
define( 'WPCV_MEMBER_SYNC_NOTIFICATION_SCOPE', 'admin' );
define( 'WPCV_MEMBER_SYNC_NOTIFICATION_SCOPE', 'both' );
```

## Requirements

- [WordPress](https://wordpress.org/download/)
- [CiviCRM](https://wordpress.org/plugins/civicrm/)
- [CiviCRM WP Member Sync](https://wordpress.org/plugins/civicrm-wp-member-sync/)

## Credits

Many thanks to Christian Wach's example notification trigger for which this plugin is based on [CiviCRM Member Sync Notify User](https://gist.github.com/christianwach/79221ab00613e2c4e5a3802481d6aac5).