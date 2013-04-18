<?php
/**
 * The base configurations of the WordPress.
 *
 * This file has the following configurations: MySQL settings, Table Prefix,
 * Secret Keys, WordPress Language, and ABSPATH. You can find more information
 * by visiting {@link http://codex.wordpress.org/Editing_wp-config.php Editing
 * wp-config.php} Codex page. You can get the MySQL settings from your web host.
 *
 * This file is used by the wp-config.php creation script during the
 * installation. You don't have to use the web site, you can just copy this file
 * to "wp-config.php" and fill in the values.
 *
 * @package WordPress
 */

// ** MySQL settings - You can get this info from your web host ** //
/** The name of the database for WordPress */
define('DB_NAME', 'wordpress');

/** MySQL database username */
define('DB_USER', 'wordpressapp');

/** MySQL database password */
define('DB_PASSWORD', '#word!@66*@press');

/** MySQL hostname */
define('DB_HOST', 'localhost');

/** Database Charset to use in creating database tables. */
define('DB_CHARSET', 'utf8');

/** The Database Collate type. Don't change this if in doubt. */
define('DB_COLLATE', '');

/**#@+
 * Authentication Unique Keys and Salts.
 *
 * Change these to different unique phrases!
 * You can generate these using the {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org secret-key service}
 * You can change these at any point in time to invalidate all existing cookies. This will force all users to have to log in again.
 *
 * @since 2.6.0
 */
define('AUTH_KEY',         '==j|6U2}=M.?iX11ukrM[[i0~EZM`/fR)z:n#j!uh^#i,P+;2ZMTP$7_,KP=;aQE');
define('SECURE_AUTH_KEY',  'mO%FB`~dA&PQBb=2#cu!.suXj*wK-fs%^sP-tcPKU_Hhk@L)%[Jn$HTJrWWO+:pf');
define('LOGGED_IN_KEY',    'U p{->CUr4gsu~}Ue}~#|gK8<%;w)!g>G|]|{Jh[PnHFUi&&.t_70BA!Fq ]coXP');
define('NONCE_KEY',        'ZDh%YTboUdJQg}p=S|K1h7z8$xE-hh? ?Sycd+R?*=chf)g~t0~jG|BgSxA!7Tnk');
define('AUTH_SALT',        '|3FXlc)R-%ar+&GHjHbmR/%;-XQxgEUHi}*ElwX*dMH?3ojEk+U.oOlJz}V.X yz');
define('SECURE_AUTH_SALT', '8DI#>~uk}LW|aEn^il^4*SxkaGm&x;kb~SeuR.seP k.,)<9=8yw@blJl>f&b.Uy');
define('LOGGED_IN_SALT',   '{(xY-?w1e4zD9ov2v%}8e-Q1@zg^tF~VcG:? D>b&y}-5sFh{5743l?b)G$dl-5E');
define('NONCE_SALT',       '9%U(?5t@qmn%-![*WJef;n5+-67hwCx-;je21%VA-mZL@RvlS 9:q/K;j_ymw9Ez');

define('FORCE_SSL_ADMIN', true);
define('FORCE_SSL_LOGIN', true);

/**#@-*/

/**
 * WordPress Database Table prefix.
 *
 * You can have multiple installations in one database if you give each a unique
 * prefix. Only numbers, letters, and underscores please!
 */
$table_prefix  = 'wp_';

/**
 * WordPress Localized Language, defaults to English.
 *
 * Change this to localize WordPress. A corresponding MO file for the chosen
 * language must be installed to wp-content/languages. For example, install
 * de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
 * language support.
 */
define('WPLANG', '');

/**
 * For developers: WordPress debugging mode.
 *
 * Change this to true to enable the display of notices during development.
 * It is strongly recommended that plugin and theme developers use WP_DEBUG
 * in their development environments.
 */
define('WP_DEBUG', false);

/* That's all, stop editing! Happy blogging. */

/** Absolute path to the WordPress directory. */
if ( !defined('ABSPATH') )
	define('ABSPATH', dirname(__FILE__) . '/');

/** Sets up WordPress vars and included files. */
require_once(ABSPATH . 'wp-settings.php');
