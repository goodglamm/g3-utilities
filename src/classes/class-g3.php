<?php
/**
 * Class which can be used as the only class for all utilities.
 * All utility classes can be registered as drivers with this class
 * and called without having to deal with different class references.
 *
 * @author Amit Gupta
 *
 * @since  2022-02-02
 */
namespace G3\Utilities;

use \G3\Utilities\Traits\Singleton;
use \ErrorException;

class G3 {

	use Singleton;

	/**
	 * @var array Default drivers which are registered everytime this class is instantiated.
	 */
	protected array $_default_drivers = [
		Utilities\Arrays::class,
		Utilities\Files::class,
		Utilities\Input::class,
		Utilities\Strings::class,
	];

	/**
	 * @var array An array of driver names which are not allowed to be registered.
	 */
	protected array $_reserved_driver_names = [
		'g3',
		'bootstrap',
	];

	/**
	 * @var array An array of all drivers registered
	 */
	protected array $_registry = [];

	/**
	 * Class constructor
	 *
	 * @throws \ErrorException
	 */
	protected function __construct() {
		$this->_register_default_drivers();
	}

	/**
	 * Method to register the default drivers
	 *
	 * @return void
	 *
	 * @throws \ErrorException
	 */
	protected function _register_default_drivers() : void {

		if ( empty( $this->_default_drivers ) ) {
			return;
		}

		foreach( $this->_default_drivers as $class ) {
			$this->register_driver( $class );
		}

	}

	/**
	 * Method to check if a driver name is reserved or not.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	protected function _is_reserved_driver_name( string $name ) : bool {
		return (bool) in_array( $name, $this->_reserved_driver_names, true );
	}

	/**
	 * Method to check if a driver is already registered or not
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public function is_driver_registered( string $name ) : bool {
		return (bool) (
			isset( $this->_registry[ $name ] )
			&& is_object( $this->_registry[ $name ] )
		);
	}

	/**
	 * Method to register a driver
	 *
	 * @param string $class
	 *
	 * @throws \ErrorException
	 */
	public function register_driver( string $class ) : void {

		if ( ! method_exists( $class, 'get_driver_name' ) ) {
			throw new ErrorException(
				sprintf(
					'Class "%1$s" must define the static method "%2$s" to be able to get registered as a driver of %3$s.',
					$class,
					'get_driver_name()',
					static::class
				)
			);
		}

		$name = call_user_func( [ $class, 'get_driver_name' ] );

		if ( $this->_is_reserved_driver_name( $name ) ) {
			throw new ErrorException(
				sprintf(
					'Driver name "%1$s" is reserved and cannot be used. Use another driver name for the class "%2$s"',
					$name,
					$class
				)
			);
		}

		if ( $this->is_driver_registered( $name ) ) {
			throw new ErrorException(
				sprintf(
					'%1$s::%2$s() - A driver by name of "%3$s" is already registered',
					static::class,
					__FUNCTION__,
					$name
				)
			);
		}

		if ( ! method_exists( $class, 'get_instance' ) ) {
			throw new ErrorException(
				sprintf(
					'%1$s::%2$s() - "%4$s" is not a static method defined on "%3$3" class. All driver classes must define "%4$s" as a static method which returns the class object.',
					static::class,
					__FUNCTION__,
					$class,
					'get_instance()'
				)
			);
		}

		$this->_registry[ $name ] = $class::get_instance();

	}

	/**
	 * Magic method to return a driver object when driver name is accessed as a property of class object.
	 *
	 * @param string $name
	 *
	 * @return mixed|void
	 *
	 * @throws \ErrorException
	 */
	public function __get( string $name ) {

		if ( ! $this->is_driver_registered( $name ) ) {
			throw new ErrorException(
				sprintf(
					'"%1$s" driver not registered with class %2$s',
					$name,
					static::class
				)
			);
		}

		return $this->_registry[ $name ];

	}

	/**
	 * Magic method which maps a static method call to a registered driver if it exists.
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return void|mixed
	 */
	public static function __callStatic( string $name, array $args ) {
		return static::get_instance()->{$name};
	}

}

//EOF
