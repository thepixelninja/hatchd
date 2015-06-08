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
		"$base/bootstrap/css/bootstrap.css",
		"$base/css/lightbox.css",
		"$base/css/mobgal.css",
		"$base/css/stackicons-min.css",
		"$base/css/screen.css"
	),
	"js" => array(
		"$base/bootstrap/js/bootstrap.min.js",
		"$base/js/modernizr.min.js",
		"$base/js/lightbox.min.js",
		"$base/js/mobgal.min.js",
		"$base/js/picturefill.js",
		"$base/js/fryed.googlemap.js",
		"$base/js/common.js"
	)
);
