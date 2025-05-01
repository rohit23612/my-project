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
define( 'DB_NAME', 'folis' );

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
define( 'AUTH_KEY',         '??{#ks30W~U*j%Sz<s_:jZm-)7U7WyJ[?N-W7s?T:yG^@n#<p,]o1k!xTuUC,/_%' );
define( 'SECURE_AUTH_KEY',  'B=l:;]sKWEaBQ%ss-AEZT[X.BoNxiMbDcrD9XUfDH`lo;-%HC?NHQ6={ .l7Fy:k' );
define( 'LOGGED_IN_KEY',    'P|nro?y2Uz;>&T*<)Tz^f$f?F*XJ=3~_Y-Z(dhn E`xm~+MynE(e8>VP&0E-Hzpn' );
define( 'NONCE_KEY',        '@LM1x0Ts=_gL`r|<av;kS9Q Fc@/X7<zu/ %$P)aJ6IB{S?ysvVr5:E8b=7h%p:Z' );
define( 'AUTH_SALT',        'g~VA{Z?)NHBsB9$RTF8=0H2M^kRP.4H2@L9?yPE8jQ`1Es8{zF#!^K7OJO+R}w*c' );
define( 'SECURE_AUTH_SALT', 'tZaHaJB(RC#O1yNvSNv6}?;WaK[3~MjIPt%a,8jFdIIVFaxFH|IENps<@]W5SohM' );
define( 'LOGGED_IN_SALT',   'iK{,_5,qQt/t7Ih`|}K!;?I4j^oO?&i4EBrAs;,D9TSgkZE|/F*}=mHuTPh]faYb' );
define( 'NONCE_SALT',       '4,Gon89/?~JBG~8r]I*5.KmE?MMuc_fsa.Ol,MJ9C3+mBt*ailY^>4-t-/a{{Ps(' );

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
define( 'WP_DEBUG', false );

/* Add any custom values between this line and the "stop editing" line. */



/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
