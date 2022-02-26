<?php
/**
 * Class to provide toolkit for retrieving and manipulating user input.
 *
 * @author Amit Gupta
 *
 * @since  2022-01-18
 */
namespace G3\Utilities\Utilities;

use \G3\Utilities\Traits\Singleton;
use \G3\Utilities\Interfaces\Utility_Driver;
use \ErrorException;

class Input implements Utility_Driver {

	use Singleton;

	/**
	 * Method to get the driver name of the class
	 *
	 * @return string
	 */
	public static function get_driver_name() : string {
		return 'input';
	}

	/**
	 * Method which powers __callStatic() and __call() magic methods allowing syntactical sugar.
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return mixed
	 *
	 * @throws \ErrorException
	 */
	protected static function _dispatch( string $name, array $args ) {

		switch ( $name ) {

			case 'get':
				$type = INPUT_GET;
				break;

			case 'post':
				$type = INPUT_POST;
				break;

			case 'cookie':
				$type = INPUT_COOKIE;
				break;

			case 'server':
				$type = INPUT_SERVER;
				break;

			case 'env':
				$type = INPUT_ENV;
				break;

			default:
				throw new ErrorException(
					sprintf(
						'Called non-existent method %s::%s()',
						static::class,
						$name
					)
				);

		}

		$args = Arrays::get_instance()->inject( $type, 0, $args );

		return static::get_instance()->filter( ...$args );

	}

	/**
	 * Magic method implementation to allow syntactical sugar like Input::get(),
	 * Input::post(), Input::server(), etc.
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return mixed
	 *
	 * @throws \ErrorException
	 */
	public static function __callStatic( string $name, array $args ) {
		return static::_dispatch( $name, $args );
	}

	/**
	 * Magic method implementation to allow syntactical sugar like Input::get_instance()->get(),
	 * Input::get_instance()->post(), Input::get_instance()->server(), etc.
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return mixed
	 *
	 * @throws \ErrorException
	 */
	public function __call( string $name, array $args ) {
		return static::_dispatch( $name, $args );
	}

	/**
	 * Method to get data from input sources like URL query-string, data from form posts,
	 * data from cookies or server vars, etc.
	 *
	 * This method works same as PHP's inbuilt function filter_input() and has same signature
	 * but unlike the original PHP function, this works on CLI too which comes in handy when
	 * unit testing code which uses this method - saves the trouble of mocking this method
	 * in unit tests.
	 *
	 * @param int       $type     One of `INPUT_GET`, `INPUT_POST`, `INPUT_COOKIE`, `INPUT_SERVER`, or `INPUT_ENV`.
	 * @param string    $var_name Name of a variable to get.
	 * @param int       $filter   The ID of the filter to apply.
	 * @param array|int $options  Associative array of options or bitwise disjunction of flags. If filter accepts options, flags can be provided in "flags" field of array.
	 *
	 * @return mixed Value of the requested variable on success, FALSE if the filter fails, or NULL if the `var_name` variable is not set. If the flag `FILTER_NULL_ON_FAILURE` is used, it returns FALSE if the `var_name` variable is not set and NULL if the filter fails.
	 *
	 * @codeCoverageIgnore
	 */
	public function filter( int $type, string $var_name, int $filter = FILTER_DEFAULT, $options = 0 ) {

		/*
		 * filter_input() does not work on CLI, so let's isolate code
		 * execution based on whether we are on CLI at the moment or not.
		 */
		if ( php_sapi_name() !== 'cli' ) {

			$value_to_return = filter_input( $type, $var_name, $filter, $options );

			// This just adds a bit more sanitization on the data
			// than what PHP's filter does on its own.
			if ( FILTER_SANITIZE_STRING === $filter ) {
				$value_to_return = sanitize_text_field( $value_to_return );
			}

			return $value_to_return;

		}

		/*
		 * If this is being run then that means current code is running
		 * on CLI. Let's allow for and use the super-globals to simulate
		 * data as this is very likely running under a unit test.
		 */

		// We don't want this flagged by any phpcs ruleset
		// phpcs:disable

		switch ( $type ) {

			case INPUT_GET:
				$value_to_return = $_GET[ $var_name ] ?? null;
				break;

			case INPUT_POST:
				$value_to_return = $_POST[ $var_name ] ?? null;
				break;

			case INPUT_COOKIE:
				$value_to_return = $_COOKIE[ $var_name ] ?? null;
				break;

			case INPUT_SERVER:
				$value_to_return = $_SERVER[ $var_name ] ?? null;
				break;

			case INPUT_ENV:
				$value_to_return = $_ENV[ $var_name ] ?? null;
				break;

			default:
				$value_to_return = null;
				break;

		}

		// phpcs:enable

		return filter_var( $value_to_return, $filter, $options );

	}

}  // end class

//EOF
