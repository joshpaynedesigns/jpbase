<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the
 * installation. You don't have to use the web site, you can
 * copy this file to "wp-config.php" and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * MySQL settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://codex.wordpress.org/Editing_wp-config.php
 *
 * @package WordPress
 */

// ** MySQL settings ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', 'root' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         'DMkUH8eiXjfLhtxnSKMlgYBZysFjfqBQZ7uReVQAnE87RwZFCSMS3/4kuWzPvvGwHJi5UOpKQJV6MR7RAI99Pw==');
define('SECURE_AUTH_KEY',  'iBfD0EwCPzp9xt0zaJmyloocBKpPBz5KkSCcHMZ+G/dM4YDeGoxmj9xUXPOVcil6SdThJTcEjx9eOJHdLn+m9Q==');
define('LOGGED_IN_KEY',    'pfTcHZIRJLUbak7p7FAIpq2mzIILUci6B6zsXKuZLZ+3L+WHu5bGZAEyw9S93vV7kDL9pH9DR7MHzTvDDq9N3g==');
define('NONCE_KEY',        '3A1qyg/RhC6jSGpqGV/J+tMaIEaKKVXLtITOaT3xokD2y2NBoEg4Du3olkIG7vsEM01BtsNYV6T7hEQ7q+L5EQ==');
define('AUTH_SALT',        'P9rNZh6Jx1+8qIFQfuNB6wfslhz3m52nlH92y7jzezEjC7MADL0BYlEB6tuR5QZpG/j/GLvhtqaJ4klxcIEpdA==');
define('SECURE_AUTH_SALT', '5UcVUcakK3QeuwIFyq52oeAJGd2KXq1VTfA1NPF3tIjr33eOtqSp53WWfH6UURcED54aDrGwzHd5KNuM1o6DoA==');
define('LOGGED_IN_SALT',   'riJ6N6GkBejMX2Osb2qiG8/gC0HbrtYzpFA7j7QkIJJi/X4TqmP0SLKgz4MJ0YVTodPAHGIcSmFin8PDk3kHbg==');
define('NONCE_SALT',       'wEy2kQvQv5MyEgUoXq5kWJOwC9KSB1pE+/Dd9axHj8XH9qAuQ1/4r35HbRbOaxt11Q4gYNwzjh/nYp2ay0Gu1g==');

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


define( 'WP_DEBUG', false );


/* Inserted by Local by Flywheel. See: http://codex.wordpress.org/Administration_Over_SSL#Using_a_Reverse_Proxy */
if ( isset( $_SERVER['HTTP_X_FORWARDED_PROTO'] ) && $_SERVER['HTTP_X_FORWARDED_PROTO'] === 'https' ) {
	$_SERVER['HTTPS'] = 'on';
}

/* Inserted by Local by Flywheel. Fixes $is_nginx global for rewrites. */
if ( ! empty( $_SERVER['SERVER_SOFTWARE'] ) && strpos( $_SERVER['SERVER_SOFTWARE'], 'Flywheel/' ) !== false ) {
	$_SERVER['SERVER_SOFTWARE'] = 'nginx/1.10.1';
}
define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) )
	define( 'ABSPATH', dirname( __FILE__ ) . '/' );

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
