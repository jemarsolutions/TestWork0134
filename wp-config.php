<?php
/**
 * The base configuration for WordPress
 *
 * The wp-config.php creation script uses this file during the installation.
 * You don't have to use the website, you can copy this file to "wp-config.php"
 * and fill in the values.
 *
 * This file contains the following configurations:
 *
 * * Database settings
 * * Secret keys
 * * Database table prefix
 * * ABSPATH
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/
 *
 * @package WordPress
 */

// ** Database settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'testwork0134' );

/** Database username */
define( 'DB_USER', 'root' );

/** Database password */
define( 'DB_PASSWORD', '' );

/** Database hostname */
define( 'DB_HOST', 'localhost' );

/** Database charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

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
define( 'AUTH_KEY',         '^/CcU!JmB&-1k=v0DWyvHN, Vu#P8y@f@d03`x[ruy&$xoyPX-rb)fa4CO%=Ux{S' );
define( 'SECURE_AUTH_KEY',  'w7v+h5QA!mP0snN[tu`9r2J6EL06K&2nllpz1{`[Y?)/%%#`{m(8%a5SA}dpL=];' );
define( 'LOGGED_IN_KEY',    'I$y}opQw?=vD}.p?_IK7Bh>)STGNX;~* @^w%RihVs(mUFN%0&wsK WXmtxj8bOK' );
define( 'NONCE_KEY',        'y|:sRV1av$MI+#^k 1lr]K1;(,],0L w!7^|PX`>]=q9>5k<Js]|y,$PMY*1dphl' );
define( 'AUTH_SALT',        'P`iX1%D}MQf:76V?z#WwpXZriowbW^Ofyc::lN7}zD9^H=F;Z[tgBs1q/+?gHeBy' );
define( 'SECURE_AUTH_SALT', 'g;9|%/dp`Jv`1zLn``8=,@7{uQ1~WItYr[H &*_.:7`k)r0;8ZRX5] b-z2fZqh2' );
define( 'LOGGED_IN_SALT',   '8exn/<7~)p;wd]Vz&AGE</%up#TYD_]_O~A01&^KFNk7v7)rlxR#0%*H5[9c!|lN' );
define( 'NONCE_SALT',       '>@y+]usSjXz#,IHM$w8WCy3Or4RI{UMCXfc3oAv<}S4WL3,w9pdD/MR(&;{qVx6A' );

/**#@-*/

/**
 * WordPress database table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 *
 * At the installation time, database tables are created with the specified prefix.
 * Changing this value after WordPress is installed will make your site think
 * it has not been installed.
 *
 * @link https://developer.wordpress.org/advanced-administration/wordpress/wp-config/#table-prefix
 */
$table_prefix = 'wp_';

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
 * @link https://developer.wordpress.org/advanced-administration/debug/debug-wordpress/
 */
define('WP_DEBUG', true);
define('WP_DEBUG_LOG', true);
define('WP_DEBUG_DISPLAY', true);

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
