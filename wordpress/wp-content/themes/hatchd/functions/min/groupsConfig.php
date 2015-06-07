<?php
/**
 * Groups configuration for default Minify implementation
 * @package Minify
 */

/**
 * You may wish to use the Minify URI Builder app to suggest
 * changes. http://yourdomain/min/builder/
 *
 * See http://code.google.com/p/minify/wiki/CustomSource for other ideas
 **/

$base = dirname(dirname(dirname(__FILE__)));

return array(
	"css" => array(
		"$base/bootstrap/css/bootstrap.min.css",
		"$base/css/lightbox.css",
		"$base/css/mobgal.css",
		"$base/css/stackicons-social.css",
		"$base/css/screen.css"
	),
	"js" => array(
		"$base/bootstrap/js/bootstrap.min.js",
		"$base/js/modernizr.min.js",
		"$base/js/lightbox.min.js",
		"$base/js/mobgal.min.js",
		"$base/js/picturefill.js",
		"$base/js/tracking.js",
		"$base/js/fullscreen-polyfill.js",
		"$base/js/isotope.js",
		"$base/js/common.js"
	)
);
