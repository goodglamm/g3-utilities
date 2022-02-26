<?php
/**
 * Autoloader for PHP classes.
 * This class can be used to register a PHP resource and its root path to enable
 * class autoloading for that namespace.
 *
 * @author Amit Gupta
 *
 * @since  2021-12-06
 */

namespace G3\Utilities;

use \ErrorException;

class Autoloader {

	/**
	 * @var string Namespace root registered for current instance
	 */
	protected string $_namespace_root = '';

	/**
	 * @var string Root directory path for the namespace root
	 */
	protected string $_dir_path = '';

	/**
	 * Class constructor
	 *
	 * @param string $namespace_root
	 * @param string $dir_path
	 *
	 * @throws \ErrorException
	 */
	public function __construct( string $namespace_root, string $dir_path ) {

		$namespace_root = trim( $namespace_root, '\\' );
		$dir_path       = str_replace( '\\', '/', $dir_path );
		$dir_path       = rtrim( $dir_path, '/' );

		if ( empty( $namespace_root ) || empty( $dir_path ) ) {
			throw new ErrorException(
				sprintf(
					'%s: Both Namespace and Directory Path are required when registering Namespace for PHP class autoloading.',
					static::class
				)
			);
		}

		$this->_namespace_root = $namespace_root;
		$this->_dir_path       = $dir_path;

	}

	/**
	 * Factory method for quick Namespace registration
	 *
	 * @param string $namespace_root
	 * @param string $dir_path
	 *
	 * @return void
	 *
	 * @throws \ErrorException
	 */
	public static function register( string $namespace_root, string $dir_path ) : void {

		$instance = new static( $namespace_root, $dir_path );

		spl_autoload_register( [ $instance, 'load_resource' ] );

	}

	/**
	 * Method to check if provided file path is valid and if a file exists or not.
	 *
	 * @param string $file_path Physical path of file
	 *
	 * @return bool
	 */
	protected function _file_exists( string $file_path ) : bool {

		return (
			! empty( $file_path )
			&& file_exists( $file_path )
			&& validate_file( $file_path ) === 0
		);

	}

	/**
	 * Method to load a PHP resource along the path of its registered directory.
	 *
	 * This method is registered with spl_autoload_register() for auto-loading
	 * PHP resources and is not to be used directly.
	 *
	 * @param string $resource
	 *
	 * @return void
	 */
	public function load_resource( string $resource = '' ) : void {

		if (
			empty( $resource )
			|| empty( $this->_namespace_root )
			|| empty( $this->_dir_path )
		) {
			return;
		}

		$resource = trim( $resource, '\\' );

		if ( empty( $resource ) || strpos( $resource, $this->_namespace_root ) !== 0 ) {
			// not our namespace, bail out
			return;
		}

		$og_resource = $resource;

		$resource = substr( $resource, strlen( $this->_namespace_root ) );
		$resource = strtolower( trim( $resource, '\\' ) );

		$path = explode(
			'\\',
			str_replace( '_', '-', $resource )
		);

		$class_path  = implode( '/', $path );
		$file_prefix = 'class';  // default file prefix

		if ( strpos( $class_path, 'traits' ) !== false ) {
			$file_prefix = 'trait';
		} elseif ( strpos( $class_path, 'interfaces' ) !== false ) {
			$file_prefix = 'interface';
		}

		$class_path = explode( '/', $class_path );

		$class_path[ count( $class_path ) - 1 ] = sprintf(
			'%s-%s',
			strtolower( $file_prefix ),
			$class_path[ count( $class_path ) - 1 ]
		);

		$class_path = implode( '/', $class_path );

		$resource_path = sprintf(
			'%1$s/classes/%2$s.php',
			untrailingslashit( $this->_dir_path ),
			$class_path
		);

		if ( $this->_file_exists( $resource_path ) ) {
			require_once $resource_path;  // phpcs:ignore
		}

	}

}  // end of class

//EOF
