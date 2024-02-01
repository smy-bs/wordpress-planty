<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the web site, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * Localized language
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'local' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', 'root' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8' );

/** The database collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication unique keys and salts.
 *
 * Change these to different unique phrases! You can generate these using
 * the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}.
 *
 * You can change these at any point in time to invalidate all existing cookies.
 * This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',          'D.6vYBvj|ta&z~%f##CGt1;fj|,Tg+X!.sp3Q.uH6Ov]!].m9n<D%1sAM4hb- SK' );
define( 'SECURE_AUTH_KEY',   'h:|m|b=;?`IWXJBYXf?9.XWJb-F62G$nY6&3UD9u@|3is1I@@}ZTsT@`B>T:Peh6' );
define( 'LOGGED_IN_KEY',     'Uj]TaRQ{dZ{/sg%FV%n$o326GXTCf1b&#P5iq15~`P(Aa~L3P5$$cm;Kya^*mqz9' );
define( 'NONCE_KEY',         'H@ae^.dU*2m=/1 !c!5M;U;I=4pzM89Z-?5]%yzldN,U()>(weTzip i^WkF.b+.' );
define( 'AUTH_SALT',         'S2OYI#K@Cn;W@FM.ZZwJy+6ghO>q&d;[(3^f.u:~7]1mXM/L2)pRQbk~R}:5^XRm' );
define( 'SECURE_AUTH_SALT',  '0o.;BOuV?`}vT@?iDMD|zlI:9YN0NM}?@W.@bko1Az=jcltr!+p0SGaEDrEY8ccY' );
define( 'LOGGED_IN_SALT',    'TXmaxItZm9tNX;^<&&m4@+eoL<iODez)Zi1T(*}_/A2&HKZoFy3`ouaL!a{&F$g!' );
define( 'NONCE_SALT',        'B*S-9g0|ki<))ImCj^0jWcr9ql;MXqcqwVmk^hhPW-]r:GX-=;)[]P`oOLflCGjg' );
define( 'WP_CACHE_KEY_SALT', '}I.Rcrxz]H%J3qWV|yM)iU]A6$x#4:c32c|`3D~g1knoylP ,=Q1d1Lg1EcC06$B' );


/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'wp_';


/* Add any custom values between this line and the "stop editing" line. */



/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 *
 * For information on other constants that can be used for debugging,
 * visit the documentation.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
if ( ! defined( 'WP_DEBUG' ) ) {
	define( 'WP_DEBUG', false );
}

define( 'WP_ENVIRONMENT_TYPE', 'local' );
/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
