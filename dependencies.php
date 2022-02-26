<?php
/**
 * Dependencies for g3-utilities package
 *
 * @author Amit Gupta
 *
 * @since  2021-12-06
 */
require_once G3_UTILITIES_ROOT . '/src/classes/class-autoloader.php';

// Register package's namespace for resource auto-loading
\G3\Utilities\Autoloader::register( '\G3\Utilities', __DIR__ . '/src' );


/*
 * Class aliases
 */
class_alias( '\G3\Utilities\G3', 'G3', true );

//EOF
