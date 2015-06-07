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
//switch databases depending on server
$server = $_SERVER["SERVER_NAME"];
switch($server){
	//the dev server and staging server
	case "dev.local":
		define('DB_NAME','dev_hatchd');
		/** MySQL database username */
		define('DB_USER','root');
		/** MySQL database password */
		define('DB_PASSWORD','root');
		/** MySQL hostname */
		define('DB_HOST', 'localhost');
	break;
	//the live server and all other environments
	default:
		define('DB_NAME','staging_hatchd');
		/** MySQL database username */
		define('DB_USER','hatchd');
		/** MySQL database password */
		define('DB_PASSWORD','');
		/** MySQL hostname */
		define('DB_HOST','localhost');
}
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
define('AUTH_KEY',         '~81(%s,nB!ZX.+^kSc|ZLAb&3JgbAI3hL*gg[N ;-:Gd+>9#Cq4bg)pRj*RsfQBr');
define('SECURE_AUTH_KEY',  'f{SVG+:p#4kc)@%a: $,|oJ+E`@!N 4+J^HC#M-@kPL?Q#PLCzqxawU%bsvt7dNA');
define('LOGGED_IN_KEY',    '!A1Loeh b(Lw2*@7-B(C4{O]*L]S?bVxdj{Jd6`hkYH>@%7O60/SUy!+*6Bqi|^Y');
define('NONCE_KEY',        '&<QP<PEj_1NGzEROSvSN8jN%HX-A=x2cjQg_!~u3m8/-URG|@.48s(,Gb&M(q:<Z');
define('AUTH_SALT',        'IKAqfkEt-Mn<xXxI|)v?@^Q&8V2b-<n>sPw(V~g6A]L[P#EL+l+/pL&2wM`D1(B3');
define('SECURE_AUTH_SALT', 'sw^qchJ[FR7[;P@M}7NBk&,@vtj}GK0g0anEKt_7R8xop1Wcg9>LT-Y+&q=v`t41');
define('LOGGED_IN_SALT',   'CR6=_lQ/X<,?bIE9N-%X*&:QBk5H|Q*%E59XL>a+(|H{m#JqZDDm.BPS5<b]$xgv');
define('NONCE_SALT',       '}.0p#lL<L+~v<6T#/N|~y]T^+q=s#@W$|;vY0^b1|veuv1szkWh)-.6B2B.:^qGG');
/**#@-*/
/**
* WordPress Database Table prefix.
*
* You can have multiple installations in one database if you give each a unique
* prefix. Only numbers, letters, and underscores please!
*/
$table_prefix  = 'pixel_';
/**
* WordPress Localized Language, defaults to English.
*
* Change this to localize WordPress. A corresponding MO file for the chosen
* language must be installed to wp-content/languages. For example, install
* de_DE.mo to wp-content/languages and set WPLANG to 'de_DE' to enable German
* language support.
*/
define('WPLANG', '');
/*
* Enable Caching
*/
define("WP_CACHE",true);
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
