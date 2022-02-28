<?php
/**
 * Class containing utility methods for working with files on filesystem
 *
 * @author Amit Gupta
 *
 * @since  2022-02-22
 */
namespace G3\Utilities\Utilities;

use \G3\Utilities\Traits\Singleton;
use \G3\Utilities\Interfaces\Utility_Driver;

class Files implements Utility_Driver {

	use Singleton;

	/**
	 * Method to get the driver name of the class
	 *
	 * @return string
	 */
	public static function get_driver_name() : string {
		return 'files';
	}

	/**
	 * Method to check if provided file path is valid and if a file exists or not.
	 *
	 * @param string $file_path Physical path of file
	 *
	 * @return bool
	 */
	public function does_exist( string $file_path ) : bool {

		return (
			! empty( $file_path )
			&& file_exists( $file_path )
			&& validate_file( $file_path ) === 0
		);

	}

}  // end class

//EOF
