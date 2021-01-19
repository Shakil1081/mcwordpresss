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
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define( 'DB_NAME', 'wordpress' );

/** MySQL database username */
define( 'DB_USER', 'root' );

/** MySQL database password */
define( 'DB_PASSWORD', '' );

/** MySQL hostname */
define( 'DB_HOST', 'localhost' );

/** Database Charset to use in creating database tables. */
define( 'DB_CHARSET', 'utf8mb4' );

/** The Database Collate type. Don't change this if in doubt. */
define( 'DB_COLLATE', '' );

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'U*2J%q9>>WQ1HeuaV`rL5TUj(R`l-60i%: >S$L!*Zf/Fqt}OulLu&G+2xF+@sv4' );
define( 'SECURE_AUTH_KEY',  'Rd~gIB86A+i(uEVcs.D={=71A]af50@<Bv./k7LLoJ3m9M19 p^]9},hJS=&/Vn5' );
define( 'LOGGED_IN_KEY',    'p)eyQ!L9tzt[j*!ul`ndIh5OWZt/hNG=XC76[YC]`dM$W_4W@~30,vy<(ED9j| 6' );
define( 'NONCE_KEY',        'hC AYLyHJXA8^4]|#J,(zoZ},*a~/+z-/rTYzWlaLOh7,$Q(^;8Dor8k1#M>_o0H' );
define( 'AUTH_SALT',        'Q 44,ZD}6w4p6_8fhB%a6wlvpfpL@.%&dr)zCC4rUJE%FKdPA[La27am;B/,*sC,' );
define( 'SECURE_AUTH_SALT', 'F6%{IZpjd=={<S*@YXT>%~ yCsc4!K8|// _.<DN2bs,Z|HM!L4uHz?oTL,a)8]+' );
define( 'LOGGED_IN_SALT',   '&h1;8UUr](kDCj&t8,[%)+`cVE9]|f>1GNcrIAm%JJZWFk^jOH,ENl#DLn]{/ h9' );
define( 'NONCE_SALT',       'rXZ-G/H0voy46}P_5c;3-/4;|qHzw^Y7&{;3)rE&9h>7qPyfCTh>DEV#`xYaqfNs' );

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each
 * a unique prefix. Only numbers, letters, and underscores please!
 */
$table_prefix = 'mcw_';

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
define( 'WP_DEBUG', true );

/* That's all, stop editing! Happy publishing. */

/** Absolute path to the WordPress directory. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Sets up WordPress vars and included files. */
require_once ABSPATH . 'wp-settings.php';
