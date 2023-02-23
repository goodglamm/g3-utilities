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
		'factory'   => [
		],
		'singleton' => [
			Utilities\Arrays::class,
			Utilities\Files::class,
			Utilities\Input::class,
			Utilities\Strings::class,
			Utilities\Time::class,
		],
	];

	/**
	 * @var array An array of driver names which are not allowed to be registered.
	 */
	protected array $_reserved_driver_names = [
		'g3',
		'bootstrap',
	];

	/**
	 * @var array An array of all registered drivers
	 */
	protected array $_registry = [
		'factory'   => [],
		'singleton' => [],
	];

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

		foreach( $this->_default_drivers as $type => $drivers ) {

			$is_factory = ( 'factory' === $type );

			foreach ( $drivers as $driver ) {
				$this->register_driver( $driver, $is_factory );
			}

		}

	}

	/**
	 * Method to check if a driver name is that of a non Singleton driver or not.
	 *
	 * @param string $name
	 *
	 * @return bool Returns TRUE if driver is registered as a non-Singleton else FALSE.
	 */
	protected function _is_factory_driver( string $name ) : bool {
		return ( ! empty( $this->_registry['factory'][ $name ] ) );
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
		return (
			(
				isset( $this->_registry['singleton'][ $name ] )
				&& is_object( $this->_registry['singleton'][ $name ] )
			)
			||
			(
				! empty( $this->_registry['factory'][ $name ] )
				&& is_string( $this->_registry['factory'][ $name ] )
			)
		);
	}

	/**
	 * Method to register a driver
	 *
	 * @param string $class
	 * @param bool   $is_factory
	 *
	 * @return void
	 *
	 * @throws \ErrorException
	 */
	public function register_driver( string $class, bool $is_factory = false ) : void {

		if ( ! method_exists( $class, 'get_driver_name' ) ) {
			throw new ErrorException(
				sprintf(
					/* translators: Placeholders for class and method names */
					__( 'Class "%1$s" must define the static method "%2$s" to be able to get registered as a driver of %3$s.', 'g3-utilities' ),
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
					/* translators: Placeholders are driver and class names */
					__( 'Driver name "%1$s" is reserved and cannot be used. Use another driver name for the class "%2$s"', 'g3-utilities' ),
					$name,
					$class
				)
			);
		}

		if ( $this->is_driver_registered( $name ) ) {
			throw new ErrorException(
				sprintf(
					/* translators: Placeholders are class, method & driver names */
					__( '%1$s::%2$s() - A driver by name of "%3$s" is already registered', 'g3-utilities' ),
					static::class,
					__FUNCTION__,
					$name
				)
			);
		}

		if ( ! method_exists( $class, 'get_instance' ) ) {
			throw new ErrorException(
				sprintf(
					/* translators: Placeholders are class and method names */
					__( '%1$s::%2$s() - "%4$s" is not a static method defined on "%3$3" class. All driver classes must define "%4$s" as a static method which returns the class object.', 'g3-utilities' ),
					static::class,
					__FUNCTION__,
					$class,
					'get_instance()'
				)
			);
		}

		if ( true === $is_factory ) {
			$this->_registry['factory'][ $name ] = $class;
		} else {
			$this->_registry['singleton'][ $name ] = $class::get_instance();
		}

	}

	/**
	 * Method to return driver instance
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return object
	 *
	 * @throws \ErrorException
	 */
	public function get_driver_instance( string $name, array $args = [] ) : object {

		if ( ! $this->is_driver_registered( $name ) ) {
			throw new ErrorException(
				sprintf(
					/* translators: Placeholders are driver and class names */
					__( '"%1$s" driver not registered with class %2$s', 'g3-utilities' ),
					$name,
					static::class
				)
			);
		}

		if ( $this->_is_factory_driver( $name ) ) {
			return $this->_registry['factory'][ $name ]::get_instance( ...$args );
		}

		return $this->_registry['singleton'][ $name ];

	}

	/**
	 * Magic method to return a driver object when driver name is accessed as a property of class object.
	 *
	 * @param string $name
	 *
	 * @return object
	 *
	 * @throws \ErrorException
	 */
	public function __get( string $name ) {

		if ( $this->is_driver_registered( $name ) && $this->_is_factory_driver( $name ) ) {
			throw new ErrorException(
				sprintf(
					/* translators: Placeholders are driver and class names */
					__( '"%1$s" driver not registered as a Singleton. Unable to access this driver as property of %2$s.', 'g3-utilities' ),
					$name,
					static::class
				)
			);
		}

		return $this->get_driver_instance( $name );

	}

	/**
	 * Magic method which maps a static method call to a registered driver if it exists.
	 *
	 * @param string $name
	 * @param array  $args
	 *
	 * @return object
	 *
	 * @throws \ErrorException
	 */
	public static function __callStatic( string $name, array $args ) {
		return static::get_instance()->get_driver_instance( $name, $args );
	}

}

//EOF
