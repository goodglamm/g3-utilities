<?php
/*
Plugin Name: G3 Utilities
Plugin URI: https://www.goodglamm.com/
Description: Collection of utility code/libraries for use with WordPress plugins/themes.
Version: 1.0.0
Author: Good Glamm Group Engineering, Amit Gupta
License: GPL v2
*/

define( 'G3_UTILITIES_ROOT', __DIR__ );
define( 'G3_UTILITIES_VERSION', '1.0.0' );

function g3_utilities_loader() : void {
	require_once G3_UTILITIES_ROOT . '/dependencies.php';
}

g3_utilities_loader();

//EOF
